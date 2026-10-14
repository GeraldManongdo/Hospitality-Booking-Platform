<?php
$destinations = $database->query('SELECT id,name,country FROM destinations ORDER BY name')->fetchAll();
$partners = $database->query(
    "SELECT users.id,users.first_name,users.last_name,users.email FROM users
     INNER JOIN roles ON roles.id=users.role_id WHERE roles.name='partner'
     ORDER BY users.first_name,users.last_name"
)->fetchAll();
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$value = static fn (string $key, string $default = ''): string => (string) ($property[$key] ?? $default);
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">'
    . '<style>#property-map{height:360px;border-radius:1rem;border:1px solid var(--line)}.leaflet-container{font:inherit}</style>';
$activeSection = 'properties';
require __DIR__ . '/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="mb-4"><a href="<?= $isEditing ? 'view-properties.php?id=' . (int) $property['id'] : 'manage-properties.php' ?>" class="small"><i class="bi bi-arrow-left me-1"></i>Back to properties</a>
        <span class="eyebrow d-block mt-3">ADMIN · LISTINGS</span><h1 class="h2 fw-bold mt-2"><?= $escape($pageTitle) ?></h1>
        <?php if ($feedback): ?><div class="alert-inline error mt-3" role="alert"><?= $escape($feedback) ?></div><?php endif; ?>
    </div>
    <form class="admin-card row g-3" method="post" action="../../../back-end/controllers/admin-properties.php">
        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_properties_csrf_token']) ?>">
        <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
        <?php if ($isEditing): ?><input type="hidden" name="property_id" value="<?= (int) $property['id'] ?>"><?php endif; ?>
        <div class="col-md-6"><label class="form-label fw-semibold" for="property-name">Property name</label><input id="property-name" class="form-control" name="name" maxlength="180" value="<?= $escape($value('name')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="property-slug">Slug</label><input id="property-slug" class="form-control" name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="180" value="<?= $escape($value('slug')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination">Destination</label><select id="destination" name="destination_id" class="form-select" required><option value="">Select destination</option><?php foreach($destinations as $destination): ?><option value="<?= (int)$destination['id'] ?>" <?= (int)$value('destination_id') === (int)$destination['id'] ? 'selected' : '' ?>><?= $escape($destination['name'].', '.$destination['country']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="owner">Property partner</label><select id="owner" name="owner_user_id" class="form-select"><option value="">Unassigned</option><?php foreach($partners as $partner): ?><option value="<?= (int)$partner['id'] ?>" <?= (int)$value('owner_user_id') === (int)$partner['id'] ? 'selected' : '' ?>><?= $escape($partner['first_name'].' '.$partner['last_name'].' · '.$partner['email']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label fw-semibold" for="city">City</label><input id="city" class="form-control" name="city" maxlength="120" value="<?= $escape($value('city')) ?>" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold" for="country">Country</label><input id="country" class="form-control" name="country" maxlength="100" value="<?= $escape($value('country')) ?>" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold" for="type">Property type</label><input id="type" class="form-control" name="property_type" maxlength="80" value="<?= $escape($value('property_type')) ?>" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold" for="rate">Base nightly rate</label><input id="rate" class="form-control" type="number" name="base_nightly_rate" min="0" step="0.01" value="<?= $escape($value('base_nightly_rate','0')) ?>" required></div>
        <div class="col-md-2"><label class="form-label fw-semibold" for="currency">Currency</label><input id="currency" class="form-control text-uppercase" name="currency" pattern="[A-Za-z]{3}" maxlength="3" value="<?= $escape($value('currency','USD')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="status">Status</label><select id="status" name="status" class="form-select"><?php foreach(['draft','in_review','published','rejected','archived'] as $status): ?><option value="<?= $status ?>" <?= $value('status','draft') === $status ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_',' ',$status))) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><label class="form-label fw-semibold" for="image-url">Primary image URL (optional)</label><input id="image-url" class="form-control" type="url" name="image_url" maxlength="2048" value="<?= $escape($imageUrl ?? '') ?>" placeholder="https://…"></div>
        <div class="col-12"><label class="form-label fw-semibold" for="description">Description</label><textarea id="description" class="form-control" name="description" rows="4" required><?= $escape($value('description')) ?></textarea></div>
        <div class="col-12"><h2 class="h5 fw-bold mb-1">Property location</h2><p class="small text-secondary">Click or drag the pin on the OpenStreetMap map to set the location.</p><div id="property-map" aria-label="Property location map"></div></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="latitude">Latitude</label><input id="latitude" class="form-control" name="latitude" type="number" min="-90" max="90" step="0.0000001" value="<?= $escape($value('latitude')) ?>"></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="longitude">Longitude</label><input id="longitude" class="form-control" name="longitude" type="number" min="-180" max="180" step="0.0000001" value="<?= $escape($value('longitude')) ?>"></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><?= $isEditing ? 'Save property' : 'Create property' ?></button><a class="btn btn-outline-secondary ms-2" href="<?= $isEditing ? 'view-properties.php?id=' . (int)$property['id'] : 'manage-properties.php' ?>">Cancel</a></div>
    </form>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(() => {
  const lat = document.getElementById('latitude');
  const lng = document.getElementById('longitude');
  const defaultCenter = [lat.value === '' ? 20 : Number(lat.value), lng.value === '' ? 0 : Number(lng.value)];
  const map = L.map('property-map').setView(defaultCenter, lat.value && lng.value ? 14 : 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  let marker = null;
  const setPosition = (point) => {
    lat.value = point.lat.toFixed(7);
    lng.value = point.lng.toFixed(7);
    if (marker) {
      marker.setLatLng(point);
    } else {
      marker = L.marker(point, {draggable: true}).addTo(map);
      marker.on('dragend', () => setPosition(marker.getLatLng()));
    }
  };
  if (lat.value && lng.value) setPosition(L.latLng(Number(lat.value), Number(lng.value)));
  map.on('click', (event) => setPosition(event.latlng));
  [lat, lng].forEach((input) => input.addEventListener('change', () => {
    if (lat.value && lng.value) {
      const point = L.latLng(Number(lat.value), Number(lng.value));
      setPosition(point);
      map.setView(point, Math.max(map.getZoom(), 14));
    }
  }));
  window.setTimeout(() => map.invalidateSize(), 100);
})();
</script>
<?php require __DIR__ . '/admin-footer.php'; ?>
