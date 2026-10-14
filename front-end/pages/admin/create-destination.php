<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_resource_csrf_token'])) {
    $_SESSION['admin_resource_csrf_token'] = bin2hex(random_bytes(32));
}
$destination = [];
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$isEditing = false;
$pageTitle = 'Create destination';
$activeSection = 'destinations';
require __DIR__ . '/../../components/admin-destination-form.php';
