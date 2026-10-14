<?php
declare(strict_types=1);

require __DIR__ . '/../config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/admin/manage-rooms.php', true, 303);
    exit;
}

if (
    empty($_SESSION['admin_resource_csrf_token'])
    || !hash_equals($_SESSION['admin_resource_csrf_token'], (string) ($_POST['csrf_token'] ?? ''))
) {
    adminRoomRedirect('Your session expired. Please try again.', true);
}

$database = getDatabaseConnection();
$action = (string) ($_POST['action'] ?? '');
$id = filter_var($_POST['record_id'] ?? null, FILTER_VALIDATE_INT);

try {
    if ($action === 'availability') {
        $date = (string) ($_POST['inventory_date'] ?? '');
        $available = filter_var($_POST['available_units'] ?? null, FILTER_VALIDATE_INT);
        $rateInput = trim((string) ($_POST['nightly_rate'] ?? ''));
        $roomQuery = $database->prepare('SELECT total_units FROM room_types WHERE id=:id');
        $roomQuery->execute(['id' => $id]);
        $total = $roomQuery->fetchColumn();
        if (
            !$id || $total === false || $available === false || $available < 0
            || $available > (int) $total || !adminRoomDate($date) || $date < date('Y-m-d')
            || ($rateInput !== '' && (!is_numeric($rateInput) || (float) $rateInput < 0))
        ) {
            adminRoomRedirect('Enter a valid future date, price, and availability.', true, $id ?: null);
        }
        $booked = $database->prepare(
            'SELECT COALESCE(SUM(room_count),0) FROM bookings
             WHERE room_type_id=:room AND status IN (\'pending\',\'confirmed\')
             AND check_in<=:date AND check_out>:date'
        );
        $booked->execute(['room' => $id, 'date' => $date]);
        if ($available < (int) $booked->fetchColumn()) {
            adminRoomRedirect('Availability cannot be lower than rooms already reserved for that date.', true, $id);
        }
        $save = $database->prepare(
            'INSERT INTO room_inventory (room_type_id,inventory_date,available_units,nightly_rate)
             VALUES (:room,:date,:available,:rate)
             ON DUPLICATE KEY UPDATE available_units=VALUES(available_units),
                 nightly_rate=VALUES(nightly_rate)'
        );
        $save->execute([
            'room' => $id,
            'date' => $date,
            'available' => $available,
            'rate' => $rateInput === '' ? null : round((float) $rateInput, 2),
        ]);
        adminRoomRedirect('Room availability saved.', false, $id);
    }

    if ($action === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (!$id || !in_array($status, ['active', 'inactive', 'in_review'], true)) {
            adminRoomRedirect('Choose a valid room and status.', true);
        }
        $update = $database->prepare('UPDATE room_types SET status=:status WHERE id=:id');
        $update->execute(['status' => $status, 'id' => $id]);
        adminRoomRedirect('Room status updated.');
    }

    if ($action !== 'save') {
        adminRoomRedirect('Invalid room action.', true);
    }

    $propertyId = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);
    $name = trim((string) ($_POST['name'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $guests = filter_var($_POST['max_guests'] ?? null, FILTER_VALIDATE_INT);
    $units = filter_var($_POST['total_units'] ?? null, FILTER_VALIDATE_INT);
    $rate = $_POST['nightly_rate'] ?? null;
    $currency = strtoupper(trim((string) ($_POST['currency'] ?? '')));
    $status = (string) ($_POST['status'] ?? '');

    $propertyCheck = $database->prepare('SELECT 1 FROM properties WHERE id=:id');
    $propertyCheck->execute(['id' => $propertyId]);
    if (
        !$propertyId || !$propertyCheck->fetchColumn()
        || $name === '' || mb_strlen($name) > 160 || strlen($description) > 65535
        || $guests === false || $guests < 1 || $guests > 255
        || $units === false || $units < 1 || $units > 65535
        || !is_numeric($rate) || (float) $rate < 0 || !is_finite((float) $rate)
        || !preg_match('/^[A-Z]{3}$/', $currency)
        || !in_array($status, ['active', 'inactive', 'in_review'], true)
    ) {
        adminRoomRedirect('Check room property, name, capacity, units, price, currency, and status.', true, $id, true);
    }
    if ($id) {
        $currentRoom = $database->prepare('SELECT property_id FROM room_types WHERE id=:id');
        $currentRoom->execute(['id' => $id]);
        $currentPropertyId = $currentRoom->fetchColumn();
        if ($currentPropertyId === false) {
            adminRoomRedirect('The room type no longer exists.', true, $id, true);
        }
        $bookings = $database->prepare(
            'SELECT check_in,check_out,room_count FROM bookings
             WHERE room_type_id=:id AND status IN (\'pending\',\'confirmed\')
             AND check_out>CURRENT_DATE'
        );
        $bookings->execute(['id' => $id]);
        $occupancyEvents = [];
        $today = date('Y-m-d');
        foreach ($bookings->fetchAll() as $booking) {
            $start = max($today, $booking['check_in']);
            $end = $booking['check_out'];
            $count = (int) $booking['room_count'];
            $occupancyEvents[$start] = ($occupancyEvents[$start] ?? 0) + $count;
            $occupancyEvents[$end] = ($occupancyEvents[$end] ?? 0) - $count;
        }
        ksort($occupancyEvents);
        $occupiedUnits = 0;
        foreach ($occupancyEvents as $delta) {
            $occupiedUnits += $delta;
            if ($units < $occupiedUnits) {
                adminRoomRedirect('Total units cannot be lower than pending or confirmed future reservations.', true, $id, true);
            }
        }
        if ((int) $currentPropertyId !== $propertyId) {
            $bookingCount = $database->prepare('SELECT COUNT(*) FROM bookings WHERE room_type_id=:id');
            $bookingCount->execute(['id' => $id]);
            if ((int) $bookingCount->fetchColumn() > 0) {
                adminRoomRedirect('A room type with booking history cannot be moved to another property.', true, $id, true);
            }
        }
        $maximum = $database->prepare(
            'SELECT MAX(available_units) FROM room_inventory
             WHERE room_type_id=:id AND inventory_date>=CURRENT_DATE'
        );
        $maximum->execute(['id' => $id]);
        $maxAvailability = $maximum->fetchColumn();
        if ($maxAvailability !== null && $maxAvailability !== false && $units < (int) $maxAvailability) {
            adminRoomRedirect('Units cannot be lower than configured date availability.', true, $id, true);
        }
    }

    $values = [
        'property' => $propertyId,
        'name' => $name,
        'description' => $description !== '' ? $description : null,
        'guests' => $guests,
        'units' => $units,
        'rate' => round((float) $rate, 2),
        'currency' => $currency,
        'status' => $status,
    ];
    if ($id) {
        $values['id'] = $id;
        $statement = $database->prepare(
            'UPDATE room_types SET property_id=:property,name=:name,description=:description,
             max_guests=:guests,total_units=:units,nightly_rate=:rate,currency=:currency,
             status=:status WHERE id=:id'
        );
    } else {
        $statement = $database->prepare(
            'INSERT INTO room_types (property_id,name,description,max_guests,total_units,nightly_rate,currency,status)
             VALUES (:property,:name,:description,:guests,:units,:rate,:currency,:status)'
        );
    }
    $statement->execute($values);
    $id = $id ?: (int) $database->lastInsertId();
    adminRoomRedirect('Room type saved.', false, $id);
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        adminRoomRedirect('A room with that name already exists at this property.', true, $id, true);
    }
    error_log('Admin room controller database error: ' . $exception->getMessage());
    adminRoomRedirect('Room changes could not be saved.', true, $id, true);
}

function adminRoomDate(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

function adminRoomRedirect(string $message, bool $error = false, ?int $recordId = null, bool $returnToForm = false): never
{
    $_SESSION['admin_resource_feedback'] = ['type' => $error ? 'error' : 'success', 'message' => $message];
    $location = '../../front-end/pages/admin/manage-rooms.php';
    if ($recordId) {
        $location = '../../front-end/pages/admin/edit-room.php?id=' . $recordId;
    } elseif ($returnToForm) {
        $location = '../../front-end/pages/admin/create-room.php';
    }
    header('Location: ' . $location, true, 303);
    exit;
}
