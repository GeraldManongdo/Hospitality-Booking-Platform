<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_resource_csrf_token'])) {
    $_SESSION['admin_resource_csrf_token'] = bin2hex(random_bytes(32));
}
$database = getDatabaseConnection();
$properties = $database->query('SELECT id,name FROM properties ORDER BY name')->fetchAll();
$room = ['property_id' => filter_var($_GET['property_id'] ?? null, FILTER_VALIDATE_INT) ?: null];
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$isEditing = false;
$pageTitle = 'Create room type';
$activeSection = 'rooms';
require __DIR__ . '/../../components/admin-room-form.php';
