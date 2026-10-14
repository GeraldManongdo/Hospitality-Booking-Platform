<?php
declare(strict_types=1);

require __DIR__ . '/../config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/admin/manage-bookings.php', true, 303);
    exit;
}

if (
    empty($_SESSION['admin_resource_csrf_token'])
    || !hash_equals($_SESSION['admin_resource_csrf_token'], (string) ($_POST['csrf_token'] ?? ''))
) {
    adminBookingRedirect('Your session expired. Please try again.', true);
}

$database = getDatabaseConnection();
$action = (string) ($_POST['action'] ?? '');
$id = filter_var($_POST['record_id'] ?? null, FILTER_VALIDATE_INT);

try {
    if ($action === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (!$id || !in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
            adminBookingRedirect('Choose a valid booking and status.', true);
        }
        if (in_array($status, ['pending', 'confirmed'], true)) {
            validateBookingCapacity($database, $id);
        }
        $update = $database->prepare('UPDATE bookings SET status=:status WHERE id=:id');
        $update->execute(['status' => $status, 'id' => $id]);
        adminBookingRedirect('Booking status updated.');
    }

    if ($action !== 'save' || !$id) {
        adminBookingRedirect('Bookings must be selected before they can be edited.', true);
    }

    $firstName = trim((string) ($_POST['guest_first_name'] ?? ''));
    $lastName = trim((string) ($_POST['guest_last_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['guest_email'] ?? '')));
    $phone = trim((string) ($_POST['guest_phone'] ?? ''));
    $propertyId = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);
    $roomTypeId = filter_var($_POST['room_type_id'] ?? null, FILTER_VALIDATE_INT);
    $checkIn = (string) ($_POST['check_in'] ?? '');
    $checkOut = (string) ($_POST['check_out'] ?? '');
    $guestCount = filter_var($_POST['guest_count'] ?? null, FILTER_VALIDATE_INT);
    $roomCount = filter_var($_POST['room_count'] ?? null, FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');

    if (
        $firstName === '' || mb_strlen($firstName) > 100
        || $lastName === '' || mb_strlen($lastName) > 100
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254
        || strlen($phone) > 32
        || !$propertyId || !$roomTypeId
        || !adminBookingDate($checkIn) || !adminBookingDate($checkOut) || $checkOut <= $checkIn
        || $guestCount === false || $guestCount < 1 || $guestCount > 255
        || $roomCount === false || $roomCount < 1 || $roomCount > 255
        || !in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)
    ) {
        adminBookingRedirect('Check guest details, property, room, dates, counts, and status.', true);
    }

    $roomQuery = $database->prepare(
        'SELECT id,property_id,max_guests,total_units,nightly_rate,currency,status
         FROM room_types WHERE id=:room AND property_id=:property'
    );
    $roomQuery->execute(['room' => $roomTypeId, 'property' => $propertyId]);
    $room = $roomQuery->fetch();
    $currentRoomQuery = $database->prepare('SELECT room_type_id FROM bookings WHERE id=:id');
    $currentRoomQuery->execute(['id' => $id]);
    $currentRoomTypeId = $currentRoomQuery->fetchColumn();
    if (
        !$room
        || (!in_array($room['status'], ['active', 'in_review'], true)
            && (int) $currentRoomTypeId !== $roomTypeId)
    ) {
        adminBookingRedirect('Choose an available room type that belongs to the selected property.', true);
    }
    if ($guestCount > (int) $room['max_guests'] * $roomCount) {
        adminBookingRedirect('Guest count exceeds the selected rooms’ combined capacity.', true);
    }

    $database->beginTransaction();
    $bookingQuery = $database->prepare(
        'SELECT nightly_rate,discount_amount,tax_amount,service_fee_amount
         FROM bookings WHERE id=:id FOR UPDATE'
    );
    $bookingQuery->execute(['id' => $id]);
    $booking = $bookingQuery->fetch();
    if (!$booking) {
        $database->rollBack();
        adminBookingRedirect('The booking no longer exists.', true);
    }

    if (in_array($status, ['pending', 'confirmed'], true)) {
        validateBookingCapacity($database, $id, $roomTypeId, $checkIn, $checkOut, $roomCount, (int) $room['total_units']);
    }

    $nights = (new DateTimeImmutable($checkIn))->diff(new DateTimeImmutable($checkOut))->days;
    if ($nights < 1 || $nights > 365) {
        $database->rollBack();
        adminBookingRedirect('A reservation must be between 1 and 365 nights.', true);
    }
    $nightlyRate = (float) $room['nightly_rate'];
    $subtotal = round($nightlyRate * $nights * $roomCount, 2);
    $discount = (float) $booking['discount_amount'];
    $tax = (float) $booking['tax_amount'];
    $fee = (float) $booking['service_fee_amount'];
    $total = max(0, round($subtotal - $discount + $tax + $fee, 2));
    $update = $database->prepare(
        'UPDATE bookings SET property_id=:property_id,room_type_id=:room_type_id,
         guest_first_name=:first_name,guest_last_name=:last_name,guest_email=:email,
         guest_phone=:phone,check_in=:check_in,check_out=:check_out,guest_count=:guest_count,
         room_count=:room_count,nightly_rate=:nightly_rate,currency=:currency,subtotal=:subtotal,
         total_amount=:total,status=:status WHERE id=:id'
    );
    $update->execute([
        'property_id' => $propertyId,
        'room_type_id' => $roomTypeId,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guest_count' => $guestCount,
        'room_count' => $roomCount,
        'nightly_rate' => $nightlyRate,
        'currency' => $room['currency'],
        'subtotal' => $subtotal,
        'total' => $total,
        'status' => $status,
        'id' => $id,
    ]);
    $database->commit();
    adminBookingRedirect('Booking updated with the selected room type.');
} catch (PDOException $exception) {
    if ($database->inTransaction()) {
        $database->rollBack();
    }
    error_log('Admin booking controller database error: ' . $exception->getMessage());
    adminBookingRedirect('Booking changes could not be saved.', true);
} catch (RuntimeException $exception) {
    if ($database->inTransaction()) {
        $database->rollBack();
    }
    adminBookingRedirect($exception->getMessage(), true);
}

function adminBookingDate(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

function validateBookingCapacity(
    PDO $database,
    ?int $ignoreId,
    ?int $roomTypeId = null,
    ?string $checkIn = null,
    ?string $checkOut = null,
    ?int $roomCount = null,
    ?int $totalUnits = null
): void {
    if ($ignoreId && $roomTypeId === null) {
        $booking = $database->prepare(
            'SELECT room_type_id,check_in,check_out,room_count
             FROM bookings WHERE id=:id'
        );
        $booking->execute(['id' => $ignoreId]);
        $current = $booking->fetch();
        if (!$current || $current['room_type_id'] === null) {
            throw new RuntimeException('This booking has no room type assigned.');
        }
        $roomTypeId = (int) $current['room_type_id'];
        $checkIn = $current['check_in'];
        $checkOut = $current['check_out'];
        $roomCount = (int) $current['room_count'];
    }

    if ($roomTypeId === null || $checkIn === null || $checkOut === null || $roomCount === null) {
        return;
    }
    if ($totalUnits === null) {
        $roomQuery = $database->prepare('SELECT total_units FROM room_types WHERE id=:id');
        $roomQuery->execute(['id' => $roomTypeId]);
        $totalUnits = (int) $roomQuery->fetchColumn();
    }

    $firstDay = new DateTimeImmutable($checkIn);
    $lastDay = new DateTimeImmutable($checkOut);
    if ($lastDay->diff($firstDay)->days > 365) {
        throw new RuntimeException('The requested booking is longer than 365 nights.');
    }
    $inventory = $database->prepare(
        'SELECT available_units FROM room_inventory
         WHERE room_type_id=:room AND inventory_date=:date'
    );
    $occupied = $database->prepare(
        'SELECT COALESCE(SUM(room_count),0) FROM bookings
         WHERE room_type_id=:room AND id<>:id AND status IN (\'pending\',\'confirmed\')
         AND check_in<=:date AND check_out>:date'
    );
    for ($day = $firstDay; $day < $lastDay; $day = $day->modify('+1 day')) {
        $date = $day->format('Y-m-d');
        $inventory->execute(['room' => $roomTypeId, 'date' => $date]);
        $available = $inventory->fetchColumn();
        $capacity = $available === false ? $totalUnits : (int) $available;
        $occupied->execute(['room' => $roomTypeId, 'id' => $ignoreId ?? 0, 'date' => $date]);
        if ((int) $occupied->fetchColumn() + $roomCount > $capacity) {
            throw new RuntimeException('The selected room type does not have enough availability for all requested dates.');
        }
    }
}

function adminBookingRedirect(string $message, bool $error = false): never
{
    $_SESSION['admin_resource_feedback'] = ['type' => $error ? 'error' : 'success', 'message' => $message];
    header('Location: ../../front-end/pages/admin/manage-bookings.php', true, 303);
    exit;
}
