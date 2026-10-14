<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

$database = getDatabaseConnection();
$search = trim((string) ($_GET['q'] ?? ''));
$query = $database->prepare(
    'SELECT properties.id, properties.name, properties.city, properties.country,
            properties.property_type, properties.status, destinations.name AS destination_name,
            COUNT(DISTINCT room_types.id) AS room_count
     FROM properties
     INNER JOIN destinations ON destinations.id = properties.destination_id
     LEFT JOIN room_types ON room_types.property_id = properties.id
     WHERE properties.name LIKE :name OR properties.city LIKE :city
        OR properties.country LIKE :country
     GROUP BY properties.id, destinations.name
     ORDER BY properties.created_at DESC'
);
$term = '%' . $search . '%';
$query->execute(['name' => $term, 'city' => $term, 'country' => $term]);
$properties = $query->fetchAll();

if (empty($_SESSION['admin_properties_csrf_token'])) {
    $_SESSION['admin_properties_csrf_token'] = bin2hex(random_bytes(32));
}
$feedback = $_SESSION['admin_properties_feedback'] ?? null;
unset($_SESSION['admin_properties_feedback']);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Property management';
$activeSection = 'properties';
require __DIR__ . '/../../components/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
        <div><span class="eyebrow">ADMIN · LISTINGS</span><h1 class="h2 fw-bold mt-2 mb-1">Properties</h1><p class="text-secondary mb-0">Manage listings, location, rooms, and bookings.</p></div>
        <a class="btn btn-primary" href="create-properties.php"><i class="bi bi-plus-lg me-1"></i>Create property</a>
    </div>
    <?php if ($feedback): ?><div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-4" role="status"><?= $escape($feedback['message']) ?></div><?php endif; ?>
    <section class="admin-card">
        <form class="row g-2 mb-3" method="get"><div class="col-md-9"><label class="visually-hidden" for="property-search">Search properties</label><input id="property-search" class="form-control" name="q" value="<?= $escape($search) ?>" placeholder="Search property or location"></div><div class="col-md-3"><button class="btn btn-outline-primary" type="submit">Search</button></div></form>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>Property</th><th>Location</th><th>Type</th><th>Rooms</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td class="fw-semibold"><?= $escape($property['name']) ?><small class="d-block text-secondary"><?= $escape($property['destination_name']) ?></small></td>
                        <td><?= $escape($property['city'] . ', ' . $property['country']) ?></td>
                        <td><?= $escape($property['property_type']) ?></td><td><?= (int) $property['room_count'] ?></td>
                        <td><?= $escape(ucwords(str_replace('_', ' ', $property['status']))) ?></td>
                        <td><div class="d-flex flex-wrap gap-1">
                            <a class="btn btn-sm btn-outline-secondary" href="view-properties.php?id=<?= (int) $property['id'] ?>">View</a>
                            <a class="btn btn-sm btn-outline-primary" href="edit-properties.php?id=<?= (int) $property['id'] ?>">Edit</a>
                            <form method="post" action="../../../back-end/controllers/admin-properties.php" class="d-flex gap-1">
                                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_properties_csrf_token']) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="property_id" value="<?= (int) $property['id'] ?>">
                                <select class="form-select form-select-sm" name="status" aria-label="Property status"><?php foreach (['draft','in_review','published','rejected','archived'] as $status): ?><option value="<?= $status ?>" <?= $status === $property['status'] ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_',' ',$status))) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-outline-primary" type="submit">Set</button>
                            </form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$properties): ?><tr><td colspan="6" class="text-center text-secondary py-4">No properties found.</td></tr><?php endif; ?>
            </tbody>
        </table></div>
    </section>
</main>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
