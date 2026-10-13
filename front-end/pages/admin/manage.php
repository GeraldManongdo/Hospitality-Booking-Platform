<?php
$adminSections = require __DIR__ . '/includes/sections.php';

$sectionKey = $_GET['section'] ?? 'properties';

if (!isset($adminSections[$sectionKey])) {
    $sectionKey = 'properties';
}

$section = $adminSections[$sectionKey];
$pageTitle = $section['title'];
$activeSection = $sectionKey;

require __DIR__ . '/includes/header.php';

$fields = array_slice($section['columns'], 1, 2);
?>

<main class="col-md-9 col-xl-10 admin-main">
    <div
        class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"
    >
        <div>
            <span class="eyebrow">ADMIN · SAMPLE DATA</span>

            <h1 class="h2 fw-bold mt-2 mb-1">
                <?= htmlspecialchars($section['title']) ?>
            </h1>

            <p class="text-secondary mb-0">
                <?= htmlspecialchars($section['description']) ?>
            </p>
        </div>

        <button
            class="btn btn-primary"
            type="button"
            id="toggle-editor"
            aria-expanded="false"
        >
            <i class="bi bi-plus-lg me-1"></i>
            <?= htmlspecialchars($section['add']) ?>
        </button>
    </div>

    <div class="data-note mb-4">
        Demonstration interface only. Changes are held in this page and are
        not saved to a service.
    </div>

    <section class="admin-card mb-4 d-none" id="record-editor">
        <h2 class="h5 fw-bold">
            Add a sample
            <?= htmlspecialchars(rtrim(strtolower($section['title']), 's')) ?>
        </h2>

        <form id="record-form" class="row g-3 needs-validation" novalidate>
            <div class="col-md-3">
                <label class="form-label" for="record-ref">
                    Reference
                </label>

                <input
                    class="form-control"
                    id="record-ref"
                    value="DEMO-NEW"
                    required
                >

                <div class="invalid-feedback">
                    Add a sample reference.
                </div>
            </div>

            <?php foreach ($fields as $index => $field): ?>
                <div class="col-md-4">
                    <label
                        class="form-label"
                        for="record-field-<?= $index ?>"
                    >
                        <?= htmlspecialchars($field) ?>
                    </label>

                    <input
                        class="form-control"
                        id="record-field-<?= $index ?>"
                        required
                    >

                    <div class="invalid-feedback">
                        Enter a sample
                        <?= htmlspecialchars(strtolower($field)) ?>.
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col-md-3">
                <label class="form-label" for="record-status">
                    Status
                </label>

                <select class="form-select" id="record-status">
                    <option>Review</option>
                    <option>Active</option>
                    <option>Pending</option>
                    <option>Published</option>
                    <option>Draft</option>
                </select>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">
                    Add to this preview
                </button>

                <span class="small text-secondary ms-2">
                    Not persisted.
                </span>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-6">
                <label for="table-search" class="visually-hidden">
                    Search <?= htmlspecialchars(strtolower($section['title'])) ?>
                </label>

                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        id="table-search"
                        class="form-control"
                        placeholder="Search this sample list"
                    >
                </div>
            </div>

            <div class="col-md-3">
                <label for="status-filter" class="visually-hidden">
                    Filter by status
                </label>

                <select id="status-filter" class="form-select">
                    <option value="">All statuses</option>
                    <option>Published</option>
                    <option>Active</option>
                    <option>Confirmed</option>
                    <option>Pending</option>
                    <option>Review</option>
                    <option>Draft</option>
                    <option>Open</option>
                    <option>Cancelled</option>
                    <option>Completed</option>
                </select>
            </div>

            <div
                class="col-md-3 text-md-end small text-secondary"
            >
                <span id="record-count">
                    <?= count($section['rows']) ?>
                </span>
                sample records
            </div>
        </div>

        <div class="table-responsive">
            <table
                class="table table-hover align-middle mb-0"
                id="admin-table"
            >
                <thead>
                    <tr>
                        <?php foreach ($section['columns'] as $column): ?>
                            <th>
                                <?= htmlspecialchars($column) ?>
                            </th>
                        <?php endforeach; ?>

                        <th>Demo action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($section['rows'] as $row): ?>
                        <tr>
                            <?php foreach ($row as $index => $cell): ?>
                                <td>
                                    <?= htmlspecialchars($cell) ?>

                                    <?= $index === count($row) - 1
                                        ? ' <span class="status-pill status-review ms-1">sample</span>'
                                        : '' ?>
                                </td>
                            <?php endforeach; ?>

                            <td>
                                <button
                                    class="btn btn-sm btn-outline-primary row-action"
                                    type="button"
                                >
                                    Cycle status
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div
            id="no-records"
            class="text-center text-secondary py-4 d-none"
        >
            <i class="bi bi-search fs-4"></i>

            <p class="mb-0 mt-2">
                No sample records match those filters.
            </p>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-3">
            <span class="small text-secondary" id="page-caption"></span>

            <div
                class="btn-group btn-group-sm"
                role="group"
                aria-label="Sample list pagination"
            >
                <button
                    class="btn btn-outline-secondary"
                    id="prev-page"
                    type="button"
                >
                    <i class="bi bi-chevron-left"></i>
                    <span class="visually-hidden">Previous page</span>
                </button>

                <button
                    class="btn btn-outline-secondary"
                    id="next-page"
                    type="button"
                >
                    <i class="bi bi-chevron-right"></i>
                    <span class="visually-hidden">Next page</span>
                </button>
            </div>
        </div>
    </section>

    <div
        class="alert-inline mt-3 d-none"
        id="admin-feedback"
        role="status"
    ></div>
</main>

<script>
    window.AdminTableConfig = {
        pageSize: 5,
        section: <?= json_encode($sectionKey) ?>,
        columns: <?= json_encode($section['columns']) ?>
    };
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>