<?php
declare(strict_types=1);

require __DIR__ . '/../config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/admin/manage-users.php', true, 303);
    exit;
}

$redirect = '../../front-end/pages/admin/manage-users.php';
$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['admin_users_csrf_token'] ?? '');

$fail = static function (string $message) use ($redirect): never {
    $_SESSION['admin_users_feedback'] = ['type' => 'error', 'message' => $message];
    header("Location: {$redirect}", true, 303);
    exit;
};

if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    $fail('Your session has expired. Please try again.');
}

$action = (string) ($_POST['action'] ?? '');
$database = null;

try {
    $database = getDatabaseConnection();

    if ($action === 'delete') {
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$userId || $userId === (int) ($_SESSION['user']['id'] ?? 0)) {
            $fail('You cannot delete your own administrator account.');
        }

        $database->beginTransaction();
        $target = $database->prepare(
            'SELECT users.id, users.role_id, roles.name AS role
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id
             FOR UPDATE'
        );
        $target->execute(['id' => $userId]);
        $user = $target->fetch();

        if (!$user) {
            $database->rollBack();
            $fail('The user no longer exists.');
        }

        if ($user['role'] === 'admin') {
            $activeAdmins = $database->query(
                "SELECT COUNT(*) FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE roles.name = 'admin' AND users.status = 'active'
            ")->fetchColumn();
            if ((int) $activeAdmins <= 1) {
                $database->rollBack();
                $fail('The last active administrator cannot be deleted.');
            }
        }

        $delete = $database->prepare('DELETE FROM users WHERE id = :id');
        $delete->execute(['id' => $userId]);
        $database->commit();

        $_SESSION['admin_users_feedback'] = [
            'type' => 'success',
            'message' => 'The user account was deleted.',
        ];
        header("Location: {$redirect}", true, 303);
        exit;
    }

    if (in_array($action, ['deactivate', 'activate'], true)) {
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$userId || $userId === (int) ($_SESSION['user']['id'] ?? 0)) {
            $fail('You cannot deactivate or reactivate your own administrator account.');
        }

        $targetQuery = $database->prepare(
            'SELECT users.status, roles.name AS role
             FROM users INNER JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id'
        );
        $targetQuery->execute(['id' => $userId]);
        $targetUser = $targetQuery->fetch();
        if (!$targetUser) {
            $fail('The user no longer exists.');
        }

        if ($action === 'deactivate' && $targetUser['role'] === 'admin' && $targetUser['status'] === 'active') {
            $activeAdmins = $database->query(
                "SELECT COUNT(*) FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE roles.name = 'admin' AND users.status = 'active'"
            )->fetchColumn();
            if ((int) $activeAdmins <= 1) {
                $fail('The last active administrator cannot be deactivated.');
            }
        }

        $updateStatus = $database->prepare('UPDATE users SET status = :status WHERE id = :id');
        $updateStatus->execute([
            'status' => $action === 'deactivate' ? 'disabled' : 'active',
            'id' => $userId,
        ]);
        $_SESSION['admin_users_feedback'] = [
            'type' => 'success',
            'message' => $action === 'deactivate'
                ? 'The user account was deactivated.'
                : 'The user account was reactivated.',
        ];
        header("Location: {$redirect}", true, 303);
        exit;
    }

    if (!in_array($action, ['create', 'update'], true)) {
        $fail('The requested user action is not valid.');
    }

    $userId = $action === 'update'
        ? filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT)
        : null;
    if ($action === 'update' && !$userId) {
        $fail('The user to update is not valid.');
    }

    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $roleId = filter_var($_POST['role_id'] ?? null, FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (
        $firstName === '' || mb_strlen($firstName) > 100
        || $lastName === '' || mb_strlen($lastName) > 100
    ) {
        $fail('First and last names are required and must be 100 characters or fewer.');
    }

    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fail('Enter a valid email address.');
    }

    if (strlen($phone) > 32 || ($phone !== '' && !preg_match('/^[0-9+ .()\-]+$/', $phone))) {
        $fail('Enter a valid phone number of no more than 32 characters.');
    }

    if (!in_array($status, ['pending', 'active', 'suspended', 'disabled'], true)) {
        $fail('Choose a valid account status.');
    }

    $roleQuery = $database->prepare('SELECT id FROM roles WHERE id = :id');
    $roleQuery->execute(['id' => $roleId]);
    if (!$roleId || $roleQuery->fetchColumn() === false) {
        $fail('Choose a valid account role.');
    }

    if ($action === 'create' && (strlen($password) < 8 || strlen($password) > 255)) {
        $fail('A password between 8 and 255 characters is required.');
    }

    if ($action === 'update' && $password !== '' && (strlen($password) < 8 || strlen($password) > 255)) {
        $fail('Enter a new password between 8 and 255 characters, or leave it blank.');
    }

    $currentUserId = (int) ($_SESSION['user']['id'] ?? 0);
    if ($action === 'update' && (int) $userId === $currentUserId) {
        $adminRole = $database->prepare('SELECT name FROM roles WHERE id = :id');
        $adminRole->execute(['id' => $roleId]);
        if ($adminRole->fetchColumn() !== 'admin' || $status !== 'active') {
            $fail('You cannot remove administrator access or deactivate your own account.');
        }
    }

    if ($action === 'update') {
        $currentTarget = $database->prepare(
            'SELECT roles.name AS role, users.status
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id'
        );
        $currentTarget->execute(['id' => $userId]);
        $existingUser = $currentTarget->fetch();
        if (!$existingUser) {
            $fail('The user no longer exists.');
        }

        $newRole = $database->prepare('SELECT name FROM roles WHERE id = :id');
        $newRole->execute(['id' => $roleId]);
        $newRoleName = $newRole->fetchColumn();
        if (
            $existingUser['role'] === 'admin'
            && $existingUser['status'] === 'active'
            && ($newRoleName !== 'admin' || $status !== 'active')
        ) {
            $activeAdmins = $database->query(
                "SELECT COUNT(*) FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE roles.name = 'admin' AND users.status = 'active'"
            )->fetchColumn();
            if ((int) $activeAdmins <= 1) {
                $fail('The last active administrator cannot be demoted or deactivated.');
            }
        }
    }

    if ($action === 'create') {
        $insert = $database->prepare(
            'INSERT INTO users
                (role_id, email, password_hash, first_name, last_name, phone, status)
             VALUES
                (:role_id, :email, :password_hash, :first_name, :last_name, :phone, :status)'
        );
        $insert->execute([
            'role_id' => $roleId,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone !== '' ? $phone : null,
            'status' => $status,
        ]);
    } else {
        $parameters = [
            'id' => $userId,
            'role_id' => $roleId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone !== '' ? $phone : null,
            'status' => $status,
        ];
        $passwordSql = '';
        if ($password !== '') {
            $passwordSql = ', password_hash = :password_hash';
            $parameters['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $update = $database->prepare(
            'UPDATE users
             SET role_id = :role_id, email = :email, first_name = :first_name,
                 last_name = :last_name, phone = :phone, status = :status'
             . $passwordSql .
            ' WHERE id = :id'
        );
        $update->execute($parameters);
    }

    $_SESSION['admin_users_feedback'] = [
        'type' => 'success',
        'message' => $action === 'create'
            ? 'The user account was created.'
            : 'The user account was updated.',
    ];
    header("Location: {$redirect}", true, 303);
    exit;
} catch (PDOException $exception) {
    if ($database instanceof PDO && $database->inTransaction()) {
        $database->rollBack();
    }

    if (($exception->errorInfo[1] ?? null) === 1062) {
        $fail('That email address is already assigned to another account.');
    }

    error_log('Admin user management database error: ' . $exception->getMessage());
    $fail('The user change could not be completed. Please try again later.');
}
