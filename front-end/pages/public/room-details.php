<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../back-end/config/database.php';
$roomId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$roomId) {
    http_response_code(400);
    exit('A valid room type is required.');
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
$availability = $database->prepare(
    'SELECT inventory_date,available_units,nightly_rate
     FROM room_inventory WHERE room_type_id=:id AND inventory_date>=CURRENT_DATE
     ORDER BY inventory_date LIMIT 14'
);
$availability->execute(['id' => $roomId]);
$availabilityRows = $availability->fetchAll();
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$imageUrl = $room['image_url'] ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=85';
$pageTitle = $room['name'] . ' · ' . $room['property_name'];
require __DIR__ . '/../../components/public-header.php';
?>
<main class="container py-4"><p class="small"><a href="property-details.php?id=<?= (int) $room['property_id'] ?>"><?= $escape($room['property_name']) ?></a> <span class="text-secondary mx-2">/</span> Room details</p><div class="row g-4"><section class="col-lg-7"><img class="rounded-4 w-100 mb-4" style="height:360px;object-fit:cover" src="<?= $escape($imageUrl) ?>" alt="<?= $escape($room['name']) ?>"><span class="eyebrow">ROOM TYPE</span><h1 class="h2 fw-bold mt-2"><?= $escape($room['name']) ?></h1><p class="text-secondary"><i class="bi bi-geo-alt me-1"></i><?= $escape($room['property_name'] . ' · ' . $room['city'] . ', ' . $room['country']) ?></p><p><?= nl2br($escape($room['description'] ?: 'A comfortable room at this property.')) ?></p><div class="row g-3 my-3"><div class="col-sm-4"><div class="metric-card"><span class="small text-secondary">Guests per room</span><div class="h4 mt-2 mb-0"><?= (int) $room['max_guests'] ?></div></div></div><div class="col-sm-4"><div class="metric-card"><span class="small text-secondary">Units</span><div class="h4 mt-2 mb-0"><?= (int) $room['total_units'] ?></div></div></div><div class="col-sm-4"><div class="metric-card"><span class="small text-secondary">Rate from</span><div class="h4 mt-2 mb-0"><?= $escape($room['currency']) ?> <?= number_format((float) $room['nightly_rate'],2) ?></div></div></div></div></section><aside class="col-lg-5"><div class="booking-card sticky-lg-top" style="top:1.5rem"><h2 class="h5 fw-bold">Upcoming availability</h2><p class="small text-secondary">Availability is checked again when you submit your booking.</p><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Units</th><th>Rate</th></tr></thead><tbody><?php foreach ($availabilityRows as $row): ?><tr><td><?= $escape($row['inventory_date']) ?></td><td><?= (int) $row['available_units'] ?></td><td><?= $row['nightly_rate'] === null ? $escape($room['currency'] . ' ' . number_format((float) $room['nightly_rate'],2)) : $escape($room['currency'] . ' ' . number_format((float) $row['nightly_rate'],2)) ?></td></tr><?php endforeach; ?><?php if (!$availabilityRows): ?><tr><td colspan="3" class="text-secondary">No date-specific availability overrides.</td></tr><?php endif; ?></tbody></table></div><a class="btn btn-primary w-100 mt-3" href="booking.php?room_type_id=<?= (int) $room['id'] ?>">Book this room</a></div></aside></div></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
