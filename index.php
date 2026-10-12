<?php
$pageTitle = 'Find your place in the world';
$activePage = 'home';
require __DIR__ . '/includes/customer-header.php';
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
        <?php
        $destinations = [
          ['Lisbon', 'Portugal', 'photo-1555881400-74d7acaacd8b', '2,140 stays'],
          ['Kyoto', 'Japan', 'photo-1493976040374-85c8e12f0c0e', '1,860 stays'],
          ['Amalfi Coast', 'Italy', 'photo-1533587851505-d119e13fa0d7', '960 stays'],
          ['Bali', 'Indonesia', 'photo-1537996194471-e657df975ab4', '1,320 stays'],
        ];
        foreach ($destinations as $destination): ?>
          <div class="col-6 col-lg-3"><a class="destination-card" href="properties.php?q=<?= urlencode($destination[0]) ?>"><img src="https://images.unsplash.com/<?= htmlspecialchars($destination[2]) ?>?auto=format&fit=crop&w=700&q=80" alt="<?= htmlspecialchars($destination[0]) ?>"><span class="destination-overlay"><strong><?= htmlspecialchars($destination[0]) ?></strong><small><?= htmlspecialchars($destination[1]) ?> · <?= htmlspecialchars($destination[3]) ?></small></span></a></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <section class="py-5 bg-soft">
    <div class="container">
      <div class="section-heading"><div><span class="eyebrow">A FEW GOOD FINDS</span><h2 class="h2 fw-bold mt-2">Stays worth the trip</h2></div><a class="text-link" href="properties.php">See all stays <i class="bi bi-arrow-right"></i></a></div>
      <div class="row g-4">
        <?php
        $properties = require __DIR__ . '/includes/mock-properties.php';
        foreach (array_slice($properties, 0, 3) as $property) { require __DIR__ . '/components/property-card.php'; }
        ?>
      </div>
    </div>
  </section>
  <section class="py-5"><div class="container"><div class="inspire-banner p-4 p-lg-5"><div class="row align-items-center"><div class="col-lg-8"><span class="eyebrow text-white-50">YOUR NEXT CHAPTER</span><h2 class="display-6 fw-bold text-white mt-2">The best plans leave room for wonder.</h2><p class="text-white-50 mb-lg-0">Browse places that make getting away feel effortless.</p></div><div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a href="properties.php" class="btn btn-light btn-lg">Find your stay <i class="bi bi-arrow-up-right ms-2"></i></a></div></div></div></div></section>
</main>
<?php require __DIR__ . '/includes/customer-footer.php'; ?>
