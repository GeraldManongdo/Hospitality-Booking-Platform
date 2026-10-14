<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

if (empty($_SESSION['admin_users_csrf_token'])) {
    $_SESSION['admin_users_csrf_token'] = bin2hex(random_bytes(32));
}

$feedback = $_SESSION['admin_users_feedback'] ?? null;
unset($_SESSION['admin_users_feedback']);
$search = trim((string) ($_GET['q'] ?? ''));
$roleFilter = filter_var($_GET['role_id'] ?? null, FILTER_VALIDATE_INT);

$database = getDatabaseConnection();
$where = [];
$parameters = [];
if ($search !== '') {
    $where[] = '(users.first_name LIKE :first_name OR users.last_name LIKE :last_name OR users.email LIKE :email)';
    $searchTerm = '%' . $search . '%';
    $parameters += ['first_name' => $searchTerm, 'last_name' => $searchTerm, 'email' => $searchTerm];
}
if ($roleFilter) {
    $where[] = 'users.role_id = :role_id';
    $parameters['role_id'] = $roleFilter;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$query = $database->prepare(
    'SELECT users.id, users.first_name, users.last_name, users.email,
            users.phone, users.status, users.created_at, roles.name AS role
     FROM users
     INNER JOIN roles ON roles.id = users.role_id
     ' . $whereSql . '
     ORDER BY users.created_at DESC, users.id DESC'
);
$query->execute($parameters);
$users = $query->fetchAll();

$pageTitle = 'Manage users';
$activeSection = 'users';
require __DIR__ . '/../../components/admin-header.php';
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="eyebrow">ADMIN · ACCOUNTS</span>
            <h1 class="h2 fw-bold mt-2 mb-1">Manage users</h1>
            <p class="text-secondary mb-0">View accounts, assign roles, and manage access.</p>
        </div>
        <a class="btn btn-primary" href="create-users.php">
            <i class="bi bi-person-plus me-1"></i>Create user
        </a>
    </div>

    <?php if ($feedback): ?>
        <div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-4" role="status">
            <?= $escape($feedback['message']) ?>
        </div>
    <?php endif; ?>

    <section class="admin-card">
        <form class="row g-2 align-items-end mb-3" method="get">
            <div class="col-md-8">
                <label class="form-label small fw-semibold" for="user-search">Search users</label>
                <input
                    class="form-control"
                    id="user-search"
                    name="q"
                    value="<?= $escape($search) ?>"
                    placeholder="Name or email"
                >
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <?php if ($search !== ''): ?>
                    <a class="btn btn-outline-secondary" href="manage-users.php">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="d-flex justify-content-between small text-secondary mb-2">
            <span><?= count($users) ?> user<?= count($users) === 1 ? '' : 's' ?></span>
            <?php if ($roleFilter): ?><a href="manage-users.php?q=<?= urlencode($search) ?>">Clear role filter</a><?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="fw-semibold"><?= $escape($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= $escape($user['email']) ?></td>
                            <td><?= $escape(ucfirst($user['role'])) ?></td>
                            <td><span class="status-pill status-<?= $escape($user['status']) ?>"><?= $escape(ucfirst($user['status'])) ?></span></td>
                            <td><?= $escape(date('M j, Y', strtotime($user['created_at']))) ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a class="btn btn-sm btn-outline-secondary" href="view-user.php?id=<?= (int) $user['id'] ?>">View</a>
                                    <a class="btn btn-sm btn-outline-primary" href="create-users.php?id=<?= (int) $user['id'] ?>">Edit</a>
                                    <?php if ((int) $user['id'] !== (int) ($_SESSION['user']['id'] ?? 0)): ?>
                                        <?php if ($user['status'] === 'disabled'): ?>
                                            <form action="../../../back-end/controllers/admin-users.php" method="post">
                                                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_users_csrf_token']) ?>">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                                <button class="btn btn-sm btn-outline-success" type="submit">Reactivate</button>
                                            </form>
                                        <?php else: ?>
                                            <form
                                                action="../../../back-end/controllers/admin-users.php"
                                                method="post"
                                                onsubmit="return confirm(this.dataset.confirm)"
                                                data-confirm="<?= $escape('Deactivate ' . $user['first_name'] . ' ' . $user['last_name'] . '?') ?>"
                                            >
                                                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_users_csrf_token']) ?>">
                                                <input type="hidden" name="action" value="deactivate">
                                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                                <button class="btn btn-sm btn-outline-warning" type="submit">Deactivate</button>
                                            </form>
                                        <?php endif; ?>
                                        <form
                                            action="../../../back-end/controllers/admin-users.php"
                                            method="post"
                                            onsubmit="return confirm(this.dataset.confirm)"
                                            data-confirm="<?= $escape('Delete ' . $user['first_name'] . ' ' . $user['last_name'] . '? This action cannot be undone.') ?>"
                                        >
                                            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_users_csrf_token']) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$users): ?>
                        <tr>
                            <td class="text-center text-secondary py-4" colspan="6">
                                <?= $search !== '' ? 'No users match your search.' : 'No user accounts yet.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../../components/admin-footer.php'; ?>