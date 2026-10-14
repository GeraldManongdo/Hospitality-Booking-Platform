<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

if (empty($_SESSION['admin_users_csrf_token'])) {
    $_SESSION['admin_users_csrf_token'] = bin2hex(random_bytes(32));
}

$database = getDatabaseConnection();
$roles = $database->query('SELECT id, name FROM roles ORDER BY name')->fetchAll();
$user = [
    'id' => null,
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'role_id' => '',
    'status' => 'pending',
];

$userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($userId) {
    $query = $database->prepare(
        'SELECT id, first_name, last_name, email, phone, role_id, status
         FROM users WHERE id = :id'
    );
    $query->execute(['id' => $userId]);
    $user = $query->fetch();
    if (!$user) {
        http_response_code(404);
        exit('User account not found.');
    }
}

$isEditing = $user['id'] !== null;
$pageTitle = $isEditing ? 'Edit user' : 'Create user';
$activeSection = 'users';
require __DIR__ . '/../../components/admin-header.php';
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div class="mb-4">
        <a href="manage-users.php" class="small"><i class="bi bi-arrow-left me-1"></i>Back to users</a>
        <span class="eyebrow d-block mt-3">ADMIN · ACCOUNTS</span>
        <h1 class="h2 fw-bold mt-2 mb-1"><?= $isEditing ? 'Edit user' : 'Create user' ?></h1>
        <p class="text-secondary mb-0"><?= $isEditing ? 'Update account information, role, or status.' : 'Add an account and assign its role.' ?></p>
    </div>

    <section class="admin-card">
        <form action="../../../back-end/controllers/admin-users.php" method="post" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_users_csrf_token']) ?>">
            <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
            <?php if ($isEditing): ?>
                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
            <?php endif; ?>

            <div class="col-md-6">
                <label class="form-label fw-semibold" for="first-name">First name</label>
                <input class="form-control" id="first-name" name="first_name" maxlength="100" value="<?= $escape($user['first_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="last-name">Last name</label>
                <input class="form-control" id="last-name" name="last_name" maxlength="100" value="<?= $escape($user['last_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="email">Email address</label>
                <input class="form-control" id="email" name="email" type="email" maxlength="254" value="<?= $escape($user['email']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="phone">Phone number</label>
                <input class="form-control" id="phone" name="phone" type="tel" maxlength="32" pattern="[0-9+ .()\-]*" value="<?= $escape($user['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="role">Role</label>
                <select class="form-select" id="role" name="role_id" required>
                    <option value="">Choose role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role['id'] ?>" <?= (int) $user['role_id'] === (int) $role['id'] ? 'selected' : '' ?>>
                            <?= $escape(ucfirst($role['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="status">Account status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['pending', 'active', 'suspended', 'disabled'] as $status): ?>
                        <option value="<?= $status ?>" <?= $user['status'] === $status ? 'selected' : '' ?>>
                            <?= $escape(ucfirst($status)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold" for="password">
                    <?= $isEditing ? 'New password (optional)' : 'Password' ?>
                </label>
                <input
                    class="form-control"
                    id="password"
                    name="password"
                    type="password"
                    minlength="8"
                    maxlength="255"
                    autocomplete="new-password"
                    <?= $isEditing ? '' : 'required' ?>
                >
                <div class="form-text">
                    <?= $isEditing ? 'Leave blank to keep the current password.' : 'Use at least 8 characters.' ?>
                </div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><?= $isEditing ? 'Save changes' : 'Create user' ?></button>
                <a class="btn btn-outline-secondary" href="manage-users.php">Cancel</a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/../../components/admin-footer.php'; ?>