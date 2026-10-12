<?php
$pageTitle = 'Explore destinations';
$activePage = 'destinations';
require __DIR__ . '/includes/customer-header.php';
$destinations = [
  ['Lisbon','Portugal','photo-1555881400-74d7acaacd8b','Coastal charm, tiled streets and long, golden evenings.'],
  ['Kyoto','Japan','photo-1493976040374-85c8e12f0c0e','Quiet gardens, timeless craft and culture around every corner.'],
  ['Amalfi Coast','Italy','photo-1533587851505-d119e13fa0d7','Cliffside villages, sea air and la dolce vita.'],
  ['Bali','Indonesia','photo-1537996194471-e657df975ab4','Slow mornings, green landscapes and island spirit.'],
  ['Copenhagen','Denmark','photo-1513622470522-26c3c8a854bc','Waterfront living, good design and cosy neighbourhoods.'],
  ['Cape Town','South Africa','photo-1580060839134-75a5edca2e99','A mountain-framed city with a wide-open horizon.'],
];
?>
<main><section class="page-hero"><div class="container"><span class="eyebrow">THE WORLD, OPEN</span><h1 class="display-6 fw-bold mt-2">Find a place that calls to you.</h1><p class="text-secondary mb-0">A few starting points for your next good story.</p></div></section><section class="container py-5"><div class="row g-4">
<?php foreach($destinations as $destination): ?><div class="col-sm-6 col-lg-4"><a class="destination-card d-block" href="properties.php?q=<?= urlencode($destination[0]) ?>"><img src="https://images.unsplash.com/<?= htmlspecialchars($destination[2]) ?>?auto=format&fit=crop&w=900&q=80" alt="<?= htmlspecialchars($destination[0]) ?>"><span class="destination-overlay"><strong><?= htmlspecialchars($destination[0]) ?>, <?= htmlspecialchars($destination[1]) ?></strong><small><?= htmlspecialchars($destination[3]) ?></small></span></a></div><?php endforeach; ?>
</div></section></main>
<?php require __DIR__ . '/includes/customer-footer.php'; ?>
