<?php
$propertyBasePath = $propertyBasePath ?? '';
$imageUrl = $property['image_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80';
$location = trim($property['city'] . ', ' . $property['country'], ', ');
$rating = $property['rating'] === null ? null : number_format((float) $property['rating'], 1);
?>
<div class="col-md-6 col-xl-4 property-result" data-name="<?= htmlspecialchars(strtolower($property['name'] . ' ' . $location . ' ' . $property['property_type'])) ?>" data-price="<?= (float)$property['base_nightly_rate'] ?>" data-rating="<?= htmlspecialchars((string)($rating ?? '0')) ?>">
  <article class="property-card h-100">
    <div class="property-image-wrap"><a href="<?= $propertyBasePath ?>property-details.php?id=<?= (int) $property['id'] ?>"><img class="property-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($property['name'], ENT_QUOTES, 'UTF-8') ?>"></a><span class="property-tag"><?= htmlspecialchars($property['destination_name'], ENT_QUOTES, 'UTF-8') ?></span><button class="wish-button" type="button" data-wishlist="<?= (int) $property['id'] ?>" aria-label="Add <?= htmlspecialchars($property['name'], ENT_QUOTES, 'UTF-8') ?> to wishlist" aria-pressed="false"><i class="bi bi-heart"></i></button></div>
    <div class="p-3"><div class="d-flex justify-content-between gap-2"><a href="<?= $propertyBasePath ?>property-details.php?id=<?= (int) $property['id'] ?>" class="property-title"><?= htmlspecialchars($property['name'], ENT_QUOTES, 'UTF-8') ?></a><span class="small fw-semibold text-nowrap"><?php if ($rating !== null): ?><i class="bi bi-star-fill text-warning me-1"></i><?= htmlspecialchars($rating, ENT_QUOTES, 'UTF-8') ?><?php else: ?>New<?php endif; ?></span></div>
      <p class="small text-secondary mb-1"><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?> <span class="mx-1">·</span> <?= htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8') ?></p><p class="small text-secondary mb-3"><?= htmlspecialchars($property['description'], ENT_QUOTES, 'UTF-8') ?></p><div class="d-flex align-items-baseline gap-1"><strong><?= htmlspecialchars($property['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float)$property['base_nightly_rate'], 2) ?></strong><span class="small text-secondary">/ night</span><span class="ms-auto small text-secondary"><?= (int)$property['review_count'] ?> reviews</span></div></div>
  </article>
</div>
