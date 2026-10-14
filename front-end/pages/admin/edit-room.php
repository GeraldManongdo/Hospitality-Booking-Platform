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
    exit('A valid room type ID is required.');
}
$database = getDatabaseConnection();
$query = $database->prepare('SELECT * FROM room_types WHERE id=:id');
$query->execute(['id' => $id]);
$room = $query->fetch();
if (!$room) {
    http_response_code(404);
    exit('Room type not found.');
}
$properties = $database->query('SELECT id,name FROM properties ORDER BY name')->fetchAll();
$inventoryQuery = $database->prepare('SELECT inventory_date,available_units,nightly_rate FROM room_inventory WHERE room_type_id=:id AND inventory_date>=CURRENT_DATE ORDER BY inventory_date LIMIT 60');
$inventoryQuery->execute(['id' => $id]);
$inventoryRecords = $inventoryQuery->fetchAll();
$bookingsQuery = $database->prepare('SELECT id,reference,guest_first_name,guest_last_name,check_in,check_out,status,room_count FROM bookings WHERE room_type_id=:id ORDER BY created_at DESC LIMIT 50');
$bookingsQuery->execute(['id' => $id]);
$roomBookings = $bookingsQuery->fetchAll();
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$isEditing = true;
$pageTitle = 'Edit room type';
$activeSection = 'rooms';
require __DIR__ . '/../../components/admin-room-form.php';
