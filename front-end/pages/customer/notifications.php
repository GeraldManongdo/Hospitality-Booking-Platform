<?php
$basePath='../';$publicPath='../public/';$accountPath='./';$pageTitle='Notifications';$accountActive='notifications.php';
require __DIR__.'/../../components/public-header.php';
?>
<main class="container py-4"><div class="mb-4"><span class="eyebrow">YOUR SPACE</span><h1 class="h2 fw-bold mt-2">Notifications</h1><p class="text-secondary">Updates about your account and reservations.</p></div><div class="row g-4"><?php require __DIR__.'/../../components/public-account-nav.php'; ?><section class="col-lg-9"><div class="booking-card"><p class="text-secondary mb-0">You have no new notifications.</p><a class="small" href="../public/destinations.php">Discover destinations <i class="bi bi-arrow-right"></i></a></div></section></div></main>
<?php require __DIR__.'/../../components/public-footer.php'; ?>
