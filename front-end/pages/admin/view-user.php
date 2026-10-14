<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

$userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$userId) {
    http_response_code(400);
    exit('A valid user ID is required.');
}

$query = getDatabaseConnection()->prepare(
    'SELECT users.id, users.first_name, users.last_name, users.email, users.phone,
            users.status, users.created_at, users.updated_at, users.last_login_at,
            roles.name AS role
     FROM users
     INNER JOIN roles ON roles.id = users.role_id
     WHERE users.id = :id'
);
$query->execute(['id' => $userId]);
$user = $query->fetch();
if (!$user) {
    http_response_code(404);
    exit('User account not found.');
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'View user';
$activeSection = 'users';
require __DIR__ . '/../../components/admin-header.php';
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div class="mb-4">
        <a href="manage-users.php" class="small"><i class="bi bi-arrow-left me-1"></i>Back to users</a>
        <span class="eyebrow d-block mt-3">ADMIN · ACCOUNTS</span>
        <h1 class="h2 fw-bold mt-2 mb-1"><?= $escape($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="text-secondary mb-0">Account details</p>
    </div>

    <section class="admin-card">
        <dl class="row mb-0">
            <dt class="col-sm-3 py-2">Email</dt><dd class="col-sm-9 py-2"><?= $escape($user['email']) ?></dd>
            <dt class="col-sm-3 py-2">Phone</dt><dd class="col-sm-9 py-2"><?= $escape($user['phone'] ?: 'Not provided') ?></dd>
            <dt class="col-sm-3 py-2">Role</dt><dd class="col-sm-9 py-2"><?= $escape(ucfirst($user['role'])) ?></dd>
            <dt class="col-sm-3 py-2">Status</dt><dd class="col-sm-9 py-2"><?= $escape(ucfirst($user['status'])) ?></dd>
            <dt class="col-sm-3 py-2">Created</dt><dd class="col-sm-9 py-2"><?= $escape($user['created_at']) ?></dd>
            <dt class="col-sm-3 py-2">Last updated</dt><dd class="col-sm-9 py-2"><?= $escape($user['updated_at']) ?></dd>
            <dt class="col-sm-3 py-2">Last login</dt><dd class="col-sm-9 py-2"><?= $escape($user['last_login_at'] ?: 'Never') ?></dd>
        </dl>
        <div class="d-flex gap-2 mt-4">
            <a class="btn btn-primary" href="create-users.php?id=<?= (int) $user['id'] ?>">Edit user</a>
            <a class="btn btn-outline-secondary" href="manage-users.php">Back to users</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
