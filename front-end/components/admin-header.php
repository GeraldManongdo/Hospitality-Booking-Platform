<?php
$pageTitle = $pageTitle ?? 'Admin dashboard';
$activeSection = $activeSection ?? '';
$adminName = trim((string) ($_SESSION['user']['first_name'] ?? 'Administrator'));
if (empty($_SESSION['logout_csrf_token'])) {
    $_SESSION['logout_csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · Xyvora Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <?= $extraHead ?? '' ?>
</head>
<body class="admin-layout">
    <header class="admin-topbar">
        <div
            class="container-fluid px-3 px-lg-4 py-3 d-flex justify-content-between align-items-center"
        >
            <a href="dashboard.php" class="brand-mark">
                <span class="brand-symbol">
                    <i class="bi bi-compass-fill"></i>
                </span>
                xyvora<span class="brand-period">.</span>
                <span class="small fw-semibold text-secondary ms-2">
                    ADMIN
                </span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="small text-secondary d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a
                    class="btn btn-sm btn-outline-primary"
                    href="../public/home.php"
                >
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    View site
                </a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-xl-2 admin-sidebar">
                <div
                    class="small text-secondary fw-semibold text-uppercase mb-2 px-2"
                >
                    Workspace
                </div>

                <?php require __DIR__ . '/admin-sidenav.php'; ?>
            </aside>