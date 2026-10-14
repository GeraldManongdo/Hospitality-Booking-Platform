<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../back-end/config/database.php';
$propertyId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$propertyId) {
    http_response_code(400);
    exit('A valid property ID is required.');
}
$database = getDatabaseConnection();
$propertyQuery = $database->prepare(
    'SELECT properties.*,destinations.name AS destination_name,destinations.country AS destination_country,
            (SELECT property_images.image_url FROM property_images
             WHERE property_images.property_id=properties.id
             ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1) AS image_url,
            (SELECT AVG(reviews.rating) FROM reviews
             WHERE reviews.property_id=properties.id AND reviews.status=\'published\') AS rating,
            (SELECT COUNT(*) FROM reviews
             WHERE reviews.property_id=properties.id AND reviews.status=\'published\') AS review_count
     FROM properties INNER JOIN destinations ON destinations.id=properties.destination_id
     WHERE properties.id=:id AND properties.status=\'published\' AND destinations.status=\'published\''
);
$propertyQuery->execute(['id' => $propertyId]);
$property = $propertyQuery->fetch();
if (!$property) {
    http_response_code(404);
    exit('This property is not currently available.');
}
$roomsQuery = $database->prepare(
    'SELECT room_types.*,
            (SELECT MIN(room_inventory.nightly_rate) FROM room_inventory
             WHERE room_inventory.room_type_id=room_types.id AND room_inventory.inventory_date>=CURRENT_DATE) AS inventory_rate
     FROM room_types WHERE room_types.property_id=:property_id AND room_types.status=\'active\'
     ORDER BY room_types.nightly_rate'
);
$roomsQuery->execute(['property_id' => $propertyId]);
$rooms = $roomsQuery->fetchAll();
$reviewsQuery = $database->prepare(
    'SELECT reviews.rating,reviews.title,reviews.body,reviews.created_at,
            users.first_name,users.last_name
     FROM reviews LEFT JOIN users ON users.id=reviews.guest_user_id
     WHERE reviews.property_id=:property_id AND reviews.status=\'published\'
     ORDER BY reviews.created_at DESC LIMIT 6'
);
$reviewsQuery->execute(['property_id' => $propertyId]);
$reviews = $reviewsQuery->fetchAll();
$imageUrl = $property['image_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=85';
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = $property['name'];
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">'
    . '<style>#public-property-map{height:320px;border-radius:1rem}</style>';
if ($property['latitude'] !== null && $property['longitude'] !== null) {
    $extraScripts = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>'
        . '<script>const map=L.map("public-property-map").setView([' . json_encode((float) $property['latitude']) . ',' . json_encode((float) $property['longitude']) . '],14);'
        . 'L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:19,attribution:"&copy; OpenStreetMap contributors"}).addTo(map);'
        . 'L.marker(map.getCenter()).addTo(map).bindPopup(' . json_encode($property['name'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ');</script>';
}
require __DIR__ . '/../../components/public-header.php';
?>
<main><section class="container py-4">
    <nav aria-label="Breadcrumb" class="small mb-3"><a href="properties.php">Stays</a><span class="text-secondary mx-2">/</span><a href="properties.php?destination_id=<?= (int) $property['destination_id'] ?>"><?= $escape($property['destination_name']) ?></a><span class="text-secondary mx-2">/</span><?= $escape($property['name']) ?></nav>
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3"><div><h1 class="h2 fw-bold mb-1"><?= $escape($property['name']) ?></h1><p class="text-secondary mb-0"><i class="bi bi-geo-alt me-1"></i><?= $escape($property['city'] . ', ' . $property['country']) ?><?php if ($property['rating'] !== null): ?><span class="mx-2">·</span><i class="bi bi-star-fill text-warning me-1"></i><?= number_format((float) $property['rating'],1) ?> <span class="text-secondary">(<?= (int) $property['review_count'] ?> reviews)</span><?php endif; ?></p></div><button class="btn btn-outline-primary" type="button" data-wishlist="<?= (int) $property['id'] ?>"><i class="bi bi-heart me-1"></i><span data-wishlist-label>Save</span></button></div>
    <div class="property-gallery mb-4"><img src="<?= $escape($imageUrl) ?>" alt="<?= $escape($property['name']) ?>"><img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80" alt="Guest room"><img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80" alt="Stay interior"><img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80" alt="Outdoor pool"><img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80" alt="Dining area"></div>
    <div class="row g-5"><section class="col-lg-7"><div class="d-flex align-items-center gap-3 pb-4 border-bottom"><div class="feature-icon"><i class="bi bi-house-heart"></i></div><div><h2 class="h5 fw-bold mb-1"><?= $escape($property['property_type']) ?></h2><p class="text-secondary mb-0"><?= $escape($property['destination_name'] . ', ' . $property['destination_country']) ?></p></div></div><div class="py-4 border-bottom"><h2 class="h5 fw-bold">About this stay</h2><p class="text-secondary mb-0"><?= nl2br($escape($property['description'])) ?></p></div>
    <div class="py-4 border-bottom"><h2 class="h5 fw-bold mb-3">Available rooms</h2><?php foreach ($rooms as $room): ?><article class="booking-card mb-3"><div class="d-flex flex-wrap justify-content-between gap-3 align-items-center"><div><h3 class="h6 fw-bold mb-1"><?= $escape($room['name']) ?></h3><p class="small text-secondary mb-1"><?= (int) $room['max_guests'] ?> guests · <?= (int) $room['total_units'] ?> units</p><?php if ($room['description']): ?><p class="small text-secondary mb-0"><?= $escape($room['description']) ?></p><?php endif; ?></div><div class="text-end"><strong><?= $escape($room['currency']) ?> <?= number_format((float) ($room['inventory_rate'] ?? $room['nightly_rate']),2) ?></strong><span class="small text-secondary d-block">per night</span><a class="btn btn-sm btn-primary mt-2" href="room-details.php?id=<?= (int) $room['id'] ?>">View room</a></div></div></article><?php endforeach; ?><?php if (!$rooms): ?><p class="text-secondary">No rooms are available at this property right now.</p><?php endif; ?></div>
    <?php if ($reviews): ?><div class="py-4"><h2 class="h5 fw-bold mb-3">Guest reviews</h2><?php foreach ($reviews as $review): ?><article class="border-bottom py-3"><div class="d-flex justify-content-between"><strong><?= $escape(trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?: 'Guest') ?></strong><span><i class="bi bi-star-fill text-warning me-1"></i><?= number_format((float) $review['rating'],1) ?></span></div><?php if ($review['title']): ?><h3 class="h6 mt-2 mb-1"><?= $escape($review['title']) ?></h3><?php endif; ?><p class="small text-secondary mb-0"><?= $escape($review['body']) ?></p></article><?php endforeach; ?></div><?php endif; ?>
    </section><aside class="col-lg-5"><?php if ($property['latitude'] !== null && $property['longitude'] !== null): ?><div class="booking-card mb-4"><h2 class="h5 fw-bold">Location</h2><div id="public-property-map"></div></div><?php endif; ?><div class="booking-card"><div class="d-flex justify-content-between align-items-baseline"><span class="h4 fw-bold mb-0"><?= $escape($property['currency']) ?> <?= number_format((float) $property['base_nightly_rate'],2) ?></span><span class="text-secondary">starting / night</span></div><p class="small text-secondary mt-3 mb-0">Choose an available room to see its details and start a reservation.</p></div></aside></div>
</section></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
