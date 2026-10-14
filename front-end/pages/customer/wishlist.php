<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../../../back-end/config/database.php';
$database = getDatabaseConnection();
$wishlistFilter = isset($_SESSION['user']['id'])
    ? ' AND EXISTS (SELECT 1 FROM wishlists WHERE wishlists.user_id=:user_id AND wishlists.property_id=properties.id)'
    : '';
$query = $database->prepare(
    'SELECT properties.id,properties.name,properties.city,properties.country,properties.property_type,
            properties.description,properties.base_nightly_rate,properties.currency,
            destinations.name AS destination_name,
            (SELECT property_images.image_url FROM property_images WHERE property_images.property_id=properties.id
             ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1) AS image_url,
            (SELECT AVG(reviews.rating) FROM reviews WHERE reviews.property_id=properties.id AND reviews.status=\'published\') AS rating,
            (SELECT COUNT(*) FROM reviews WHERE reviews.property_id=properties.id AND reviews.status=\'published\') AS review_count
     FROM properties INNER JOIN destinations ON destinations.id=properties.destination_id
     WHERE properties.status=\'published\' AND destinations.status=\'published\'
       AND EXISTS (SELECT 1 FROM room_types WHERE room_types.property_id=properties.id AND room_types.status=\'active\')'
     . $wishlistFilter .
    ' ORDER BY properties.created_at DESC'
);
$query->execute(isset($_SESSION['user']['id']) ? ['user_id' => (int) $_SESSION['user']['id']] : []);
$properties = $query->fetchAll();
$signedIn = isset($_SESSION['user']['id']);
$basePath='../';$publicPath='../public/';$accountPath='./';$pageTitle='Saved stays';$accountActive='wishlist.php';
$propertyBasePath='../public/';
require __DIR__.'/../../components/public-header.php';
?>
<main class="container py-4"><div class="mb-4"><span class="eyebrow">YOUR SPACE</span><h1 class="h2 fw-bold mt-2">Saved stays</h1><p class="text-secondary">Keep the places you’re dreaming about close by.</p></div><div class="row g-4"><?php require __DIR__.'/../../components/public-account-nav.php'; ?><section class="col-lg-9"><?php if (!$signedIn): ?><div class="data-note mb-3">Guest saves stay in this browser; sign in to synchronize them across devices.</div><?php else: ?><div class="data-note mb-3">Saved stays are stored in your account.</div><?php endif; ?><div class="row g-4" id="wishlist-grid"><?php foreach($properties as $property) { require __DIR__.'/../../components/property-card.php'; } ?></div><div id="wishlist-empty" class="booking-card text-center py-5 <?= $properties ? 'd-none' : '' ?>"><i class="bi bi-heart fs-1 text-teal"></i><h2 class="h5 fw-bold mt-3">Your wishlist is waiting</h2><p class="text-secondary">Save a stay you love and it’ll show up here.</p><a href="../public/properties.php" class="btn btn-outline-primary">Discover stays</a></div></section></div></main>
<script src="../../assets/js/wishlist.js"></script>
<?php require __DIR__.'/../../components/public-footer.php'; ?>
