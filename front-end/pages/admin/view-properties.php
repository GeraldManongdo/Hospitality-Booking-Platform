<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

$propertyId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$propertyId) {
    http_response_code(400);
    exit('A valid property ID is required.');
}
$database = getDatabaseConnection();
$query = $database->prepare(
    'SELECT properties.*, destinations.name AS destination_name,
            destinations.country AS destination_country,
            CONCAT(users.first_name, " ", users.last_name) AS owner_name
     FROM properties
     INNER JOIN destinations ON destinations.id=properties.destination_id
     LEFT JOIN users ON users.id=properties.owner_user_id
     WHERE properties.id=:id'
);
$query->execute(['id' => $propertyId]);
$property = $query->fetch();
if (!$property) {
    http_response_code(404);
    exit('Property not found.');
}
$imageQuery = $database->prepare('SELECT image_url FROM property_images WHERE property_id=:id ORDER BY is_primary DESC,sort_order LIMIT 1');
$imageQuery->execute(['id' => $propertyId]);
$imageUrl = $imageQuery->fetchColumn();
$roomsQuery = $database->prepare(
    'SELECT room_types.*,
        (SELECT COUNT(*) FROM bookings WHERE bookings.room_type_id=room_types.id) AS booking_count
     FROM room_types WHERE property_id=:id ORDER BY name'
);
$roomsQuery->execute(['id' => $propertyId]);
$rooms = $roomsQuery->fetchAll();
$bookingsQuery = $database->prepare(
    'SELECT bookings.id,bookings.reference,bookings.guest_first_name,bookings.guest_last_name,
        bookings.check_in,bookings.check_out,bookings.status,bookings.room_count,
        room_types.name AS room_name
     FROM bookings LEFT JOIN room_types ON room_types.id=bookings.room_type_id
     WHERE bookings.property_id=:id ORDER BY bookings.created_at DESC LIMIT 50'
);
$bookingsQuery->execute(['id' => $propertyId]);
$bookings = $bookingsQuery->fetchAll();
$feedback = $_SESSION['admin_properties_feedback'] ?? null;
unset($_SESSION['admin_properties_feedback']);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Property details';
$activeSection = 'properties';
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">';
if ($property['latitude'] !== null && $property['longitude'] !== null) {
    $extraScripts = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>'
        . '<script>const coordinates=[' . json_encode((float) $property['latitude']) . ',' . json_encode((float) $property['longitude']) . '];'
        . 'const map=L.map("property-detail-map").setView(coordinates,14);'
        . 'L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:19,attribution:"&copy; OpenStreetMap contributors"}).addTo(map);'
        . 'L.marker(coordinates).addTo(map).bindPopup(' . json_encode($property['name'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ').openPopup();</script>';
}
require __DIR__ . '/../../components/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><a href="manage-properties.php" class="small"><i class="bi bi-arrow-left me-1"></i>Properties</a><span class="eyebrow d-block mt-3">PROPERTY DETAILS</span><h1 class="h2 fw-bold mt-2 mb-1"><?= $escape($property['name']) ?></h1><p class="text-secondary mb-0"><?= $escape($property['city'].', '.$property['country']) ?></p></div>
        <div class="d-flex gap-2"><a class="btn btn-primary" href="edit-properties.php?id=<?= (int)$propertyId ?>">Edit property</a><a class="btn btn-outline-primary" href="manage-rooms.php?property_id=<?= (int)$propertyId ?>">Manage rooms</a></div>
    </div>
    <?php if ($feedback): ?><div class="alert-inline <?= $feedback['type']==='error'?'error':'' ?> mb-3" role="status"><?= $escape($feedback['message']) ?></div><?php endif; ?>
    <div class="row g-4">
        <section class="col-xl-7"><div class="admin-card h-100">
            <?php if ($imageUrl): ?><img class="img-fluid rounded-3 mb-4" src="<?= $escape($imageUrl) ?>" alt="<?= $escape($property['name']) ?>" referrerpolicy="no-referrer"><?php endif; ?>
            <dl class="row mb-0"><dt class="col-sm-4 py-2">Destination</dt><dd class="col-sm-8 py-2"><?= $escape($property['destination_name'].', '.$property['destination_country']) ?></dd><dt class="col-sm-4 py-2">Type</dt><dd class="col-sm-8 py-2"><?= $escape($property['property_type']) ?></dd><dt class="col-sm-4 py-2">Owner</dt><dd class="col-sm-8 py-2"><?= $escape($property['owner_name'] ?: 'Unassigned') ?></dd><dt class="col-sm-4 py-2">Nightly rate</dt><dd class="col-sm-8 py-2"><?= $escape($property['currency'].' '.number_format((float)$property['base_nightly_rate'],2)) ?></dd><dt class="col-sm-4 py-2">Status</dt><dd class="col-sm-8 py-2"><?= $escape(ucwords(str_replace('_',' ',$property['status']))) ?></dd><dt class="col-sm-4 py-2">Description</dt><dd class="col-sm-8 py-2"><?= nl2br($escape($property['description'])) ?></dd></dl>
        </div></section>
        <section class="col-xl-5"><div class="admin-card"><h2 class="h5 fw-bold">Location</h2><?php if ($property['latitude'] !== null && $property['longitude'] !== null): ?><div id="property-detail-map" style="height:320px;border-radius:.8rem"></div><?php else: ?><p class="text-secondary mb-0">No map location set. Edit this property and choose a location on the map.</p><?php endif; ?></div></section>
        <section class="col-12"><div class="admin-card"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 fw-bold mb-0">Rooms at this property</h2><a class="btn btn-sm btn-primary" href="create-room.php?property_id=<?= (int)$propertyId ?>">Add room</a></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Room type</th><th>Price</th><th>Capacity</th><th>Units</th><th>Bookings</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rooms as $room): ?><tr><td><?= $escape($room['name']) ?></td><td><?= $escape($room['currency'].' '.number_format((float)$room['nightly_rate'],2)) ?></td><td><?= (int)$room['max_guests'] ?></td><td><?= (int)$room['total_units'] ?></td><td><?= (int)$room['booking_count'] ?></td><td><?= $escape(ucfirst($room['status'])) ?></td><td><a class="btn btn-sm btn-outline-primary" href="edit-room.php?id=<?= (int)$room['id'] ?>">Edit</a></td></tr><?php endforeach; ?><?php if (!$rooms): ?><tr><td colspan="7" class="text-center text-secondary py-4">No room types are connected to this property yet.</td></tr><?php endif; ?></tbody></table></div></div></section>
        <section class="col-12"><div class="admin-card"><h2 class="h5 fw-bold mb-3">Bookings for this property and its rooms</h2><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Reference</th><th>Guest</th><th>Room type</th><th>Dates</th><th>Rooms</th><th>Status</th></tr></thead><tbody><?php foreach($bookings as $booking): ?><tr><td><a href="manage-bookings.php?id=<?= (int)$booking['id'] ?>"><?= $escape($booking['reference']) ?></a></td><td><?= $escape($booking['guest_first_name'].' '.$booking['guest_last_name']) ?></td><td><?= $escape($booking['room_name'] ?? 'Not assigned') ?></td><td><?= $escape($booking['check_in'].' – '.$booking['check_out']) ?></td><td><?= (int)$booking['room_count'] ?></td><td><?= $escape(ucfirst($booking['status'])) ?></td></tr><?php endforeach; ?><?php if (!$bookings): ?><tr><td colspan="6" class="text-center text-secondary py-4">No bookings for this property.</td></tr><?php endif; ?></tbody></table></div></div></section>
    </div>
</main>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
