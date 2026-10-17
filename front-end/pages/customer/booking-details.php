<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$bookingId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$booking = null;
if ($bookingId && isset($_SESSION['user']['id'])) {
    require_once __DIR__ . '/../../../back-end/config/database.php';
    $database = getDatabaseConnection();
    $query = $database->prepare(
        'SELECT bookings.*,properties.name AS property_name,properties.city,properties.country,
                room_types.name AS room_name
         FROM bookings INNER JOIN properties ON properties.id=bookings.property_id
         LEFT JOIN room_types ON room_types.id=bookings.room_type_id
         WHERE bookings.id=:id AND (
             bookings.guest_user_id=:guest_id
             OR (bookings.guest_user_id IS NULL AND LOWER(bookings.guest_email)=:guest_email)
         )'
    );
    $query->execute([
        'id' => $bookingId,
        'guest_id' => (int) $_SESSION['user']['id'],
        'guest_email' => strtolower((string) ($_SESSION['user']['email'] ?? '')),
    ]);
    $booking = $query->fetch();
}
$publicPath = '../public/';
$accountPath = './';
$pageTitle = 'Booking details';
$accountActive = 'bookings.php';
$payment = null;
if ($booking) {
    $paymentQuery = $database->prepare(
        'SELECT status,provider_checkout_url FROM payments
         WHERE booking_id=:booking_id AND payment_type=\'charge\'
           AND provider=\'PayMongo QRPH\' ORDER BY id DESC LIMIT 1'
    );
    $paymentQuery->execute(['booking_id' => $bookingId]);
    $payment = $paymentQuery->fetch();
}
require __DIR__ . '/../../components/public-header.php';
?>
<main class="container py-4"><p><a href="bookings.php"><i class="bi bi-arrow-left me-1"></i>Back to my trips</a></p><div class="row g-4"><section class="col-lg-8"><?php if (!$booking): ?><div class="booking-card"><h1 class="h4 fw-bold">Booking not found</h1><p class="text-secondary">This booking could not be found in your account.</p></div><?php else: ?><span class="status-pill"><?= htmlspecialchars(ucfirst($booking['status']), ENT_QUOTES, 'UTF-8') ?></span><h1 class="h2 fw-bold mt-2">Booking details</h1><div class="booking-card"><h2 class="h5 fw-bold"><?= htmlspecialchars($booking['property_name'], ENT_QUOTES, 'UTF-8') ?></h2><p class="text-secondary"><?= htmlspecialchars($booking['room_name'] ?? 'Room', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($booking['city'] . ', ' . $booking['country'], ENT_QUOTES, 'UTF-8') ?></p><div class="row g-3"><div class="col-6"><span class="small text-secondary d-block">Check-in</span><strong><?= htmlspecialchars($booking['check_in'], ENT_QUOTES, 'UTF-8') ?></strong></div><div class="col-6"><span class="small text-secondary d-block">Check-out</span><strong><?= htmlspecialchars($booking['check_out'], ENT_QUOTES, 'UTF-8') ?></strong></div><div class="col-6"><span class="small text-secondary d-block">Guests</span><strong><?= (int) $booking['guest_count'] ?> guests</strong></div><div class="col-6"><span class="small text-secondary d-block">Rooms</span><strong><?= (int) $booking['room_count'] ?></strong></div><div class="col-6"><span class="small text-secondary d-block">Reference</span><strong><?= htmlspecialchars($booking['reference'], ENT_QUOTES, 'UTF-8') ?></strong></div><div class="col-6"><span class="small text-secondary d-block">Total</span><strong><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></strong></div></div><?php if ($payment): ?><hr><p class="small mb-2">QR PH payment: <strong><?= htmlspecialchars(ucfirst($payment['status']), ENT_QUOTES, 'UTF-8') ?></strong></p><?php if ($payment['status'] === 'pending' && $payment['provider_checkout_url']): ?><a class="btn btn-primary" href="<?= htmlspecialchars($payment['provider_checkout_url'], ENT_QUOTES, 'UTF-8') ?>" rel="noopener">Continue to payment</a><?php endif; ?><?php endif; ?></div><?php endif; ?></section><aside class="col-lg-4"><div class="booking-card"><h2 class="h6 fw-bold">Need a hand?</h2><p class="small text-secondary">Contact the property with your reservation reference if you need help.</p><a href="settings.php" class="btn btn-outline-primary w-100">Notification preferences</a></div></aside></div></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
