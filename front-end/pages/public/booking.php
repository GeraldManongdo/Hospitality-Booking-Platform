<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../../../back-end/config/database.php';
$roomId = filter_var($_GET['room_type_id'] ?? null, FILTER_VALIDATE_INT);
if (!$roomId) {
    http_response_code(400);
    exit('Choose a room before starting a booking.');
}
$database = getDatabaseConnection();
$query = $database->prepare(
    'SELECT room_types.*,properties.id AS property_id,properties.name AS property_name,
            properties.city,properties.country,properties.status AS property_status,
            properties.currency AS property_currency,
            (SELECT property_images.image_url FROM property_images
             WHERE property_images.property_id=properties.id
             ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1) AS image_url
     FROM room_types INNER JOIN properties ON properties.id=room_types.property_id
     INNER JOIN destinations ON destinations.id=properties.destination_id
     WHERE room_types.id=:id AND room_types.status=\'active\'
       AND properties.status=\'published\' AND destinations.status=\'published\''
);
$query->execute(['id' => $roomId]);
$room = $query->fetch();
if (!$room) {
    http_response_code(404);
    exit('This room is not currently available.');
}
$user = $_SESSION['user'] ?? null;
$isSignedIn = isset($user['id']);
if (!$isSignedIn) {
    $_SESSION['booking_room_after_login'] = (int) $room['id'];
}
$guestFirstName = $user['first_name'] ?? '';
$guestLastName = $user['last_name'] ?? '';
$guestEmail = $user['email'] ?? '';
$guestPhone = '';
if ($user) {
    $guestQuery = $database->prepare('SELECT first_name,last_name,email,phone FROM users WHERE id=:id');
    $guestQuery->execute(['id' => (int) $user['id']]);
    $guest = $guestQuery->fetch();
    if (!$guest) {
        http_response_code(401);
        exit('Your account could not be verified. Please sign in again.');
    }
    $guestFirstName = $guest['first_name'];
    $guestLastName = $guest['last_name'];
    $guestEmail = $guest['email'];
    $guestPhone = $guest['phone'] ?? '';
}
$feedback = $_SESSION['booking_feedback'] ?? null;
unset($_SESSION['booking_feedback']);
$csrfToken = $_SESSION['booking_csrf_token'] ??= bin2hex(random_bytes(32));
$checkInValue = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['checkin'] ?? '')) ? (string) $_GET['checkin'] : '';
$checkOutValue = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['checkout'] ?? '')) ? (string) $_GET['checkout'] : '';
$imageUrl = $room['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80';
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Book ' . $room['name'];
require __DIR__ . '/../../components/public-header.php';
?>
<main class="container py-4"><p class="small"><a href="room-details.php?id=<?= (int) $room['id'] ?>">Back to room details</a></p><h1 class="h2 fw-bold mt-2">Book your stay</h1><p class="text-secondary"><?= $escape($room['property_name']) ?> · <?= $escape($room['name']) ?></p><?php if ($feedback): ?><div class="alert alert-danger" role="alert"><?= $escape((string) $feedback) ?></div><?php endif; ?><?php if (!$isSignedIn): ?><section class="booking-card my-4"><h2 class="h5 fw-bold">Sign in to reserve this room</h2><p class="text-secondary">Your reservation will be linked to your account so you can track it in My Trips.</p><a class="btn btn-primary" href="login.php">Sign in and continue</a><a class="btn btn-outline-primary ms-2" href="register.php">Create an account</a></section><?php else: ?><div class="row g-4"><section class="col-lg-7"><form action="../../../back-end/controllers/create-booking.php" method="post" class="booking-card"><input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>"><input type="hidden" name="room_type_id" value="<?= (int) $room['id'] ?>"><h2 class="h5 fw-bold mb-3">Guest details</h2><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="first-name">First name</label><input class="form-control" id="first-name" name="first_name" maxlength="100" required value="<?= $escape($guestFirstName) ?>" readonly></div><div class="col-sm-6"><label class="form-label" for="last-name">Last name</label><input class="form-control" id="last-name" name="last_name" maxlength="100" required value="<?= $escape($guestLastName) ?>" readonly></div><div class="col-sm-8"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" type="email" name="email" maxlength="254" required value="<?= $escape($guestEmail) ?>" readonly></div><div class="col-sm-4"><label class="form-label" for="phone">Phone (optional)</label><input class="form-control" id="phone" name="phone" maxlength="32" value="<?= $escape($guestPhone) ?>" readonly></div><div class="col-sm-6"><label class="form-label" for="check-in">Check-in</label><input class="form-control" id="check-in" type="date" name="check_in" min="<?= date('Y-m-d') ?>" required value="<?= $escape($checkInValue) ?>"></div><div class="col-sm-6"><label class="form-label" for="check-out">Check-out</label><input class="form-control" id="check-out" type="date" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required value="<?= $escape($checkOutValue) ?>"></div><div class="col-sm-6"><label class="form-label" for="guest-count">Guests</label><input class="form-control" id="guest-count" type="number" name="guest_count" min="1" max="<?= (int) $room['max_guests'] ?>" value="1" required></div><div class="col-sm-6"><label class="form-label" for="room-count">Rooms</label><select class="form-select" id="room-count" name="room_count"><?php for ($count = 1; $count <= min((int) $room['total_units'], 5); $count++): ?><option value="<?= $count ?>"><?= $count ?> <?= $count === 1 ? 'room' : 'rooms' ?></option><?php endfor; ?></select></div><div class="col-12"><label class="form-label" for="special-requests">Special requests (optional)</label><textarea class="form-control" id="special-requests" name="special_requests" maxlength="2000" rows="3"></textarea></div></div><button class="btn btn-primary btn-lg w-100 mt-4" type="submit">Place reservation request <i class="bi bi-arrow-right ms-2"></i></button><p class="small text-secondary mt-3 mb-0">Your request will be saved as pending. No payment is taken online.</p></form></section><aside class="col-lg-5"><div class="booking-card sticky-lg-top" style="top:1.5rem"><img class="rounded-3 w-100 mb-3" style="height:190px;object-fit:cover" src="<?= $escape($imageUrl) ?>" alt="<?= $escape($room['name']) ?>"><h2 class="h5 fw-bold"><?= $escape($room['property_name']) ?></h2><p class="small text-secondary"><?= $escape($room['name']) ?> · <?= (int) $room['max_guests'] ?> guests per room</p><div class="d-flex justify-content-between"><span>Rate per night</span><strong><?= $escape($room['currency']) ?> <?= number_format((float) $room['nightly_rate'], 2) ?></strong></div><p class="small text-secondary mt-3 mb-0">The final total is calculated from the nightly prices available for your selected dates.</p></div></aside></div><?php endif; ?></main>
<?php if ($isSignedIn): ?>
<script>
(() => {
    const roomCount = document.getElementById('room-count');
    const guestCount = document.getElementById('guest-count');
    const guestsPerRoom = <?= (int) $room['max_guests'] ?>;
    const updateGuestLimit = () => {
        const maxGuests = guestsPerRoom * Number(roomCount.value);
        guestCount.max = String(maxGuests);
        if (Number(guestCount.value) > maxGuests) {
            guestCount.value = String(maxGuests);
        }
    };
    roomCount.addEventListener('change', updateGuestLimit);
    updateGuestLimit();
})();
</script>
<?php endif; ?>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
