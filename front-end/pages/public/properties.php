<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../back-end/config/database.php';
$database = getDatabaseConnection();
$search = trim((string) ($_GET['q'] ?? ''));
$destinationId = filter_var($_GET['destination_id'] ?? null, FILTER_VALIDATE_INT);
$query = $database->prepare(
    'SELECT properties.id,properties.name,properties.city,properties.country,properties.property_type,
            properties.description,properties.base_nightly_rate,properties.currency,
            destinations.name AS destination_name,
            (SELECT property_images.image_url FROM property_images
             WHERE property_images.property_id=properties.id
             ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1) AS image_url
     FROM properties
     INNER JOIN destinations ON destinations.id=properties.destination_id
     WHERE properties.status=\'published\'
       AND destinations.status=\'published\'
       AND EXISTS (SELECT 1 FROM room_types WHERE room_types.property_id=properties.id AND room_types.status=\'active\')
       AND (:search_empty=1 OR properties.name LIKE :name OR properties.city LIKE :city
            OR properties.country LIKE :country OR destinations.name LIKE :destination)
       AND (:destination_filter IS NULL OR properties.destination_id=:destination_filter_match)
     ORDER BY properties.created_at DESC'
);
$term = '%' . $search . '%';
$query->execute([
    'search_empty' => $search === '' ? 1 : 0,
    'name' => $term,
    'city' => $term,
    'country' => $term,
    'destination' => $term,
    'destination_filter' => $destinationId ?: null,
    'destination_filter_match' => $destinationId ?: null,
]);
$properties = $query->fetchAll();
$pageTitle = 'Find your stay';
$activePage = 'properties';
require __DIR__ . '/../../components/public-header.php';
?>
<main><section class="page-hero"><div class="container"><span class="eyebrow">YOUR NEXT STAY</span><h1 class="display-6 fw-bold mt-2">Somewhere wonderful awaits.</h1><p class="text-secondary">Browse available stays and room types.</p>
<form class="row g-2 mt-3" action="properties.php" method="get"><?php if ($destinationId): ?><input type="hidden" name="destination_id" value="<?= (int) $destinationId ?>"><?php endif; ?><div class="col-md-9"><label for="search-query" class="visually-hidden">Destination or property</label><div class="input-group"><span class="input-group-text bg-white"><i class="bi bi-search"></i></span><input class="form-control" id="search-query" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search a destination, stay or style"></div></div><div class="col-md-3 d-grid"><button class="btn btn-primary" type="submit">Search stays</button></div></form></div></section>
<section class="container py-4"><div class="results-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4"><span><strong><?= count($properties) ?></strong> available stays <span class="text-secondary">· Prices shown per night</span></span></div><div class="row g-4" id="property-results">
<?php foreach ($properties as $property) { require __DIR__ . '/../../components/property-card.php'; } ?>
</div><?php if (!$properties): ?><div class="text-center py-5"><i class="bi bi-compass fs-1 text-teal"></i><h2 class="h4 mt-3">No available stays found</h2><p class="text-secondary">Try another destination or check back later.</p></div><?php endif; ?></section></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
