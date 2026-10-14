<?php
session_start();

$basePath = '';

if (empty($_SESSION['registration_csrf_token'])) {
    $_SESSION['registration_csrf_token'] = bin2hex(random_bytes(32));
}

$registrationFeedback = $_SESSION['registration_feedback'] ?? null;
$registrationValues = $_SESSION['registration_values'] ?? [];
unset($_SESSION['registration_feedback'], $_SESSION['registration_values']);

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = "Create an account";

require __DIR__ . "/../../components/public-header.php";
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-6">
            <div class="booking-card p-4 p-md-5">
                <span class="eyebrow">COME ON IN</span>

                <h1 class="h2 fw-bold mt-2">
                    Make travel feel more yours.
                </h1>

                <p class="text-secondary">
                    Create an account to start planning your next stay.
                </p>

                <?php if ($registrationFeedback): ?>
                    <div
                        class="alert-inline <?= $registrationFeedback['type'] === 'error' ? 'error' : '' ?> mb-4"
                        role="<?= $registrationFeedback['type'] === 'error' ? 'alert' : 'status' ?>"
                    >
                        <?= $escape($registrationFeedback['message']) ?>
                    </div>
                <?php endif; ?>

                <form
                    action="../../../back-end/controllers/register.php"
                    method="post"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($_SESSION['registration_csrf_token']) ?>"
                    >

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label
                                for="register-first"
                                class="form-label fw-semibold"
                            >
                                First name
                            </label>

                            <input
                                id="register-first"
                                name="first_name"
                                class="form-control"
                                maxlength="100"
                                autocomplete="given-name"
                                value="<?= $escape($registrationValues['first_name'] ?? '') ?>"
                                required
                            >

                            <div class="invalid-feedback">
                                Enter your first name.
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label
                                for="register-last"
                                class="form-label fw-semibold"
                            >
                                Last name
                            </label>

                            <input
                                id="register-last"
                                name="last_name"
                                class="form-control"
                                maxlength="100"
                                autocomplete="family-name"
                                value="<?= $escape($registrationValues['last_name'] ?? '') ?>"
                                required
                            >

                            <div class="invalid-feedback">
                                Enter your last name.
                            </div>
                        </div>

                        <div class="col-12">
                            <label
                                for="register-email"
                                class="form-label fw-semibold"
                            >
                                Email address
                            </label>

                            <input
                                id="register-email"
                                name="email"
                                type="email"
                                class="form-control"
                                maxlength="254"
                                autocomplete="email"
                                value="<?= $escape($registrationValues['email'] ?? '') ?>"
                                required
                            >

                            <div class="invalid-feedback">
                                Enter a valid email address.
                            </div>
                        </div>

                        <div class="col-12">
                            <label
                                for="register-phone"
                                class="form-label fw-semibold"
                            >
                                Phone number
                            </label>

                            <input
                                id="register-phone"
                                name="phone"
                                type="tel"
                                class="form-control"
                                pattern="[0-9+ .\-]{7,32}"
                                maxlength="32"
                                autocomplete="tel"
                                value="<?= $escape($registrationValues['phone'] ?? '') ?>"
                                required
                            >

                            <div class="invalid-feedback">
                                Enter a valid phone number.
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label
                                for="register-password"
                                class="form-label fw-semibold"
                            >
                                Password
                            </label>

                            <input
                                id="register-password"
                                name="password"
                                type="password"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                            <div class="invalid-feedback">
                                Use at least 8 characters.
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label
                                for="register-confirm"
                                class="form-label fw-semibold"
                            >
                                Confirm password
                            </label>

                            <input
                                id="register-confirm"
                                name="password_confirmation"
                                data-password-confirm="register-password"
                                type="password"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                            <div class="invalid-feedback">
                                Passwords must match.
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="terms"
                                    id="terms"
                                    value="1"
                                    required
                                >

                                <label
                                    class="form-check-label small"
                                    for="terms"
                                >
                                    I agree to the terms and conditions.
                                </label>

                                <div class="invalid-feedback">
                                    Please confirm to continue.
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button
                                class="btn btn-primary w-100"
                                type="submit"
                            >
                                Create account
                            </button>
                        </div>
                    </div>

                </form>

                <p class="small text-center text-secondary mt-4 mb-0">
                    Already have an account?
                    <a href="login.php">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . "/../../components/public-footer.php"; ?>