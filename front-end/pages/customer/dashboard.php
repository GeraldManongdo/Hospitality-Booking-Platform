<?php
$basePath='../';$publicPath='../public/';$accountPath='./';$pageTitle='Your travel space';$activePage='';$accountActive='dashboard.php';
require __DIR__.'/../../components/public-header.php';
?>
<main class="container py-4"><div class="mb-4"><span class="eyebrow">YOUR SPACE</span><h1 class="h2 fw-bold mt-2">Your travel account</h1><p class="text-secondary">Manage your profile, saved stays, and reservations.</p></div><div class="row g-4"><?php require __DIR__.'/../../components/public-account-nav.php'; ?><section class="col-lg-9"><div class="booking-card"><h2 class="h5 fw-bold">Your reservations</h2><p class="text-secondary">Review the status and details of your reservations.</p><a href="bookings.php" class="btn btn-outline-primary">View my trips</a></div></section></div></main>
<?php require __DIR__.'/../../components/public-footer.php'; ?>
