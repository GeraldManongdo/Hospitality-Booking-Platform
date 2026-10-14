<?php
declare(strict_types=1);

require __DIR__ . '/../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../back-end/config/database.php';

if (empty($_SESSION['admin_resource_csrf_token'])) {
    $_SESSION['admin_resource_csrf_token'] = bin2hex(random_bytes(32));
}

$definitions = [
    'destinations' => [
        'title' => 'Destination management',
        'table' => 'destinations',
        'columns' => ['name' => 'Destination', 'country' => 'Country', 'slug' => 'Slug', 'status' => 'Status'],
        'statuses' => ['draft', 'in_review', 'published', 'archived'],
        'can_create' => true,
    ],
    'rooms' => [
        'title' => 'Room management',
        'table' => 'room_types',
        'columns' => ['name' => 'Room type', 'property_name' => 'Property', 'nightly_rate' => 'Rate', 'max_guests' => 'Capacity', 'total_units' => 'Units', 'booking_count' => 'Bookings', 'status' => 'Status'],
        'statuses' => ['active', 'inactive', 'in_review'],
        'can_create' => true,
    ],
    'bookings' => [
        'title' => 'Booking management',
        'table' => 'bookings',
        'columns' => ['reference' => 'Reference', 'guest_name' => 'Guest', 'property_name' => 'Property', 'room_name' => 'Room type', 'dates' => 'Stay', 'total_amount' => 'Total', 'status' => 'Status', 'created_at' => 'Booked'],
        'statuses' => ['pending', 'confirmed', 'cancelled', 'completed'],
        'can_create' => false,
    ],
];

$module = (string) ($resourceModule ?? '');
if (!isset($definitions[$module])) {
    http_response_code(404);
    exit('The requested management module was not found.');
}

$definition = $definitions[$module];
$database = getDatabaseConnection();
$search = trim((string) ($_GET['q'] ?? ''));
$editId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$feedback = $_SESSION['admin_resource_feedback'] ?? null;
unset($_SESSION['admin_resource_feedback']);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

if ($module === 'destinations') {
    $listSql = 'SELECT * FROM destinations
        WHERE name LIKE :q1 OR country LIKE :q2 OR slug LIKE :q3
        ORDER BY name';
    $editSql = 'SELECT * FROM destinations WHERE id = :id';
} elseif ($module === 'rooms') {
    $properties = $database->query('SELECT id, name FROM properties ORDER BY name')->fetchAll();
    $propertyFilter = filter_var($_GET['property_id'] ?? null, FILTER_VALIDATE_INT);
    $listSql = 'SELECT room_types.*, properties.name AS property_name
        , (SELECT COUNT(*) FROM bookings WHERE bookings.room_type_id=room_types.id) AS booking_count
        FROM room_types INNER JOIN properties ON properties.id = room_types.property_id
        WHERE room_types.name LIKE :q1 OR properties.name LIKE :q2
        ORDER BY properties.name, room_types.name';
    if ($propertyFilter) {
        $listSql = str_replace(
            'WHERE room_types.name LIKE :q1 OR properties.name LIKE :q2',
            'WHERE (room_types.name LIKE :q1 OR properties.name LIKE :q2) AND room_types.property_id=:property_id',
            $listSql
        );
    }
    $editSql = 'SELECT * FROM room_types WHERE id = :id';
} else {
    $bookingProperties = $database->query('SELECT id,name FROM properties ORDER BY name')->fetchAll();
    $bookingRoomsQuery = $database->prepare(
        'SELECT room_types.id,room_types.property_id,room_types.name,room_types.nightly_rate,
                room_types.currency,room_types.max_guests,properties.name AS property_name
         FROM room_types INNER JOIN properties ON properties.id=room_types.property_id
         WHERE room_types.status IN (\'active\',\'in_review\')
            OR (:booking_filter_id IS NOT NULL AND room_types.id IN
                (SELECT room_type_id FROM bookings WHERE id=:booking_id AND room_type_id IS NOT NULL))
         ORDER BY properties.name,room_types.name'
    );
    $bookingRoomsQuery->execute(['booking_filter_id' => $editId, 'booking_id' => $editId]);
    $bookingRooms = $bookingRoomsQuery->fetchAll();
    $listSql = 'SELECT bookings.*, properties.name AS property_name,
        room_types.name AS room_name,
        CONCAT(bookings.guest_first_name, \' \', bookings.guest_last_name) AS guest_name
        FROM bookings INNER JOIN properties ON properties.id = bookings.property_id
        LEFT JOIN room_types ON room_types.id = bookings.room_type_id
        WHERE bookings.reference LIKE :q1 OR bookings.guest_first_name LIKE :q2
            OR bookings.guest_last_name LIKE :q3 OR bookings.guest_email LIKE :q4
            OR room_types.name LIKE :q5
        ORDER BY bookings.created_at DESC';
    $editSql = 'SELECT bookings.*, properties.name AS property_name
        FROM bookings INNER JOIN properties ON properties.id = bookings.property_id
        LEFT JOIN room_types ON room_types.id=bookings.room_type_id
        WHERE bookings.id = :id';
}

$list = $database->prepare($listSql);
$term = '%' . $search . '%';
$listParameters = [];
preg_match_all('/:q[0-9]+/', $listSql, $matches);
foreach (array_unique($matches[0]) as $placeholder) {
    $listParameters[substr($placeholder, 1)] = $term;
}
if ($module === 'rooms' && $propertyFilter) {
    $listParameters['property_id'] = $propertyFilter;
}
$list->execute($listParameters);
$records = $list->fetchAll();

$record = null;
$inventoryRecords = [];
if ($editId) {
    $edit = $database->prepare($editSql);
    $edit->execute(['id' => $editId]);
    $record = $edit->fetch();
    if (!$record) {
        http_response_code(404);
        exit('The requested record was not found.');
    }
    if ($module === 'rooms') {
        $inventory = $database->prepare(
            'SELECT inventory_date, available_units, nightly_rate
             FROM room_inventory
             WHERE room_type_id = :id AND inventory_date >= CURRENT_DATE
             ORDER BY inventory_date LIMIT 60'
        );
        $inventory->execute(['id' => $editId]);
        $inventoryRecords = $inventory->fetchAll();
        $roomBookingsQuery = $database->prepare(
            'SELECT id,reference,guest_first_name,guest_last_name,check_in,check_out,status,room_count
             FROM bookings WHERE room_type_id=:id ORDER BY created_at DESC LIMIT 50'
        );
        $roomBookingsQuery->execute(['id' => $editId]);
        $roomBookings = $roomBookingsQuery->fetchAll();
    }
}

$pageTitle = $definition['title'];
$activeSection = $module;
require __DIR__ . '/admin-header.php';

$value = static fn (string $key, string $default = ''): string =>
    (string) ($record[$key] ?? $default);
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="eyebrow">ADMIN · OPERATIONS</span>
            <h1 class="h2 fw-bold mt-2 mb-1"><?= $escape($definition['title']) ?></h1>
            <p class="text-secondary mb-0">Manage live records in the platform database.</p>
        </div>
        <?php if ($definition['can_create'] && !$record): ?>
            <a class="btn btn-primary" href="<?= $escape($resourcePage) ?>?new=1<?= $module === 'rooms' && $propertyFilter ? '&property_id=' . (int) $propertyFilter : '' ?>"><i class="bi bi-plus-lg me-1"></i>Add <?= $module === 'rooms' ? 'room type' : rtrim($module, 's') ?></a>
        <?php endif; ?>
    </div>

    <?php if ($feedback): ?>
        <div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-4" role="status"><?= $escape($feedback['message']) ?></div>
    <?php endif; ?>

    <?php if ($record || (isset($_GET['new']) && $definition['can_create'])): ?>
        <section class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><?= $record ? 'Edit record' : 'Add record' ?></h2>
                    <?php if ($record && $module === 'bookings'): ?>
                        <p class="small text-secondary mb-0"><?= $escape($record['reference']) ?> · Created <?= $escape($record['created_at']) ?></p>
                    <?php endif; ?>
                </div>
                <a class="btn btn-sm btn-outline-secondary" href="<?= $escape($resourcePage) ?>">Close</a>
            </div>
            <form class="row g-3" method="post" action="<?= $escape($resourceController) ?>">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_resource_csrf_token']) ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="module" value="<?= $escape($module) ?>">
                <?php if ($record): ?><input type="hidden" name="record_id" value="<?= (int) $record['id'] ?>"><?php endif; ?>

                <?php if ($module === 'destinations'): ?>
                    <div class="col-md-6"><label class="form-label fw-semibold">Name</label><input class="form-control" name="name" maxlength="160" value="<?= $escape($value('name')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Slug</label><input class="form-control" name="slug" maxlength="160" value="<?= $escape($value('slug')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Country / region</label><input class="form-control" name="country" maxlength="100" value="<?= $escape($value('country')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Image URL</label><input class="form-control" type="url" name="image_url" maxlength="2048" value="<?= $escape($value('image_url')) ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status"><?php foreach ($definition['statuses'] as $status): ?><option value="<?= $status ?>" <?= $value('status', 'draft') === $status ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_', ' ', $status))) ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><label class="form-label fw-semibold">Description</label><textarea class="form-control" name="description" rows="4"><?= $escape($value('description')) ?></textarea></div>
                <?php elseif ($module === 'rooms'): ?>
                    <div class="col-md-6"><label class="form-label fw-semibold">Property</label><select class="form-select" name="property_id" required><option value="">Choose property</option><?php foreach ($properties as $property): ?><option value="<?= (int) $property['id'] ?>" <?= (int) $value('property_id', (string) ($propertyFilter ?: '0')) === (int) $property['id'] ? 'selected' : '' ?>><?= $escape($property['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Room type</label><input class="form-control" name="name" maxlength="160" value="<?= $escape($value('name')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Max guests</label><input class="form-control" name="max_guests" type="number" min="1" max="255" value="<?= $escape($value('max_guests', '2')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Total units</label><input class="form-control" name="total_units" type="number" min="1" max="65535" value="<?= $escape($value('total_units', '1')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Nightly price</label><input class="form-control" name="nightly_rate" type="number" min="0" step="0.01" value="<?= $escape($value('nightly_rate', '0')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Currency</label><input class="form-control" name="currency" maxlength="3" value="<?= $escape($value('currency', 'USD')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status"><?php foreach ($definition['statuses'] as $status): ?><option value="<?= $status ?>" <?= $value('status', 'active') === $status ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_', ' ', $status))) ?></option><?php endforeach; ?></select></div>
                    <div class="col-12"><label class="form-label fw-semibold">Description</label><textarea class="form-control" name="description" rows="3"><?= $escape($value('description')) ?></textarea></div>
                <?php else: ?>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="booking-property">Property</label><select class="form-select" id="booking-property" name="property_id" required><option value="">Choose property</option><?php foreach ($bookingProperties as $bookingProperty): ?><option value="<?= (int) $bookingProperty['id'] ?>" <?= (int) $value('property_id') === (int) $bookingProperty['id'] ? 'selected' : '' ?>><?= $escape($bookingProperty['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="booking-room">Room type</label><select class="form-select" id="booking-room" name="room_type_id" required><option value="">Choose room</option><?php foreach ($bookingRooms as $bookingRoom): ?><option value="<?= (int) $bookingRoom['id'] ?>" data-property="<?= (int) $bookingRoom['property_id'] ?>" data-rate="<?= $escape((string)$bookingRoom['nightly_rate']) ?>" data-currency="<?= $escape($bookingRoom['currency']) ?>" <?= (int) $value('room_type_id') === (int) $bookingRoom['id'] ? 'selected' : '' ?>><?= $escape($bookingRoom['property_name'].' · '.$bookingRoom['name'].' · '.$bookingRoom['currency'].' '.number_format((float)$bookingRoom['nightly_rate'],2)) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Guest first name</label><input class="form-control" name="guest_first_name" maxlength="100" value="<?= $escape($value('guest_first_name')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Guest last name</label><input class="form-control" name="guest_last_name" maxlength="100" value="<?= $escape($value('guest_last_name')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Guest email</label><input class="form-control" type="email" name="guest_email" maxlength="254" value="<?= $escape($value('guest_email')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Guest phone</label><input class="form-control" name="guest_phone" maxlength="32" value="<?= $escape($value('guest_phone')) ?>"></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Check in</label><input class="form-control" type="date" name="check_in" value="<?= $escape($value('check_in')) ?>" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Check out</label><input class="form-control" type="date" name="check_out" value="<?= $escape($value('check_out')) ?>" required></div>
                    <div class="col-md-2"><label class="form-label fw-semibold">Guests</label><input class="form-control" type="number" name="guest_count" min="1" max="255" value="<?= $escape($value('guest_count')) ?>" required></div>
                    <div class="col-md-2"><label class="form-label fw-semibold">Rooms</label><input class="form-control" type="number" name="room_count" min="1" max="255" value="<?= $escape($value('room_count')) ?>" required></div>
                    <div class="col-md-2"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status"><?php foreach ($definition['statuses'] as $status): ?><option value="<?= $status ?>" <?= $value('status') === $status ? 'selected' : '' ?>><?= $escape(ucfirst($status)) ?></option><?php endforeach; ?></select></div>
                    <div class="col-12 small text-secondary">The selected room determines the property and nightly rate. Totals are recalculated from that rate and the requested dates/room count.</div>
                <?php endif; ?>

                <div class="col-12"><button class="btn btn-primary" type="submit"><?= $record ? 'Save changes' : 'Create record' ?></button></div>
            </form>
            <?php if ($module === 'bookings' && $record): ?>
                <script>
                (() => {
                  const property = document.getElementById('booking-property');
                  const room = document.getElementById('booking-room');
                  const filterRooms = () => {
                    const selectedProperty = property.value;
                    Array.from(room.options).forEach((option) => {
                      if (!option.value) return;
                      option.hidden = option.dataset.property !== selectedProperty;
                      if (option.hidden && option.selected) room.value = '';
                    });
                  };
                  property.addEventListener('change', filterRooms);
                  filterRooms();
                })();
                </script>
            <?php endif; ?>

            <?php if ($module === 'rooms' && $record): ?>
                <hr>
                <h3 class="h6 fw-bold">Set date availability</h3>
                <form class="row g-2" method="post" action="<?= $escape($resourceController) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_resource_csrf_token']) ?>">
                    <input type="hidden" name="action" value="availability">
                    <input type="hidden" name="module" value="rooms">
                    <input type="hidden" name="record_id" value="<?= (int) $record['id'] ?>">
                    <div class="col-sm-4"><label class="form-label small" for="inventory-date">Date</label><input class="form-control" id="inventory-date" type="date" name="inventory_date" min="<?= date('Y-m-d') ?>" required></div>
                    <div class="col-sm-3"><label class="form-label small" for="available-units">Available units</label><input class="form-control" id="available-units" type="number" name="available_units" min="0" max="<?= (int) $record['total_units'] ?>" required></div>
                    <div class="col-sm-3"><label class="form-label small" for="inventory-rate">Date-specific price (optional)</label><input class="form-control" id="inventory-rate" type="number" name="nightly_rate" min="0" step="0.01"></div>
                    <div class="col-sm-2 d-flex align-items-end"><button class="btn btn-outline-primary w-100" type="submit">Save date</button></div>
                </form>
                <div class="table-responsive mt-3">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Date</th><th>Available units</th><th>Date price</th></tr></thead>
                        <tbody>
                            <?php foreach ($inventoryRecords as $inventoryRecord): ?>
                                <tr>
                                    <td><?= $escape($inventoryRecord['inventory_date']) ?></td>
                                    <td><?= (int) $inventoryRecord['available_units'] ?></td>
                                    <td><?= $inventoryRecord['nightly_rate'] === null ? 'Default room price' : $escape($record['currency'] . ' ' . number_format((float) $inventoryRecord['nightly_rate'], 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$inventoryRecords): ?><tr><td colspan="3" class="text-secondary">No future date overrides configured.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <h3 class="h6 fw-bold">Bookings for this room type</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead><tr><th>Reference</th><th>Guest</th><th>Dates</th><th>Units</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($roomBookings as $roomBooking): ?>
                                <tr><td><a href="manage-bookings.php?id=<?= (int)$roomBooking['id'] ?>"><?= $escape($roomBooking['reference']) ?></a></td><td><?= $escape($roomBooking['guest_first_name'].' '.$roomBooking['guest_last_name']) ?></td><td><?= $escape($roomBooking['check_in'].' – '.$roomBooking['check_out']) ?></td><td><?= (int)$roomBooking['room_count'] ?></td><td><?= $escape(ucfirst($roomBooking['status'])) ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (!$roomBookings): ?><tr><td colspan="5" class="text-secondary">No bookings use this room type.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($module !== 'rooms' || !$record): ?>
        <section class="admin-card">
            <form class="row g-2 mb-3" method="get">
                <?php if ($module === 'rooms' && $propertyFilter): ?><input type="hidden" name="property_id" value="<?= (int) $propertyFilter ?>"><?php endif; ?>
                <div class="col-md-9"><label class="visually-hidden" for="resource-search">Search records</label><input class="form-control" id="resource-search" name="q" value="<?= $escape($search) ?>" placeholder="Search by name, guest, or reference"></div>
                <div class="col-md-3 d-flex gap-2"><button class="btn btn-outline-primary" type="submit">Search</button><a class="btn btn-outline-secondary" href="<?= $escape($resourcePage) ?><?= $module === 'rooms' && $propertyFilter ? '?property_id=' . (int) $propertyFilter : '' ?>">Clear</a></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><?php foreach ($definition['columns'] as $label): ?><th><?= $escape($label) ?></th><?php endforeach; ?><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $item): ?>
                            <tr>
                                <?php foreach ($definition['columns'] as $field => $label): ?>
                                    <td>
                                        <?php if ($field === 'dates'): ?>
                                            <?= $escape($item['check_in'] . ' – ' . $item['check_out']) ?>
                                        <?php elseif ($field === 'total_amount' || $field === 'nightly_rate'): ?>
                                            <?= $escape($item['currency'] . ' ' . number_format((float) $item[$field], 2)) ?>
                                        <?php elseif ($field === 'created_at'): ?>
                                            <?= $escape(date('M j, Y', strtotime($item[$field]))) ?>
                                        <?php elseif ($field === 'room_name'): ?>
                                            <?= $escape((string) ($item[$field] ?? 'Not assigned')) ?>
                                        <?php else: ?>
                                            <?= $escape((string) ($item[$field] ?? '')) ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if ($module === 'bookings'): ?><a class="btn btn-sm btn-outline-secondary" href="<?= $escape($resourcePage) ?>?id=<?= (int) $item['id'] ?>">View / modify</a><?php else: ?><a class="btn btn-sm btn-outline-primary" href="<?= $escape($resourcePage) ?>?id=<?= (int) $item['id'] ?>">Edit</a><?php endif; ?>
                                        <?php if ($module === 'rooms'): ?><a class="btn btn-sm btn-outline-secondary" href="<?= $escape($resourcePage) ?>?id=<?= (int) $item['id'] ?>">Availability</a><?php endif; ?>
                                        <form method="post" action="<?= $escape($resourceController) ?>" class="d-flex gap-1" <?= $module === 'bookings' ? 'onsubmit="return confirm(\'Change booking status to \' + this.elements.status.value + \'?\')"' : '' ?>>
                                            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_resource_csrf_token']) ?>">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="module" value="<?= $escape($module) ?>">
                                            <input type="hidden" name="record_id" value="<?= (int) $item['id'] ?>">
                                            <label class="visually-hidden">Change status</label>
                                            <select class="form-select form-select-sm" name="status"><?php foreach ($definition['statuses'] as $status): ?><option value="<?= $status ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_', ' ', $status))) ?></option><?php endforeach; ?></select>
                                            <button class="btn btn-sm btn-outline-primary" type="submit">Set</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$records): ?><tr><td colspan="<?= count($definition['columns']) + 1 ?>" class="text-center text-secondary py-4">No matching records.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/admin-footer.php'; ?>
