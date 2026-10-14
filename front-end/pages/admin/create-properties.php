<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_properties_csrf_token'])) {
    $_SESSION['admin_properties_csrf_token'] = bin2hex(random_bytes(32));
}
$database = getDatabaseConnection();
$property = [];
$imageUrl = '';
$feedback = $_SESSION['admin_properties_feedback']['message'] ?? null;
unset($_SESSION['admin_properties_feedback']);
$isEditing = false;
$pageTitle = 'Create property';
require __DIR__ . '/../../components/admin-property-form.php';
