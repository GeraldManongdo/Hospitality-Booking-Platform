<?php
session_start();

$basePath = '';

if (empty($_SESSION['login_csrf_token'])) {
    $_SESSION['login_csrf_token'] = bin2hex(random_bytes(32));
}

$loginFeedback = $_SESSION['login_feedback'] ?? null;
$loginEmail = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_feedback'], $_SESSION['login_email']);

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Sign in';

require __DIR__ . '/../../components/public-header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="booking-card p-4 p-md-5">
                <span class="eyebrow">WELCOME BACK</span>

                <h1 class="h2 fw-bold mt-2">Sign in to Xyvora</h1>

                <p class="text-secondary">
                    Pick up right where your next journey begins.
                </p>

                <?php if ($loginFeedback): ?>
                    <div class="alert-inline error mb-4" role="alert">
                        <?= $escape($loginFeedback) ?>
                    </div>
                <?php endif; ?>

                <form
                    action="../../../back-end/controllers/login.php"
                    method="post"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($_SESSION['login_csrf_token']) ?>"
                    >

                    <div class="mb-3">
                        <label
                            for="login-email"
                            class="form-label fw-semibold"
                        >
                            Email address
                        </label>

                        <input
                            class="form-control"
                            type="email"
                            id="login-email"
                            name="email"
                            maxlength="254"
                            autocomplete="email"
                            value="<?= $escape($loginEmail) ?>"
                            required
                        >

                        <div class="invalid-feedback">
                            Enter a valid email address.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label
                            for="login-password"
                            class="form-label fw-semibold"
                        >
                            Password
                        </label>

                        <div class="input-group">
                            <input
                                class="form-control"
                                type="password"
                                id="login-password"
                                name="password"
                                minlength="8"
                                maxlength="255"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                data-toggle-password="login-password"
                                aria-label="Show password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                            <div class="invalid-feedback">
                                Enter a password of at least 8 characters.
                            </div>
                        </div>
                    </div>

                    <!-- <div
                        class="d-flex justify-content-between align-items-center mb-4"
                    >
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember"
                            >

                            <label
                                class="form-check-label small"
                                for="remember"
                            >
                                Remember me
                            </label>
                        </div>

                        <a href="login.php" class="small">
                            Forgot password?
                        </a>
                    </div> -->

                    <button class="btn btn-primary w-100" type="submit">
                        Sign in
                    </button>

                </form>

                <p class="small  text-secondary mt-4 mb-0">
                    New around here?
                    <a href="register.php">Create a account</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../../components/public-footer.php'; ?>