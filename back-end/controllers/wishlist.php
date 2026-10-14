<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$userId = filter_var($_SESSION['user']['id'] ?? null, FILTER_VALIDATE_INT);
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Sign in to save stays to your account.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        require __DIR__ . '/../config/database.php';
        $database = getDatabaseConnection();
        $query = $database->prepare(
            'SELECT wishlists.property_id FROM wishlists
             INNER JOIN properties ON properties.id=wishlists.property_id
             INNER JOIN destinations ON destinations.id=properties.destination_id
             WHERE wishlists.user_id=:user_id AND properties.status=\'published\'
               AND destinations.status=\'published\'
               AND EXISTS (SELECT 1 FROM room_types
                           WHERE room_types.property_id=properties.id AND room_types.status=\'active\')'
        );
        $query->execute(['user_id' => $userId]);
        echo json_encode(['property_ids' => array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN))]);
        exit;
    } catch (PDOException $exception) {
        error_log('Wishlist load database error: ' . $exception->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Your saved stays could not be loaded. Please try again.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo json_encode(['error' => 'This wishlist action is not supported.']);
    exit;
}

$postedToken = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['wishlist_csrf_token'] ?? '');
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'Your session expired. Refresh the page and try again.']);
    exit;
}

$action = (string) ($_POST['action'] ?? 'toggle');
if ($action === 'sync') {
    $propertyIds = $_POST['property_ids'] ?? [];
    if (!is_array($propertyIds) || count($propertyIds) > 100) {
        http_response_code(422);
        echo json_encode(['error' => 'The saved stays list is invalid.']);
        exit;
    }

    $propertyIds = array_values(array_unique(array_filter(
        array_map(static fn ($id): int => filter_var($id, FILTER_VALIDATE_INT) ?: 0, $propertyIds),
        static fn (int $id): bool => $id > 0
    )));
    try {
        require __DIR__ . '/../config/database.php';
        $database = getDatabaseConnection();
        if ($propertyIds) {
            $placeholders = [];
            $parameters = ['user_id' => $userId];
            foreach ($propertyIds as $index => $id) {
                $key = 'property_' . $index;
                $placeholders[] = ':' . $key;
                $parameters[$key] = $id;
            }
            $sync = $database->prepare(
                'INSERT IGNORE INTO wishlists (user_id,property_id)
                 SELECT :user_id,properties.id FROM properties
                 INNER JOIN destinations ON destinations.id=properties.destination_id
                 WHERE properties.id IN (' . implode(',', $placeholders) . ')
                   AND properties.status=\'published\' AND destinations.status=\'published\'
                   AND EXISTS (SELECT 1 FROM room_types
                               WHERE room_types.property_id=properties.id AND room_types.status=\'active\')'
            );
            $sync->execute($parameters);
        }
        $query = $database->prepare('SELECT property_id FROM wishlists WHERE user_id=:user_id');
        $query->execute(['user_id' => $userId]);
        echo json_encode(['property_ids' => array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN))]);
        exit;
    } catch (PDOException $exception) {
        error_log('Wishlist sync database error: ' . $exception->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Your saved stays could not be synchronized. Please try again.']);
        exit;
    }
}

$propertyId = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);
if ($action !== 'toggle' || !$propertyId || $propertyId < 1) {
    http_response_code(422);
    echo json_encode(['error' => 'Choose a valid stay to save.']);
    exit;
}

try {
    require __DIR__ . '/../config/database.php';
    $database = getDatabaseConnection();
    $propertyQuery = $database->prepare(
        'SELECT properties.id FROM properties
         INNER JOIN destinations ON destinations.id=properties.destination_id
         WHERE properties.id=:id AND properties.status=\'published\' AND destinations.status=\'published\'
           AND EXISTS (SELECT 1 FROM room_types WHERE room_types.property_id=properties.id AND room_types.status=\'active\')'
    );
    $propertyQuery->execute(['id' => $propertyId]);
    if (!$propertyQuery->fetchColumn()) {
        http_response_code(404);
        echo json_encode(['error' => 'This stay is no longer available to save.']);
        exit;
    }

    $delete = $database->prepare('DELETE FROM wishlists WHERE user_id=:user_id AND property_id=:property_id');
    $delete->execute(['user_id' => $userId, 'property_id' => $propertyId]);
    if ($delete->rowCount() > 0) {
        echo json_encode(['saved' => false]);
        exit;
    }

    $insert = $database->prepare('INSERT INTO wishlists (user_id,property_id) VALUES (:user_id,:property_id)');
    $insert->execute(['user_id' => $userId, 'property_id' => $propertyId]);
    echo json_encode(['saved' => true]);
} catch (PDOException $exception) {
    error_log('Wishlist update database error: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Your saved stays could not be updated. Please try again.']);
}
