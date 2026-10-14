<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';
if (empty($_SESSION['admin_properties_csrf_token'])) {
    $_SESSION['admin_properties_csrf_token'] = bin2hex(random_bytes(32));
}
$propertyId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$propertyId) {
    http_response_code(400);
    exit('A valid property ID is required.');
}
$database = getDatabaseConnection();
$query = $database->prepare('SELECT * FROM properties WHERE id=:id');
$query->execute(['id' => $propertyId]);
$property = $query->fetch();
if (!$property) {
    http_response_code(404);
    exit('Property not found.');
}
$imageQuery = $database->prepare('SELECT image_url FROM property_images WHERE property_id=:id AND is_primary=1 LIMIT 1');
$imageQuery->execute(['id' => $propertyId]);
$imageUrl = (string) ($imageQuery->fetchColumn() ?: '');
$feedback = $_SESSION['admin_properties_feedback']['message'] ?? null;
unset($_SESSION['admin_properties_feedback']);
$isEditing = true;
$pageTitle = 'Edit property';
require __DIR__ . '/../../components/admin-property-form.php';
