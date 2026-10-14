<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$bookingPage = '../../front-end/pages/public/booking.php';
$redirectWithFeedback = static function (string $message, int $roomId) use ($bookingPage): never {
    $_SESSION['booking_feedback'] = $message;
    header('Location: ' . $bookingPage . '?room_type_id=' . $roomId, true, 303);
    exit;
};

$roomId = filter_var($_POST['room_type_id'] ?? null, FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$roomId || $roomId < 1) {
    http_response_code(400);
    exit('A valid room type is required.');
}
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['booking_room_after_login'] = $roomId;
    header('Location: ../../front-end/pages/public/login.php', true, 303);
    exit;
}
$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['booking_csrf_token'] ?? '');
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    $redirectWithFeedback('Your session expired. Please review your booking and try again.', $roomId);
}

$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$phone = trim((string) ($_POST['phone'] ?? ''));
$specialRequests = trim((string) ($_POST['special_requests'] ?? ''));
$checkIn = (string) ($_POST['check_in'] ?? '');
$checkOut = (string) ($_POST['check_out'] ?? '');
$guestCount = filter_var($_POST['guest_count'] ?? null, FILTER_VALIDATE_INT);
$roomCount = filter_var($_POST['room_count'] ?? null, FILTER_VALIDATE_INT);
$dateFrom = DateTimeImmutable::createFromFormat('!Y-m-d', $checkIn);
$dateTo = DateTimeImmutable::createFromFormat('!Y-m-d', $checkOut);
$dateErrors = DateTimeImmutable::getLastErrors();
$datesValid = $dateFrom !== false && $dateTo !== false
    && $dateFrom->format('Y-m-d') === $checkIn
    && $dateTo->format('Y-m-d') === $checkOut
    && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0));
$today = new DateTimeImmutable('today');
if (
    $firstName === '' || strlen($firstName) > 100
    || $lastName === '' || strlen($lastName) > 100
    || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($phone) > 32 || strlen($specialRequests) > 2000
    || !$guestCount || $guestCount < 1 || $guestCount > 255
    || !$roomCount || $roomCount < 1 || $roomCount > 255
    || !$datesValid || $dateFrom < $today || $dateTo <= $dateFrom
    || $dateFrom->diff($dateTo)->days > 365
) {
    $redirectWithFeedback('Check your guest details, dates, and room selection. The stay must be between 1 and 365 nights.', $roomId);
}

require __DIR__ . '/../config/database.php';
try {
    $database = getDatabaseConnection();
    $database->beginTransaction();
    $roomQuery = $database->prepare(
        'SELECT room_types.id,room_types.property_id,room_types.name,room_types.max_guests,
                room_types.total_units,room_types.nightly_rate,room_types.currency,
                properties.status AS property_status,destinations.status AS destination_status
         FROM room_types INNER JOIN properties ON properties.id=room_types.property_id
         INNER JOIN destinations ON destinations.id=properties.destination_id
         WHERE room_types.id=:id AND room_types.status=\'active\'
         FOR UPDATE'
    );
    $roomQuery->execute(['id' => $roomId]);
    $room = $roomQuery->fetch();
    if (!$room || $room['property_status'] !== 'published' || $room['destination_status'] !== 'published') {
        throw new RuntimeException('This room is no longer available to book.');
    }
    if ($guestCount > (int) $room['max_guests'] * $roomCount || $roomCount > (int) $room['total_units']) {
        throw new RuntimeException('The selected number of guests or rooms exceeds this room type’s capacity.');
    }

    $guestUserId = null;
    if (isset($_SESSION['user']['id'])) {
        $userQuery = $database->prepare(
            'SELECT id,first_name,last_name,email,phone FROM users WHERE id=:id AND status IN (\'pending\',\'active\')'
        );
        $userQuery->execute(['id' => (int) $_SESSION['user']['id']]);
        $account = $userQuery->fetch();
        if (!$account) {
            throw new RuntimeException('Your account could not be verified. Please sign in again.');
        }
        $guestUserId = (int) $account['id'];
        $firstName = $account['first_name'];
        $lastName = $account['last_name'];
        $email = $account['email'];
        $phone = $account['phone'] ?? '';
    }

    $inventoryQuery = $database->prepare(
        'SELECT available_units,nightly_rate FROM room_inventory
         WHERE room_type_id=:inventory_room_id AND inventory_date=:inventory_date FOR UPDATE'
    );
    $bookedQuery = $database->prepare(
        'SELECT COALESCE(SUM(room_count),0) FROM bookings
         WHERE room_type_id=:booked_room_id AND status IN (\'pending\',\'confirmed\')
           AND check_in<=:booked_date_start AND check_out>:booked_date_end'
    );
    $nightCount = (int) $dateFrom->diff($dateTo)->days;
    $subtotal = 0.0;
    for ($night = 0; $night < $nightCount; $night++) {
        $date = $dateFrom->modify('+' . $night . ' days')->format('Y-m-d');
        $inventoryQuery->execute(['inventory_room_id' => $roomId, 'inventory_date' => $date]);
        $inventory = $inventoryQuery->fetch();
        $capacity = $inventory ? (int) $inventory['available_units'] : (int) $room['total_units'];
        $bookedQuery->execute([
            'booked_room_id' => $roomId,
            'booked_date_start' => $date,
            'booked_date_end' => $date,
        ]);
        $alreadyBooked = (int) $bookedQuery->fetchColumn();
        if ($capacity - $alreadyBooked < $roomCount) {
            throw new RuntimeException('There are not enough rooms available for every night you selected.');
        }
        $nightlyRate = $inventory && $inventory['nightly_rate'] !== null
            ? (float) $inventory['nightly_rate']
            : (float) $room['nightly_rate'];
        $subtotal += $nightlyRate * $roomCount;
    }
    $subtotal = round($subtotal, 2);
    $averageNightlyRate = round($subtotal / ($nightCount * $roomCount), 2);
    $reference = 'XYV-' . strtoupper(bin2hex(random_bytes(6)));
    $insert = $database->prepare(
        'INSERT INTO bookings
            (reference,guest_user_id,property_id,room_type_id,guest_first_name,guest_last_name,
             guest_email,guest_phone,check_in,check_out,guest_count,room_count,nightly_rate,
             subtotal,discount_amount,tax_amount,service_fee_amount,total_amount,currency,status,special_requests)
         VALUES
            (:reference,:guest_user_id,:property_id,:room_type_id,:first_name,:last_name,
             :email,:phone,:check_in,:check_out,:guest_count,:room_count,:nightly_rate,
             :subtotal,0,0,0,:total_amount,:currency,\'pending\',:special_requests)'
    );
    $insert->execute([
        'reference' => $reference,
        'guest_user_id' => $guestUserId,
        'property_id' => $room['property_id'],
        'room_type_id' => $roomId,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guest_count' => $guestCount,
        'room_count' => $roomCount,
        'nightly_rate' => $averageNightlyRate,
        'subtotal' => $subtotal,
        'total_amount' => $subtotal,
        'currency' => $room['currency'],
        'special_requests' => $specialRequests !== '' ? $specialRequests : null,
    ]);
    $bookingId = (int) $database->lastInsertId();
    $database->commit();

    unset($_SESSION['booking_csrf_token']);
    $_SESSION['last_booking_id'] = $bookingId;
    $_SESSION['last_booking_reference'] = $reference;
    header('Location: ../../front-end/pages/public/booking-confirmation.php', true, 303);
    exit;
} catch (RuntimeException $exception) {
    if (isset($database) && $database->inTransaction()) {
        $database->rollBack();
    }
    $redirectWithFeedback($exception->getMessage(), $roomId);
} catch (PDOException $exception) {
    if (isset($database) && $database->inTransaction()) {
        $database->rollBack();
    }
    error_log('Public booking database error: ' . $exception->getMessage());
    $redirectWithFeedback('We could not save your booking right now. Please try again later.', $roomId);
}
