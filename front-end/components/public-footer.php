<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a
                    class="brand-mark text-white"
                    href="index.php"
                >
                    <span class="brand-symbol">
                        <i class="bi bi-compass-fill"></i>
                    </span>
                    xyvora<span class="brand-period">.</span>
                </a>

                <p class="text-white-50 mt-3">
                    Find the places that make a journey feel like yours.
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h2 class="footer-title">Discover</h2>
                <a href="destinations.php">Destinations</a>
                <a href="properties.php">Stays</a>
                <a href="customer/wishlist.php">Wishlist</a>
            </div>

            <div class="col-6 col-lg-2">
                <h2 class="footer-title">Your account</h2>
                <a href="login.php">Sign in</a>
                <a href="customer/bookings.php">My trips</a>
                <a href="customer/profile.php">Profile</a>
            </div>

            <div class="col-lg-3">
                <h2 class="footer-title">A little note</h2>

                <p class="small text-white-50">
                    Xyvora is a frontend demonstration. Search results,
                    accounts and booking steps use sample data and are not
                    connected to a booking service.
                </p>

                <a href="admin/dashboard.php">Admin demo</a>
                <a href="partner/dashboard.php">Partner demo</a>
            </div>
        </div>

        <hr class="border-light border-opacity-10">

        <div
            class="d-flex flex-column flex-md-row justify-content-between gap-2 small text-white-50"
        >
            <span>© <?= date('Y') ?> Xyvora </span>
            <span>Made for the joy of getting away.</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/app.js"></script>
</body>
</html>