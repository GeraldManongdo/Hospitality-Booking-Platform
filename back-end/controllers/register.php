<?php
declare(strict_types=1);

session_start();

$registerPage = '../../front-end/pages/public/register.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$registerPage}", true, 303);
    exit;
}

$values = [
    'first_name' => trim((string) ($_POST['first_name'] ?? '')),
    'last_name' => trim((string) ($_POST['last_name'] ?? '')),
    'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
    'phone' => trim((string) ($_POST['phone'] ?? '')),
];

$fail = static function (string $message) use ($registerPage, $values): never {
    $_SESSION['registration_feedback'] = [
        'type' => 'error',
        'message' => $message,
    ];
    $_SESSION['registration_values'] = $values;
    header("Location: {$registerPage}", true, 303);
    exit;
};

$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['registration_csrf_token'] ?? '');
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    $fail('Your session has expired. Please try submitting the form again.');
}

if (
    $values['first_name'] === ''
    || mb_strlen($values['first_name']) > 100
    || $values['last_name'] === ''
    || mb_strlen($values['last_name']) > 100
) {
    $fail('Enter a first and last name of no more than 100 characters each.');
}

if (strlen($values['email']) > 254 || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $fail('Enter a valid email address.');
}

if (
    strlen($values['phone']) < 7
    || strlen($values['phone']) > 32
    || !preg_match('/^[0-9+ .()\-]+$/', $values['phone'])
) {
    $fail('Enter a valid phone number.');
}

$password = (string) ($_POST['password'] ?? '');
$passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');
if (strlen($password) < 8 || strlen($password) > 255) {
    $fail('Use a password between 8 and 255 characters.');
}

if (!hash_equals($password, $passwordConfirmation)) {
    $fail('The passwords do not match.');
}

if (($_POST['terms'] ?? '') !== '1') {
    $fail('Please accept the terms to create an account.');
}

require __DIR__ . '/../config/database.php';

try {
    $database = getDatabaseConnection();
    $roleQuery = $database->prepare('SELECT id FROM roles WHERE name = :name');
    $roleQuery->execute(['name' => 'customer']);
    $roleId = $roleQuery->fetchColumn();

    if ($roleId === false) {
        error_log('Customer role is missing; registration cannot create an account.');
        $fail('Registration is temporarily unavailable. Please try again later.');
    }

    $insertUser = $database->prepare(
        'INSERT INTO users (role_id, email, password_hash, first_name, last_name, phone)
         VALUES (:role_id, :email, :password_hash, :first_name, :last_name, :phone)'
    );
    $insertUser->execute([
        'role_id' => $roleId,
        'email' => $values['email'],
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'first_name' => $values['first_name'],
        'last_name' => $values['last_name'],
        'phone' => $values['phone'],
    ]);
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        $fail('An account with this email address already exists. Sign in or use another email.');
    }

    error_log('Account registration database error: ' . $exception->getMessage());
    $fail('Registration is temporarily unavailable. Please try again later.');
}

unset($_SESSION['registration_csrf_token'], $_SESSION['registration_values']);
$_SESSION['registration_feedback'] = [
    'type' => 'success',
    'message' => 'Your account has been created successfully.',
];
header("Location: {$registerPage}", true, 303);
exit;
