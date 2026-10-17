<?php
$pageTitle = 'Find your place in the world';
$activePage = 'home';
require_once __DIR__ . '/../../../back-end/config/database.php';
$database = getDatabaseConnection();
$destinations = $database->query(
    'SELECT destinations.id,destinations.name,destinations.country,
            COALESCE(destinations.image_url,
                (SELECT property_images.image_url FROM properties
                 INNER JOIN property_images ON property_images.property_id=properties.id
                 WHERE properties.destination_id=destinations.id
                 ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1)) AS image_url,
            (SELECT COUNT(DISTINCT properties.id) FROM properties
             INNER JOIN room_types ON room_types.property_id=properties.id AND room_types.status=\'active\'
             WHERE properties.destination_id=destinations.id AND properties.status=\'published\') AS stay_count
     FROM destinations WHERE destinations.status=\'published\'
     ORDER BY stay_count DESC,destinations.name LIMIT 4'
)->fetchAll();
$properties = $database->query(
    'SELECT properties.id,properties.name,properties.city,properties.country,properties.property_type,
            properties.description,properties.base_nightly_rate,properties.currency,
            destinations.name AS destination_name,
            (SELECT property_images.image_url FROM property_images
             WHERE property_images.property_id=properties.id
             ORDER BY property_images.is_primary DESC,property_images.sort_order LIMIT 1) AS image_url
     FROM properties INNER JOIN destinations ON destinations.id=properties.destination_id
     WHERE properties.status=\'published\' AND destinations.status=\'published\'
       AND EXISTS (SELECT 1 FROM room_types WHERE room_types.property_id=properties.id AND room_types.status=\'active\')
     ORDER BY properties.created_at DESC LIMIT 3'
)->fetchAll();
require __DIR__ . '/../../components/public-header.php';
?>
<main>
  <section class="hero-section">
    <div class="container py-5">
      <div class="row align-items-center min-vh-hero">
        <div class="col-lg-7 py-5">
          <span class="eyebrow"><i class="bi bi-stars me-2"></i>STAYS THAT STAY WITH YOU</span>
          <h1 class="display-3 fw-bold mt-3">Your next story<br><span class="text-teal">starts somewhere.</span></h1>
          <p class="lead text-secondary mt-3 mb-4">Find thoughtful stays and unforgettable places, all in one journey.</p>
          <form class="search-panel row g-2 align-items-end" action="properties.php" method="get">
            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="destination">Where to?</label>
              <div class="input-group"><span class="input-group-text"><i class="bi bi-geo-alt"></i></span><input class="form-control" id="destination" name="q" placeholder="City, region or stay"></div>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label small fw-semibold" for="checkin">Check in</label>
              <input class="form-control" type="date" id="checkin" name="checkin">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label small fw-semibold" for="guests">Guests</label>
              <select class="form-select" id="guests" name="guests"><option>2 guests</option><option>1 guest</option><option>3 guests</option><option>4 guests</option><option>5+ guests</option></select>
            </div>
            <div class="col-12 col-md-2 d-grid"><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-search me-2"></i>Explore</button></div>
          </form>
          <div class="mt-4 small text-secondary"><i class="bi bi-shield-check me-1 text-teal"></i> Carefully chosen stays. A better way to get away.</div>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="hero-photo-wrap">
            <img class="hero-photo" src="https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=1000&q=85" alt="Sunlit Mediterranean coast with white architecture">
            <div class="floating-note"><span class="avatar-dot"><i class="bi bi-heart-fill"></i></span><span><strong>Find your kind of escape</strong><small class="d-block text-secondary">Places worth slowing down for</small></span></div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5">
    <div class="container">
      <div class="section-heading"><div><span class="eyebrow">GO SOMEWHERE GOOD</span><h2 class="h2 fw-bold mt-2">Places to fall for</h2></div><a class="text-link" href="destinations.php">Explore destinations <i class="bi bi-arrow-right"></i></a></div>
      <div class="row g-4">
        <?php foreach ($destinations as $destination): ?>
          <?php $imageUrl = $destination['image_url'] ?: 'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=700&q=80'; ?>
          <div class="col-6 col-lg-3"><a class="destination-card" href="properties.php?destination_id=<?= (int) $destination['id'] ?>"><img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($destination['name'], ENT_QUOTES, 'UTF-8') ?>"><span class="destination-overlay"><strong><?= htmlspecialchars($destination['name'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($destination['country'], ENT_QUOTES, 'UTF-8') ?> · <?= (int) $destination['stay_count'] ?> stays</small></span></a></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <section class="py-5 bg-soft">
    <div class="container">
      <div class="section-heading"><div><span class="eyebrow">A FEW GOOD FINDS</span><h2 class="h2 fw-bold mt-2">Stays worth the trip</h2></div><a class="text-link" href="properties.php">See all stays <i class="bi bi-arrow-right"></i></a></div>
      <div class="row g-4">
        <?php foreach ($properties as $property) { require __DIR__ . '/../../components/property-card.php'; } ?>
      </div>
    </div>
  </section>
  <section class="py-5"><div class="container"><div class="inspire-banner p-4 p-lg-5"><div class="row align-items-center"><div class="col-lg-8"><span class="eyebrow text-white-50">YOUR NEXT CHAPTER</span><h2 class="display-6 fw-bold text-white mt-2">The best plans leave room for wonder.</h2><p class="text-white-50 mb-lg-0">Browse places that make getting away feel effortless.</p></div><div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a href="properties.php" class="btn btn-light btn-lg">Find your stay <i class="bi bi-arrow-up-right ms-2"></i></a></div></div></div></div></section>
</main>
<?php require __DIR__ . '/../../components/public-footer.php'; ?>