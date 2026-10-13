<?php
$properties = require __DIR__ . "/includes/mock-properties.php";
$property = $properties[0];
foreach ($properties as $candidate) {
    if ($candidate["id"] === ($_GET["id"] ?? "")) {
        $property = $candidate;
        break;
    }
}
$pageTitle = $property["name"];
require __DIR__ . "/includes/customer-header.php";
?>
<main><section class="container py-4"><nav aria-label="Breadcrumb" class="small mb-3"><a href="properties.php">Stays</a><span class="text-secondary mx-2">/</span><span><?= htmlspecialchars(
    $property["name"],
) ?></span></nav><div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3"><div><h1 class="h2 fw-bold mb-1"><?= htmlspecialchars(
    $property["name"],
) ?></h1><p class="text-secondary mb-0"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars(
    $property["location"],
) ?> <span class="mx-2">·</span><i class="bi bi-star-fill text-warning me-1"></i><?= htmlspecialchars(
     (string) $property["rating"],
 ) ?> <span class="text-secondary">(<?= (int) $property[
     "reviews"
 ] ?> sample reviews)</span></p></div><button class="btn btn-outline-primary" type="button" data-wishlist="<?= htmlspecialchars(
     $property["id"],
 ) ?>"><i class="bi bi-heart me-1"></i>Save</button></div>
<div class="property-gallery mb-4"><img src="https://images.unsplash.com/<?= htmlspecialchars(
    $property["image"],
) ?>?auto=format&fit=crop&w=1200&q=85" alt="View of <?= htmlspecialchars(
    $property["name"],
) ?>"><img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80" alt="Comfortable guest room"><img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80" alt="Stay interior"><img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80" alt="Outdoor pool"><img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80" alt="Dining area"></div>
<div class="data-note mb-4"><i class="bi bi-info-circle me-1"></i>This property and its availability are sample content for a frontend demo.</div><div class="row g-5"><div class="col-lg-7"><div class="d-flex align-items-center gap-3 pb-4 border-bottom"><div class="feature-icon"><i class="bi bi-house-heart"></i></div><div><h2 class="h5 fw-bold mb-1"><?= htmlspecialchars(
    $property["type"],
) ?> · hosted with care</h2><p class="text-secondary mb-0">A relaxed place to call home while you explore.</p></div></div><div class="py-4 border-bottom"><h2 class="h5 fw-bold">About this stay</h2><p class="text-secondary mb-0"><?= htmlspecialchars(
     $property["description"],
 ) ?> Thoughtfully appointed rooms, welcoming service, and a wonderful base for discovering the neighbourhood at your own pace.</p></div><div class="py-4 border-bottom"><h2 class="h5 fw-bold mb-3">What makes it special</h2><div class="row g-3"><div class="col-sm-6"><i class="bi bi-wifi text-teal me-2"></i>Complimentary Wi-Fi</div><div class="col-sm-6"><i class="bi bi-cup-hot text-teal me-2"></i>Breakfast available</div><div class="col-sm-6"><i class="bi bi-snow text-teal me-2"></i>Air conditioning</div><div class="col-sm-6"><i class="bi bi-water text-teal me-2"></i>Outdoor pool</div><div class="col-sm-6"><i class="bi bi-p-circle text-teal me-2"></i>On-site parking</div><div class="col-sm-6"><i class="bi bi-door-open text-teal me-2"></i>Private bathroom</div></div></div><div class="py-4"><h2 class="h5 fw-bold">A note on reviews</h2><p class="text-secondary">Guest ratings displayed on this sample listing are illustrative UI data, not verified customer feedback.</p></div></div>
<aside class="col-lg-5"><div class="booking-card sticky-lg-top" style="top:1.5rem"><div class="d-flex justify-content-between align-items-baseline"><span class="h4 fw-bold mb-0">$<?= (int) $property[
    "price"
] ?></span><span class="text-secondary">/ night</span></div><div class="row g-2 mt-3"><div class="col-6"><label class="form-label small fw-semibold" for="detail-checkin">Check-in</label><input class="form-control" id="detail-checkin" type="date" data-date-min-today></div><div class="col-6"><label class="form-label small fw-semibold" for="detail-checkout">Check-out</label><input class="form-control" id="detail-checkout" type="date" data-date-min-today></div><div class="col-12"><label class="form-label small fw-semibold" for="detail-guests">Guests</label><select class="form-select" id="detail-guests"><option value="2">2 guests</option><option value="1">1 guest</option><option value="3">3 guests</option><option value="4">4 guests</option></select></div></div><div id="detail-date-error" class="invalid-feedback d-block d-none mt-2"></div><a class="btn btn-primary w-100 mt-3" id="start-booking" href="booking.php?id=<?= urlencode(
    $property["id"],
) ?>">Continue to booking <i class="bi bi-arrow-right ms-1"></i></a><p class="text-center small text-secondary mt-3 mb-0">No payment or reservation is made in this demo.</p></div></aside></div></section></main>
<script>
(() => {
  const checkin=document.getElementById('detail-checkin'), checkout=document.getElementById('detail-checkout'), guests=document.getElementById('detail-guests'), link=document.getElementById('start-booking'), error=document.getElementById('detail-date-error');
  checkin.addEventListener('change',()=>{checkout.min=checkin.value || checkout.min;});
  link.addEventListener('click',(event)=>{if(checkin.value&&checkout.value&&checkout.value>checkin.value){const url=new URL(link.href);url.searchParams.set('checkin',checkin.value);url.searchParams.set('checkout',checkout.value);url.searchParams.set('guests',guests.value);link.href=url.href;return;}event.preventDefault();error.textContent='Choose valid check-in and check-out dates before continuing.';error.classList.remove('d-none');});
})();
</script>
<?php require __DIR__ . "/includes/customer-footer.php"; ?>
