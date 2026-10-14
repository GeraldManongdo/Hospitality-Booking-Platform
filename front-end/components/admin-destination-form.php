<?php
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$value = static fn (string $key, string $default = ''): string => (string) ($destination[$key] ?? $default);
require __DIR__ . '/admin-header.php';
?>
<main class="col-md-9 col-xl-10 admin-main">
    <div class="mb-4"><a href="manage-destinations.php" class="small"><i class="bi bi-arrow-left me-1"></i>Back to destinations</a><span class="eyebrow d-block mt-3">ADMIN · LOCATIONS</span><h1 class="h2 fw-bold mt-2"><?= $escape($pageTitle) ?></h1></div>
    <?php if ($feedback): ?><div class="alert-inline <?= $feedback['type'] === 'error' ? 'error' : '' ?> mb-3" role="alert"><?= $escape($feedback['message']) ?></div><?php endif; ?>
    <form class="admin-card row g-3" method="post" action="../../../back-end/controllers/admin-destinations.php">
        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['admin_resource_csrf_token']) ?>"><input type="hidden" name="action" value="save">
        <?php if ($isEditing): ?><input type="hidden" name="record_id" value="<?= (int) $destination['id'] ?>"><?php endif; ?>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination-name">Name</label><input class="form-control" id="destination-name" name="name" maxlength="160" value="<?= $escape($value('name')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination-slug">Slug</label><input class="form-control" id="destination-slug" name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="160" value="<?= $escape($value('slug')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination-country">Country / region</label><input class="form-control" id="destination-country" name="country" maxlength="100" value="<?= $escape($value('country')) ?>" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination-image">Image URL (optional)</label><input class="form-control" id="destination-image" name="image_url" type="url" maxlength="2048" value="<?= $escape($value('image_url')) ?>"></div>
        <div class="col-md-6"><label class="form-label fw-semibold" for="destination-status">Status</label><select class="form-select" id="destination-status" name="status"><?php foreach (['draft','in_review','published','archived'] as $status): ?><option value="<?= $status ?>" <?= $value('status','draft') === $status ? 'selected' : '' ?>><?= $escape(ucwords(str_replace('_',' ',$status))) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><label class="form-label fw-semibold" for="destination-description">Description</label><textarea class="form-control" id="destination-description" name="description" rows="4"><?= $escape($value('description')) ?></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit"><?= $isEditing ? 'Save destination' : 'Create destination' ?></button><a class="btn btn-outline-secondary ms-2" href="manage-destinations.php">Cancel</a></div>
    </form>
</main>
<?php require __DIR__ . '/admin-footer.php'; ?>
