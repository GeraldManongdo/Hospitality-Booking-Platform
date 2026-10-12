<?php
$basePath='../';$pageTitle='Saved stays';$accountActive='wishlist.php';
$propertyBasePath='../';
require __DIR__.'/../includes/customer-header.php';
$properties=require __DIR__.'/../includes/mock-properties.php';
?>
<main class="container py-4"><div class="mb-4"><span class="eyebrow">YOUR SPACE</span><h1 class="h2 fw-bold mt-2">Saved stays</h1><p class="text-secondary">Keep the places you’re dreaming about close by.</p></div><div class="row g-4"><?php require __DIR__.'/../includes/customer-account-nav.php'; ?><section class="col-lg-9"><div class="data-note mb-3">Wishlist changes are stored in this browser only.</div><div class="row g-4" id="wishlist-grid"><?php foreach($properties as $property) { require __DIR__.'/../components/property-card.php'; } ?></div><div id="wishlist-empty" class="booking-card text-center py-5 d-none"><i class="bi bi-heart fs-1 text-teal"></i><h2 class="h5 fw-bold mt-3">Your wishlist is waiting</h2><p class="text-secondary">Save a stay you love and it’ll show up here.</p><a href="../properties.php" class="btn btn-outline-primary">Discover stays</a></div></section></div></main>
<script src="../assets/js/wishlist.js"></script>
<?php require __DIR__.'/../includes/customer-footer.php'; ?>
