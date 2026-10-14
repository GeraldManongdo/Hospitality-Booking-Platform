<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_resource_csrf_token'])) {
    $_SESSION['admin_resource_csrf_token'] = bin2hex(random_bytes(32));
}
$database = getDatabaseConnection();
$search = trim((string) ($_GET['q'] ?? ''));
$propertyFilter = filter_var($_GET['property_id'] ?? null, FILTER_VALIDATE_INT);
$sql = 'SELECT room_types.*,properties.name AS property_name,
        (SELECT COUNT(*) FROM bookings WHERE bookings.room_type_id=room_types.id) AS booking_count
        FROM room_types INNER JOIN properties ON properties.id=room_types.property_id
        WHERE (room_types.name LIKE :room_name OR properties.name LIKE :property_name)';
if ($propertyFilter) {
    $sql .= ' AND room_types.property_id=:property_id';
}
$sql .= ' ORDER BY properties.name,room_types.name';
$query = $database->prepare($sql);
$term = '%' . $search . '%';
$parameters = ['room_name' => $term, 'property_name' => $term];
if ($propertyFilter) {
    $parameters['property_id'] = $propertyFilter;
}
$query->execute($parameters);
$rooms = $query->fetchAll();
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Room management';
$activeSection = 'rooms';
require __DIR__ . '/../../components/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><span class="eyebrow">ADMIN · INVENTORY</span><h1 class="h2 fw-bold mt-2 mb-1">Rooms</h1><p class="text-secondary mb-0">Manage room types, rates, capacity, and availability for each property.</p></div><a class="btn btn-primary" href="create-room.php<?= $propertyFilter ? '?property_id=' . (int) $propertyFilter : '' ?>"><i class="bi bi-plus-lg me-1"></i>Create room type</a></div>
    <?php if ($feedback): ?><div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-4" role="status"><?= $escape($feedback['message']) ?></div><?php endif; ?>
    <section class="admin-card">
        <form class="row g-2 mb-3" method="get"><?php if ($propertyFilter): ?><input type="hidden" name="property_id" value="<?= (int) $propertyFilter ?>"><?php endif; ?><div class="col-md-9"><label class="visually-hidden" for="room-search">Search rooms</label><input class="form-control" id="room-search" name="q" value="<?= $escape($search) ?>" placeholder="Search room type or property"></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Search</button><a class="btn btn-outline-secondary" href="manage-rooms.php<?= $propertyFilter ? '?property_id=' . (int) $propertyFilter : '' ?>">Clear</a></div></form>
        <?php if ($propertyFilter): ?><p class="small text-secondary">Showing rooms for property ID <?= (int) $propertyFilter ?>. <a href="manage-rooms.php">Show all</a></p><?php endif; ?>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Room type</th><th>Property</th><th>Nightly price</th><th>Capacity</th><th>Units</th><th>Bookings</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($rooms as $room): ?><tr><td class="fw-semibold"><?= $escape($room['name']) ?></td><td><?= $escape($room['property_name']) ?></td><td><?= $escape($room['currency'] . ' ' . number_format((float) $room['nightly_rate'],2)) ?></td><td><?= (int) $room['max_guests'] ?></td><td><?= (int) $room['total_units'] ?></td><td><?= (int) $room['booking_count'] ?></td><td><?= $escape(ucwords(str_replace('_',' ',$room['status']))) ?></td><td><a class="btn btn-sm btn-outline-primary" href="edit-room.php?id=<?= (int) $room['id'] ?>">Edit / availability</a></td></tr><?php endforeach; ?>
        <?php if (!$rooms): ?><tr><td colspan="8" class="text-center text-secondary py-4">No room types found.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
</main>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
