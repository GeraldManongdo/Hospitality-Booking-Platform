<?php
declare(strict_types=1);

function requireAdministrator(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = filter_var($_SESSION['user']['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$userId) {
        http_response_code(403);
        exit('Administrator access is required.');
    }

    require_once __DIR__ . '/database.php';
    $database = getDatabaseConnection();
    $query = $database->prepare(
        'SELECT roles.name AS role, users.status
         FROM users
         INNER JOIN roles ON roles.id = users.role_id
         WHERE users.id = :id'
    );
    $query->execute(['id' => $userId]);
    $user = $query->fetch();

    if (
        $user
        && $user['role'] === 'admin'
        && in_array($user['status'], ['pending', 'active'], true)
    ) {
        $_SESSION['user']['role'] = 'admin';
        return;
    }

    unset($_SESSION['user']);
    http_response_code(403);
    exit('Administrator access is required.');
}
