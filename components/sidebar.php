<?php $currentAction = $_SESSION['action'] ?? 'dashboard'; ?>
<aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-mobile-top d-lg-none">
        <button class="sidebar-close btn btn-link text-white p-0" type="button" aria-label="Close navigation" data-sidebar-close>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div>
        <div class="brand">
            <span class="brand-mark"><?= htmlspecialchars($_SESSION['user_name'] ?? $brand_name)[0] ?></span>
            <div>
                <h1 class="brand-title"><?= htmlspecialchars($_SESSION['user_name'] ?? $brand_name) ?></h1>
                <p class="brand-subtitle">Operations dashboard</p>
            </div>
        </div>

        <nav class="nav flex-column nav-pills gap-2 mt-4">
            <a class="nav-link <?= $currentAction === 'dashboard' ? 'active' : '' ?>" href="?action=dashboard"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            <a class="nav-link" href="#"><i class="bi bi-calendar3"></i><span>Daily Report</span></a>
            <a class="nav-link" href="#"><i class="bi bi-graph-up-arrow"></i><span>Analytics</span></a>
            <a class="nav-link" href="#"><i class="bi bi-kanban"></i><span>Projects</span></a>
            <a class="nav-link <?= in_array($currentAction, ['users', 'add_edit_user', 'user_access'], true) ? 'active' : '' ?>" href="?action=users"><i class="bi bi-people"></i><span>Users</span></a>
            <a class="nav-link" href="#"><i class="bi bi-gear"></i><span>Settings</span></a>
            <a class="nav-link" href="?action=logout"><i class="bi bi-lock"></i><span>Logout</span></a>
        </nav>
    </div>

    <div class="sidebar-card mt-auto">
        <span class="badge text-bg-light">Boost</span>
        <h2>Need a faster workflow?</h2>
        <p>Centralize reports, team activity, and finance metrics in one place.</p>
        <button class="btn btn-light w-100">Upgrade Plan</button>
    </div>
</aside>
