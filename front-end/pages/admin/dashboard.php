<?php
declare(strict_types=1);

require __DIR__ . '/../../../back-end/config/admin-auth.php';
requireAdministrator();
require_once __DIR__ . '/../../../back-end/config/database.php';

$databaseError = null;
$metrics = null;
$weeklyBookings = [];
$recentBookings = [];

try {
    $database = getDatabaseConnection();
    $metrics = $database->query(
        'SELECT
            (SELECT COUNT(*) FROM bookings) AS booking_count,
            (SELECT COALESCE(SUM(amount),0) FROM payments
             WHERE payment_type=\'charge\' AND status=\'completed\' AND currency=\'PHP\') AS revenue_php,
            (SELECT COUNT(*) FROM properties WHERE status=\'published\') AS property_count,
            (SELECT COUNT(*) FROM users WHERE status=\'active\') AS active_user_count'
    )->fetch();

    $weeklyQuery = $database->query(
        'SELECT DATE(created_at) AS booking_day,COUNT(*) AS booking_count
         FROM bookings
         WHERE created_at>=DATE_SUB(CURDATE(),INTERVAL 6 DAY)
           AND created_at<DATE_ADD(CURDATE(),INTERVAL 1 DAY)
         GROUP BY DATE(created_at)'
    );
    foreach ($weeklyQuery->fetchAll() as $day) {
        $weeklyBookings[$day['booking_day']] = (int) $day['booking_count'];
    }

    $recentBookings = $database->query(
        'SELECT bookings.id,bookings.reference,bookings.guest_first_name,bookings.guest_last_name,
                properties.name AS property_name,bookings.check_in,bookings.check_out,
                bookings.status,bookings.total_amount,bookings.currency
         FROM bookings INNER JOIN properties ON properties.id=bookings.property_id
         ORDER BY bookings.created_at DESC LIMIT 5'
    )->fetchAll();
} catch (PDOException $exception) {
    error_log('Admin dashboard database error: ' . $exception->getMessage());
    $databaseError = 'Dashboard data could not be loaded. Check the database connection and try again.';
}

$chartDays = [];
for ($offset = 6; $offset >= 0; $offset--) {
    $day = new DateTimeImmutable('-' . $offset . ' days');
    $chartDays[] = [
        'date' => $day->format('Y-m-d'),
        'label' => $day->format('D'),
        'count' => $weeklyBookings[$day->format('Y-m-d')] ?? 0,
    ];
}
$maxBookings = max(1, ...array_column($chartDays, 'count'));
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

$pageTitle = 'Overview';
$activeSection = 'dashboard';
require __DIR__ . '/../../components/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="eyebrow">ADMIN WORKSPACE</span>
            <h1 class="h2 fw-bold mt-2 mb-1">Overview</h1>
            <p class="text-secondary mb-0">Live activity from your booking platform.</p>
        </div>
        <a class="btn btn-outline-primary" href="manage-bookings.php">
            <i class="bi bi-calendar-check me-1"></i>Manage bookings
        </a>
    </div>

    <?php if ($databaseError): ?>
        <div class="alert alert-danger" role="alert"><?= $escape($databaseError) ?></div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between">
                        <span class="small text-secondary">Total bookings</span>
                        <span class="feature-icon"><i class="bi bi-calendar-check"></i></span>
                    </div>
                    <div class="metric-value mt-2"><?= number_format((int) $metrics['booking_count']) ?></div>
                    <a class="small" href="manage-bookings.php">View reservations</a>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between">
                        <span class="small text-secondary">Payments collected</span>
                        <span class="feature-icon"><i class="bi bi-cash-stack"></i></span>
                    </div>
                    <div class="metric-value mt-2">PHP <?= number_format((float) $metrics['revenue_php'], 2) ?></div>
                    <span class="small text-secondary">Completed PHP charges</span>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between">
                        <span class="small text-secondary">Published stays</span>
                        <span class="feature-icon"><i class="bi bi-buildings"></i></span>
                    </div>
                    <div class="metric-value mt-2"><?= number_format((int) $metrics['property_count']) ?></div>
                    <a class="small" href="manage-properties.php">Manage properties</a>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between">
                        <span class="small text-secondary">Active users</span>
                        <span class="feature-icon"><i class="bi bi-people"></i></span>
                    </div>
                    <div class="metric-value mt-2"><?= number_format((int) $metrics['active_user_count']) ?></div>
                    <a class="small" href="manage-users.php">Manage users</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <section class="col-xl-7">
                <div class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Reservation activity</h2>
                            <p class="small text-secondary mb-0">Bookings created in the last 7 days</p>
                        </div>
                        <a class="small" href="manage-bookings.php">View bookings</a>
                    </div>
                    <div class="chart-bars" role="img" aria-label="Bookings created in the last 7 days">
                        <?php foreach ($chartDays as $day): ?>
                            <?php $barHeight = (int) round($day['count'] / $maxBookings * 100); ?>
                            <div title="<?= $day['count'] ?> bookings">
                                <span style="height:<?= $barHeight ?>%"></span>
                                <small><?= $escape($day['label']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <section class="col-xl-5">
                <div class="admin-card h-100">
                    <h2 class="h5 fw-bold">Manage platform</h2>
                    <div class="list-group list-group-flush">
                        <a href="manage-users.php" class="list-group-item px-0 d-flex justify-content-between">
                            <span><i class="bi bi-people text-teal me-2"></i>Users &amp; roles</span><i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="manage-properties.php" class="list-group-item px-0 d-flex justify-content-between">
                            <span><i class="bi bi-buildings text-teal me-2"></i>Properties</span><i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="manage-destinations.php" class="list-group-item px-0 d-flex justify-content-between">
                            <span><i class="bi bi-geo-alt text-teal me-2"></i>Destinations</span><i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="manage-rooms.php" class="list-group-item px-0 d-flex justify-content-between">
                            <span><i class="bi bi-door-open text-teal me-2"></i>Rooms &amp; availability</span><i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>
            <section class="col-12">
                <div class="admin-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Recent reservations</h2>
                        <a class="small" href="manage-bookings.php">Manage bookings</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr><th>Reference</th><th>Guest</th><th>Property</th><th>Dates</th><th>Total</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!$recentBookings): ?>
                                    <tr><td colspan="6" class="text-center text-secondary py-4">No bookings have been made yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <tr>
                                            <td><?= $escape($booking['reference']) ?></td>
                                            <td><?= $escape($booking['guest_first_name'] . ' ' . $booking['guest_last_name']) ?></td>
                                            <td><?= $escape($booking['property_name']) ?></td>
                                            <td><?= $escape($booking['check_in']) ?> – <?= $escape($booking['check_out']) ?></td>
                                            <td><?= $escape($booking['currency']) ?> <?= number_format((float) $booking['total_amount'], 2) ?></td>
                                            <td><span class="status-pill status-<?= $escape($booking['status']) ?>"><?= $escape(ucfirst($booking['status'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    <?php endif; ?>
</main>
<style>
    .chart-bars{height:210px;display:flex;align-items:end;justify-content:space-around;gap:1rem;padding:1rem 1rem .25rem;border-bottom:1px solid var(--line)}
    .chart-bars>div{height:100%;width:10%;display:flex;align-items:center;flex-direction:column;justify-content:end;gap:.5rem}
    .chart-bars span{width:100%;max-width:46px;min-height:2px;background:#83c2b8;border-radius:.5rem .5rem .15rem .15rem}
    .chart-bars small{color:var(--muted)}
</style>
<?php require __DIR__ . '/../../components/admin-footer.php'; ?>
