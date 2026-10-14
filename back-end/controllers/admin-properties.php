<?php
declare(strict_types=1);

require __DIR__ . '/../config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/admin/manage-properties.php', true, 303);
    exit;
}

$redirect = '../../front-end/pages/admin/manage-properties.php';
$token = (string) ($_POST['csrf_token'] ?? '');
if (
    empty($_SESSION['admin_properties_csrf_token'])
    || !hash_equals($_SESSION['admin_properties_csrf_token'], $token)
) {
    $_SESSION['admin_properties_feedback'] = ['type' => 'error', 'message' => 'Your session expired. Please try again.'];
    header("Location: {$redirect}", true, 303);
    exit;
}

$database = getDatabaseConnection();
$action = (string) ($_POST['action'] ?? '');
$id = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);
$fail = static function (string $message, ?int $propertyId = null): never {
    $_SESSION['admin_properties_feedback'] = ['type' => 'error', 'message' => $message];
    $url = $propertyId ? '../../front-end/pages/admin/edit-properties.php?id=' . $propertyId : '../../front-end/pages/admin/create-properties.php';
    header("Location: {$url}", true, 303);
    exit;
};

try {
    if ($action === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (!$id || !in_array($status, ['draft', 'in_review', 'published', 'rejected', 'archived'], true)) {
            $fail('Choose a valid property and status.');
        }
        $statement = $database->prepare('UPDATE properties SET status = :status WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $id]);
        $_SESSION['admin_properties_feedback'] = ['type' => 'success', 'message' => 'Property status updated.'];
        header("Location: {$redirect}", true, 303);
        exit;
    }

    if ($action !== 'create' && $action !== 'update') {
        $fail('Invalid property action.');
    }
    if (($action === 'update' && !$id) || ($action === 'create' && $id)) {
        $fail('The property form is invalid.', $id ?: null);
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
    $destinationId = filter_var($_POST['destination_id'] ?? null, FILTER_VALIDATE_INT);
    $ownerId = trim((string) ($_POST['owner_user_id'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? ''));
    $propertyType = trim((string) ($_POST['property_type'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $rate = $_POST['base_nightly_rate'] ?? null;
    $currency = strtoupper(trim((string) ($_POST['currency'] ?? '')));
    $status = (string) ($_POST['status'] ?? 'draft');
    $latitude = trim((string) ($_POST['latitude'] ?? ''));
    $longitude = trim((string) ($_POST['longitude'] ?? ''));
    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

    $ownerId = $ownerId === '' ? null : filter_var($ownerId, FILTER_VALIDATE_INT);
    $latitudeValue = $latitude === '' ? null : filter_var($latitude, FILTER_VALIDATE_FLOAT);
    $longitudeValue = $longitude === '' ? null : filter_var($longitude, FILTER_VALIDATE_FLOAT);

    if (
        $name === '' || mb_strlen($name) > 180
        || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || strlen($slug) > 180
        || !$destinationId || $city === '' || mb_strlen($city) > 120
        || $country === '' || mb_strlen($country) > 100
        || $propertyType === '' || mb_strlen($propertyType) > 80
        || $description === '' || strlen($description) > 65535
        || !is_numeric($rate) || (float) $rate < 0 || !is_finite((float) $rate)
        || !preg_match('/^[A-Z]{3}$/', $currency)
        || !in_array($status, ['draft', 'in_review', 'published', 'rejected', 'archived'], true)
    ) {
        $fail('Check required fields, slug, price, currency, and status.', $id);
    }

    if (
        ($latitude !== '' && ($latitudeValue === false || $latitudeValue < -90 || $latitudeValue > 90))
        || ($longitude !== '' && ($longitudeValue === false || $longitudeValue < -180 || $longitudeValue > 180))
        || (($latitudeValue === null) !== ($longitudeValue === null))
    ) {
        $fail('Select a valid map location or clear both coordinates.', $id);
    }
    if ($ownerId !== null && $ownerId === false) {
        $fail('Choose a valid property owner.', $id);
    }
    if ($imageUrl !== '') {
        $scheme = strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME));
        if (strlen($imageUrl) > 2048 || !filter_var($imageUrl, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
            $fail('Enter a valid HTTP or HTTPS image URL.', $id);
        }
    }

    $destinationCheck = $database->prepare('SELECT 1 FROM destinations WHERE id = :id');
    $destinationCheck->execute(['id' => $destinationId]);
    if (!$destinationCheck->fetchColumn()) {
        $fail('Choose an existing destination.', $id);
    }
    if ($ownerId !== null) {
        $ownerCheck = $database->prepare(
            "SELECT 1 FROM users INNER JOIN roles ON roles.id=users.role_id
             WHERE users.id=:id AND roles.name='partner'"
        );
        $ownerCheck->execute(['id' => $ownerId]);
        if (!$ownerCheck->fetchColumn()) {
            $fail('Choose an account with the partner role as the property owner.', $id);
        }
    }

    $database->beginTransaction();
    $values = [
        'owner_user_id' => $ownerId,
        'destination_id' => $destinationId,
        'slug' => $slug,
        'name' => $name,
        'city' => $city,
        'country' => $country,
        'property_type' => $propertyType,
        'description' => $description,
        'latitude' => $latitudeValue,
        'longitude' => $longitudeValue,
        'base_nightly_rate' => round((float) $rate, 2),
        'currency' => $currency,
        'status' => $status,
    ];
    if ($action === 'create') {
        $statement = $database->prepare(
            'INSERT INTO properties
             (owner_user_id,destination_id,slug,name,city,country,property_type,description,
              latitude,longitude,base_nightly_rate,currency,status)
             VALUES
             (:owner_user_id,:destination_id,:slug,:name,:city,:country,:property_type,:description,
              :latitude,:longitude,:base_nightly_rate,:currency,:status)'
        );
        $statement->execute($values);
        $id = (int) $database->lastInsertId();
    } else {
        $values['id'] = $id;
        $statement = $database->prepare(
            'UPDATE properties SET owner_user_id=:owner_user_id,destination_id=:destination_id,
             slug=:slug,name=:name,city=:city,country=:country,property_type=:property_type,
             description=:description,latitude=:latitude,longitude=:longitude,
             base_nightly_rate=:base_nightly_rate,currency=:currency,status=:status
             WHERE id=:id'
        );
        $statement->execute($values);
    }

    if ($imageUrl !== '') {
        $image = $database->prepare(
            'SELECT id FROM property_images WHERE property_id=:property_id AND is_primary=1 LIMIT 1'
        );
        $image->execute(['property_id' => $id]);
        $imageId = $image->fetchColumn();
        if ($imageId) {
            $updateImage = $database->prepare(
                'UPDATE property_images SET image_url=:url, alt_text=:alt WHERE id=:id'
            );
            $updateImage->execute(['url' => $imageUrl, 'alt' => $name, 'id' => $imageId]);
        } else {
            $insertImage = $database->prepare(
                'INSERT INTO property_images (property_id,image_url,alt_text,is_primary)
                 VALUES (:property_id,:url,:alt,1)'
            );
            $insertImage->execute(['property_id' => $id, 'url' => $imageUrl, 'alt' => $name]);
        }
    }
    $database->commit();

    $_SESSION['admin_properties_feedback'] = [
        'type' => 'success',
        'message' => $action === 'create' ? 'Property created.' : 'Property updated.',
    ];
    header('Location: ../../front-end/pages/admin/view-properties.php?id=' . $id, true, 303);
    exit;
} catch (PDOException $exception) {
    if ($database->inTransaction()) {
        $database->rollBack();
    }
    if (($exception->errorInfo[1] ?? null) === 1062) {
        $fail('That property slug is already in use.', $id);
    }
    error_log('Admin property controller database error: ' . $exception->getMessage());
    $fail('Property changes could not be saved. Please check the database migration and try again.', $id);
}
