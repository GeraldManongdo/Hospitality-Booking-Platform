<nav class="nav-links" aria-label="Admin navigation">
    <a
        class="<?= $activeSection === 'dashboard' ? 'active' : '' ?>"
        href="dashboard.php"
    >
        <i class="bi bi-grid-1x2"></i>
        Overview
    </a>

    <a class="<?= $activeSection === 'users' ? 'active' : '' ?>" href="manage-users.php"><i class="bi bi-people"></i>Users</a>
    <a class="<?= $activeSection === 'roles' ? 'active' : '' ?>" href="manage-roles.php"><i class="bi bi-person-badge"></i>Roles</a>
    <a class="<?= $activeSection === 'properties' ? 'active' : '' ?>" href="manage-properties.php"><i class="bi bi-buildings"></i>Properties</a>
    <a class="<?= $activeSection === 'destinations' ? 'active' : '' ?>" href="manage-destinations.php"><i class="bi bi-geo-alt"></i>Destinations</a>
    <a class="<?= $activeSection === 'rooms' ? 'active' : '' ?>" href="manage-rooms.php"><i class="bi bi-door-open"></i>Rooms</a>
    <a class="<?= $activeSection === 'bookings' ? 'active' : '' ?>" href="manage-bookings.php"><i class="bi bi-calendar-check"></i>Bookings</a>
</nav>