<?php
declare(strict_types=1);

require __DIR__ . '/../config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../front-end/pages/admin/manage-destinations.php', true, 303);
    exit;
}

if (
    empty($_SESSION['admin_resource_csrf_token'])
    || !hash_equals($_SESSION['admin_resource_csrf_token'], (string) ($_POST['csrf_token'] ?? ''))
) {
    adminDestinationRedirect('Your session expired. Please try again.', true);
}

$database = getDatabaseConnection();
$action = (string) ($_POST['action'] ?? '');
$id = filter_var($_POST['record_id'] ?? null, FILTER_VALIDATE_INT);

try {
    if ($action === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (!$id || !in_array($status, ['draft', 'in_review', 'published', 'archived'], true)) {
            adminDestinationRedirect('Choose a valid destination and status.', true);
        }
        $query = $database->prepare('UPDATE destinations SET status=:status WHERE id=:id');
        $query->execute(['status' => $status, 'id' => $id]);
        adminDestinationRedirect('Destination status updated.');
    }

    if ($action !== 'save') {
        adminDestinationRedirect('Invalid destination action.', true);
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
    $country = trim((string) ($_POST['country'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
    $status = (string) ($_POST['status'] ?? '');
    $scheme = strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME));
    if (
        $name === '' || mb_strlen($name) > 160
        || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || strlen($slug) > 160
        || $country === '' || mb_strlen($country) > 100 || strlen($description) > 65535
        || !in_array($status, ['draft', 'in_review', 'published', 'archived'], true)
        || ($imageUrl !== '' && (strlen($imageUrl) > 2048
            || !filter_var($imageUrl, FILTER_VALIDATE_URL)
            || !in_array($scheme, ['http', 'https'], true)))
    ) {
        adminDestinationRedirect('Check the destination name, slug, region, image, and status.', true, $id, true);
    }

    $values = [
        'name' => $name,
        'slug' => $slug,
        'country' => $country,
        'description' => $description !== '' ? $description : null,
        'image_url' => $imageUrl !== '' ? $imageUrl : null,
        'status' => $status,
    ];
    if ($id) {
        $values['id'] = $id;
        $query = $database->prepare(
            'UPDATE destinations SET name=:name,slug=:slug,country=:country,
             description=:description,image_url=:image_url,status=:status WHERE id=:id'
        );
    } else {
        $query = $database->prepare(
            'INSERT INTO destinations (name,slug,country,description,image_url,status)
             VALUES (:name,:slug,:country,:description,:image_url,:status)'
        );
    }
    $query->execute($values);
    adminDestinationRedirect('Destination saved.');
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        adminDestinationRedirect('That destination slug is already in use.', true, $id, true);
    }
    error_log('Admin destination controller database error: ' . $exception->getMessage());
    adminDestinationRedirect('Destination changes could not be saved.', true, $id, true);
}

function adminDestinationRedirect(string $message, bool $error = false, ?int $formId = null, bool $returnToForm = false): never
{
    $_SESSION['admin_resource_feedback'] = ['type' => $error ? 'error' : 'success', 'message' => $message];
    $location = '../../front-end/pages/admin/manage-destinations.php';
    if ($returnToForm) {
        $location = $formId
            ? '../../front-end/pages/admin/edit-destination.php?id=' . $formId
            : '../../front-end/pages/admin/create-destination.php';
    }
    header('Location: ' . $location, true, 303);
    exit;
}
