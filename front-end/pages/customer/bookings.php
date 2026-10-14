<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../../../back-end/config/database.php';
$bookings = [];
if (isset($_SESSION['user']['id'])) {
    $database = getDatabaseConnection();
    $query = $database->prepare(
        'SELECT bookings.id,bookings.reference,bookings.check_in,bookings.check_out,
                bookings.guest_count,bookings.room_count,bookings.total_amount,bookings.currency,
                bookings.status,properties.name AS property_name,properties.city,properties.country
         FROM bookings INNER JOIN properties ON properties.id=bookings.property_id
         WHERE bookings.guest_user_id=:guest_id
            OR (bookings.guest_user_id IS NULL AND LOWER(bookings.guest_email)=:guest_email)
         ORDER BY bookings.created_at DESC'
    );
    $query->execute([
        'guest_id' => (int) $_SESSION['user']['id'],
        'guest_email' => strtolower((string) ($_SESSION['user']['email'] ?? '')),
    ]);
    $bookings = $query->fetchAll();
}
$publicPath = '../public/';
$accountPath = './';
$pageTitle = 'My trips';
$accountActive = 'bookings.php';
require __DIR__ . '/../../components/public-header.php';
?>
<main class="container py-4"><div class="mb-4"><span class="eyebrow">YOUR SPACE</span><h1 class="h2 fw-bold mt-2">My trips</h1><p class="text-secondary">Your reservation requests and stay history.</p></div><div class="row g-4"><?php require __DIR__ . '/../../components/public-account-nav.php'; ?><section class="col-lg-9"><?php if (!isset($_SESSION['user']['id'])): ?><div class="booking-card"><h2 class="h5 fw-bold">Sign in to view your trips</h2><p class="text-secondary">Reservations made while signed in are saved to your account.</p><a class="btn btn-primary" href="../public/login.php">Sign in</a></div><?php elseif (!$bookings): ?><div class="booking-card text-center py-5"><i class="bi bi-airplane fs-2 text-teal"></i><h2 class="h5 fw-bold mt-3">No trips yet</h2><p class="text-secondary">Your confirmed and pending reservations will appear here.</p><a class="btn btn-outline-primary" href="../public/properties.php">Find a stay</a></div><?php else: foreach ($bookings as $booking): ?><article class="booking-card mb-3"><div class="d-flex justify-content-between flex-wrap gap-2"><div><span class="status-pill status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($booking['status']), ENT_QUOTES, 'UTF-8') ?></span><h2 class="h5 fw-bold mt-2 mb-1"><?= htmlspecialchars($booking['property_name'], ENT_QUOTES, 'UTF-8') ?></h2><p class="small text-secondary mb-0"><?= htmlspecialchars($booking['city'] . ', ' . $booking['country'], ENT_QUOTES, 'UTF-8') ?></p></div><span class="small text-secondary"><?= htmlspecialchars($booking['check_in']) ?> – <?= htmlspecialchars($booking['check_out']) ?></span></div><hr><div class="d-flex justify-content-between align-items-center flex-wrap gap-2"><span class="small text-secondary"><?= (int) $booking['guest_count'] ?> guests · <?= (int) $booking['room_count'] ?> rooms · <?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?> · <?= htmlspecialchars($booking['reference'], ENT_QUOTES, 'UTF-8') ?></span><a href="booking-details.php?id=<?= (int) $booking['id'] ?>" class="btn btn-sm btn-outline-primary">View details</a></div></article><?php endforeach; endif; ?></section></div></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
