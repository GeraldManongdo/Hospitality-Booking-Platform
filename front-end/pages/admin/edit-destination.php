<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_resource_csrf_token'])) {
    $_SESSION['admin_resource_csrf_token'] = bin2hex(random_bytes(32));
}
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('A valid destination ID is required.');
}
$database = getDatabaseConnection();
$query = $database->prepare('SELECT * FROM destinations WHERE id=:id');
$query->execute(['id' => $id]);
$destination = $query->fetch();
if (!$destination) {
    http_response_code(404);
    exit('Destination not found.');
}
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$isEditing = true;
$pageTitle = 'Edit destination';
$activeSection = 'destinations';
require __DIR__ . '/../../components/admin-destination-form.php';
