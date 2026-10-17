<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$bookingId = filter_var($_GET['booking_id'] ?? $_SESSION['last_booking_id'] ?? null, FILTER_VALIDATE_INT);
if (!$bookingId) {
    http_response_code(404);
    exit('No booking confirmation is available for this session.');
}
require_once __DIR__ . '/../../../back-end/config/database.php';
$database = getDatabaseConnection();
$query = $database->prepare(
    'SELECT bookings.*,properties.name AS property_name,room_types.name AS room_name
     FROM bookings INNER JOIN properties ON properties.id=bookings.property_id
     LEFT JOIN room_types ON room_types.id=bookings.room_type_id
     WHERE bookings.id=:id'
);
$query->execute(['id' => $bookingId]);
$booking = $query->fetch();
if (!$booking) {
    http_response_code(404);
    exit('The booking record could not be found.');
}
$isBookingGuest = isset($_SESSION['user']['id'])
    && (int) $_SESSION['user']['id'] === (int) $booking['guest_user_id'];
if (!$isBookingGuest && !hash_equals((string) ($_SESSION['last_booking_reference'] ?? ''), (string) $booking['reference'])) {
    http_response_code(403);
    exit('You are not allowed to view this booking.');
}
$paymentQuery = $database->prepare(
    'SELECT status,provider_checkout_url FROM payments
     WHERE booking_id=:booking_id AND payment_type=\'charge\'
       AND provider=\'PayMongo QRPH\' ORDER BY id DESC LIMIT 1'
);
$paymentQuery->execute(['booking_id' => $bookingId]);
$payment = $paymentQuery->fetch();
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Booking confirmation';
require __DIR__ . '/../../components/public-header.php';
?>
<main class="container py-5"><div class="row justify-content-center"><div class="col-lg-8"><div class="booking-card text-center p-4 p-md-5"><div class="feature-icon mx-auto mb-3"><i class="bi bi-check2-circle"></i></div><span class="eyebrow">BOOKING REQUEST</span><h1 class="h2 fw-bold mt-2"><?= $booking['status'] === 'confirmed' ? 'Your stay is confirmed.' : ($booking['status'] === 'cancelled' ? 'This booking was cancelled.' : 'Your stay is awaiting payment.') ?></h1><p class="text-secondary"><?= $payment && $payment['status'] === 'completed' ? 'Your QR PH payment has been verified.' : (($payment['status'] ?? '') === 'pending' ? 'Complete QR PH payment to confirm your reservation. If you just paid, allow a moment for PayMongo to verify it.' : 'Payment status updates after PayMongo verifies it.') ?></p><div class="alert alert-light border text-start my-4"><div><strong><?= $escape($booking['property_name']) ?> · <?= $escape($booking['room_name'] ?? 'Room') ?></strong></div><div class="small mt-2"><?= $escape($booking['check_in']) ?> to <?= $escape($booking['check_out']) ?> · <?= (int) $booking['guest_count'] ?> guests · <?= (int) $booking['room_count'] ?> rooms</div><div class="small mt-2">Reference <code><?= $escape($booking['reference']) ?></code> · <?= ucfirst($escape($booking['status'])) ?></div><div class="d-flex justify-content-between fw-bold mt-3"><span>Total</span><span><?= $escape($booking['currency']) ?> <?= number_format((float) $booking['total_amount'], 2) ?></span></div><?php if ($payment): ?><div class="small mt-2">Payment status: <?= ucfirst($escape($payment['status'])) ?></div><?php endif; ?></div><div class="d-flex flex-wrap justify-content-center gap-2"><?php if ($payment && $payment['status'] === 'pending' && $payment['provider_checkout_url']): ?><a class="btn btn-primary" href="<?= $escape($payment['provider_checkout_url']) ?>" rel="noopener">Continue to QR PH payment</a><?php endif; ?><?php if ($isBookingGuest): ?><a class="btn btn-primary" href="../customer/bookings.php">View my trips</a><?php endif; ?><a class="btn btn-outline-primary" href="properties.php">Explore more stays</a></div></div></div></div></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
