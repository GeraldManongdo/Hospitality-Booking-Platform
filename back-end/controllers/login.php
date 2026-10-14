<?php
declare(strict_types=1);

session_start();

$loginPage = '../../front-end/pages/public/login.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$loginPage}", true, 303);
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['login_csrf_token'] ?? '');

$fail = static function (string $message) use ($loginPage, $email): never {
    $_SESSION['login_feedback'] = $message;
    $_SESSION['login_email'] = $email;
    header("Location: {$loginPage}", true, 303);
    exit;
};

if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    $fail('Your session has expired. Please try signing in again.');
}

if (
    strlen($email) > 254
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || $password === ''
    || strlen($password) > 255
) {
    $fail('The email address or password is incorrect.');
}

require __DIR__ . '/../config/database.php';

try {
    $database = getDatabaseConnection();
    $query = $database->prepare(
        'SELECT users.id, users.email, users.password_hash, users.first_name,
                users.last_name, users.status, roles.name AS role
         FROM users
         INNER JOIN roles ON roles.id = users.role_id
         WHERE users.email = :email
         LIMIT 1'
    );
    $query->execute(['email' => $email]);
    $user = $query->fetch();

    if (
        !$user
        || !password_verify($password, $user['password_hash'])
        || !in_array($user['status'], ['pending', 'active'], true)
    ) {
        $fail('The email address or password is incorrect.');
    }

    $updateLogin = $database->prepare(
        'UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id'
    );
    $updateLogin->execute(['id' => $user['id']]);
} catch (PDOException $exception) {
    error_log('Account login database error: ' . $exception->getMessage());
    $fail('Sign-in is temporarily unavailable. Please try again later.');
}

session_regenerate_id(true);
unset($_SESSION['login_csrf_token'], $_SESSION['login_feedback'], $_SESSION['login_email']);
$_SESSION['user'] = [
    'id' => $user['id'],
    'email' => $user['email'],
    'first_name' => $user['first_name'],
    'last_name' => $user['last_name'],
    'role' => $user['role'],
];

$destination = $user['role'] === 'admin'
    ? '../../front-end/pages/admin/dashboard.php'
    : '../../front-end/pages/public/home.php';

header("Location: {$destination}", true, 303);
exit;
