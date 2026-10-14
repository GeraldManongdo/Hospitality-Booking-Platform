<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

$database = getDatabaseConnection();
$roles = $database->query(
    'SELECT roles.id, roles.name, roles.description, COUNT(users.id) AS user_count
     FROM roles LEFT JOIN users ON users.role_id = roles.id
     GROUP BY roles.id, roles.name, roles.description
     ORDER BY roles.id'
)->fetchAll();
$pageTitle = 'Role management';
$activeSection = 'roles';
require __DIR__ . '/../../components/admin-header.php';
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div class="mb-4">
        <span class="eyebrow">ADMIN · ACCESS CONTROL</span>
        <h1 class="h2 fw-bold mt-2 mb-1">Role management</h1>
        <p class="text-secondary mb-0">Assign platform roles from the user account editor.</p>
    </div>
    <section class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Role</th><th>Permission scope</th><th>Accounts</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td class="fw-semibold"><?= $escape(ucfirst($role['name'])) ?></td>
                            <td><?= $escape($role['description'] ?? 'No description configured.') ?></td>
                            <td><?= (int) $role['user_count'] ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="manage-users.php?role_id=<?= (int) $role['id'] ?>">Manage assignments</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
