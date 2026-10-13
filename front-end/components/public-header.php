<?php
$pageTitle = $pageTitle ?? 'Xyvora';
$activePage = $activePage ?? '';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Discover thoughtfully chosen stays and destinations with Xyvora." >

    <title><?= htmlspecialchars($pageTitle) ?> · Xyvora</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="../../assets/css/style.css" rel="stylesheet">

    <link href="../../assets/css/customer.css" rel="stylesheet" >

    <link href="../../assets/css/dashboard.css" rel="stylesheet" >
</head>
<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container">
                <a class="navbar-brand brand-mark" href="<?= $basePath ?>index.php" >
                    <span class="brand-symbol">
                        <i class="bi bi-compass-fill"></i>
                    </span> xyvora<span class="brand-period">.</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation" >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <div class="navbar-nav mx-auto gap-lg-3">
                        <a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="home.php"  > Home </a>

                        <a class="nav-link <?= $activePage === 'destinations' ? 'active' : '' ?>" href="destinations.php" > Destinations </a>

                        <a class="nav-link <?= $activePage === 'properties' ? 'active' : '' ?>" href="properties.php"  >  Find a stay </a>

                        <a class="nav-link" href="customer/bookings.php"  > My trips </a>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <a class="btn btn-link text-decoration-none text-dark" href="customer/wishlist.php" >
                            <i class="bi bi-heart me-1"></i>
                            <span class="d-lg-none">Wishlist</span>
                        </a>

                        <a class="btn btn-outline-primary" href="login.php"  > Sign in </a>

                        <a class="btn btn-primary" href="register.php" > Join Xyvora </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>