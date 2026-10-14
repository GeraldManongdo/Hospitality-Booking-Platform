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
$query = $database->prepare(
    'SELECT destinations.*,(SELECT COUNT(*) FROM properties WHERE properties.destination_id=destinations.id) AS property_count
     FROM destinations
     WHERE destinations.name LIKE :name OR destinations.country LIKE :country OR destinations.slug LIKE :slug
     ORDER BY destinations.name'
);
$term = '%' . $search . '%';
$query->execute(['name' => $term, 'country' => $term, 'slug' => $term]);
$destinations = $query->fetchAll();
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Destination management';
$activeSection = 'destinations';
require __DIR__ . '/../../components/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><span class="eyebrow">ADMIN · LOCATIONS</span><h1 class="h2 fw-bold mt-2 mb-1">Destinations</h1><p class="text-secondary mb-0">Manage destination names, regions, images, and listing status.</p></div>
        <a class="btn btn-primary" href="create-destination.php"><i class="bi bi-plus-lg me-1"></i>Create destination</a>
    </div>
    <?php if ($feedback): ?><div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-4" role="status"><?= $escape($feedback['message']) ?></div><?php endif; ?>
    <section class="admin-card">
        <form class="row g-2 mb-3" method="get"><div class="col-md-9"><label class="visually-hidden" for="destination-search">Search destinations</label><input class="form-control" id="destination-search" name="q" value="<?= $escape($search) ?>" placeholder="Search destination, region, or slug"></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Search</button><a class="btn btn-outline-secondary" href="manage-destinations.php">Clear</a></div></form>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Destination</th><th>Country / region</th><th>Properties</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($destinations as $destination): ?>
                <tr><td class="fw-semibold"><?= $escape($destination['name']) ?><small class="d-block text-secondary"><?= $escape($destination['slug']) ?></small></td><td><?= $escape($destination['country']) ?></td><td><?= (int) $destination['property_count'] ?></td><td><?= $escape(ucwords(str_replace('_', ' ', $destination['status']))) ?></td><td><div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-primary" href="edit-destination.php?id=<?= (int) $destination['id'] ?>">Edit</a><form method="post" action="../../../back-end/controllers/admin-destinations.php" class="d-flex gap-1"><input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_resource_csrf_token']) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="record_id" value="<?= (int) $destination['id'] ?>"><select class="form-select form-select-sm" name="status" aria-label="Destination status"><?php foreach (['draft','in_review','published','archived'] as $status): ?><option value="<?= $status ?>" <?= $status === $destination['status'] ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_',' ',$status))) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-outline-primary" type="submit">Set</button></form></div></td></tr>
            <?php endforeach; ?>
            <?php if (!$destinations): ?><tr><td colspan="5" class="text-center text-secondary py-4">No destinations found.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
</main>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
