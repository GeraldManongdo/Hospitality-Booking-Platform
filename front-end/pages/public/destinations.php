<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../back-end/config/database.php';
$database = getDatabaseConnection();
$destinations = $database->query(
    'SELECT destinations.id,destinations.name,destinations.country,destinations.description,
            COALESCE(destinations.image_url,
                (SELECT property_images.image_url FROM properties
                 INNER JOIN property_images ON property_images.property_id=properties.id
                 WHERE properties.destination_id=destinations.id
                 ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1)) AS image_url,
            (SELECT COUNT(DISTINCT properties.id) FROM properties
             INNER JOIN room_types ON room_types.property_id=properties.id AND room_types.status=\'active\'
             WHERE properties.destination_id=destinations.id AND properties.status=\'published\') AS stay_count
     FROM destinations
     WHERE destinations.status=\'published\'
     ORDER BY destinations.name'
)->fetchAll();
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Explore destinations';
$activePage = 'destinations';
require __DIR__ . '/../../components/public-header.php';
?>
<main><section class="page-hero"><div class="container"><span class="eyebrow">THE WORLD, OPEN</span><h1 class="display-6 fw-bold mt-2">Find a place that calls to you.</h1><p class="text-secondary mb-0">Explore places with stays available to book.</p></div></section><section class="container py-5"><div class="row g-4">
<?php foreach ($destinations as $destination): ?>
    <?php $imageUrl = $destination['image_url'] ?: 'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=900&q=80'; ?>
    <div class="col-sm-6 col-lg-4"><a class="destination-card d-block" href="properties.php?destination_id=<?= (int) $destination['id'] ?>"><img src="<?= $escape($imageUrl) ?>" alt="<?= $escape($destination['name']) ?>"><span class="destination-overlay"><strong><?= $escape($destination['name']) ?>, <?= $escape($destination['country']) ?></strong><small><?= $escape($destination['description'] ?: ((int) $destination['stay_count'] . ' available stays')) ?></small></span></a></div>
<?php endforeach; ?>
<?php if (!$destinations): ?><div class="col-12"><p class="text-secondary">No destinations are available right now.</p></div><?php endif; ?>
</div></section></main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>
