<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a
                    class="brand-mark text-white"
                    href="<?= $publicPath ?? '' ?>home.php"
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
                <a href="<?= $publicPath ?? '' ?>destinations.php">Destinations</a>
                <a href="<?= $publicPath ?? '' ?>properties.php">Stays</a>
                <a href="<?= $accountPath ?? '../customer/' ?>wishlist.php">Wishlist</a>
            </div>

            <div class="col-6 col-lg-2">
                <h2 class="footer-title">Your account</h2>
                <a href="<?= $publicPath ?? '' ?>login.php">Sign in</a>
                <a href="<?= $accountPath ?? '../customer/' ?>bookings.php">My trips</a>
                <a href="<?= $accountPath ?? '../customer/' ?>profile.php">Profile</a>
            </div>

            <div class="col-lg-3">
                <h2 class="footer-title">A little note</h2>

                <p class="small text-white-50">
                    Xyvora demo listings and reservations are stored in the
                    local application database.
                </p>

                <a href="<?= $publicPath ?? '' ?>../admin/dashboard.php">Admin</a>
                <a href="<?= $publicPath ?? '' ?>../partner/dashboard.php">Partner</a>
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
<?= $extraScripts ?? '' ?>
</body>
</html>