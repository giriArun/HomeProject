<?php $currentAction = $_SESSION['action'] ?? 'dashboard'; ?>
<aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-mobile-top d-lg-none">
        <button class="sidebar-close btn btn-link text-white p-0" type="button" aria-label="Close navigation" data-sidebar-close>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div>
        <div class="brand">
            <span class="brand-mark"><?= htmlspecialchars(ucfirst($_SESSION['user_name'] ?? $brand_name))[0] ?></span>
            <div>
                <h1 class="brand-title"><?= htmlspecialchars(ucwords($_SESSION['user_name'] ?? $brand_name)) ?></h1>
                <p class="brand-subtitle">Operations dashboard</p>
            </div>
        </div>

        <nav class="nav flex-column nav-pills gap-2 mt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
            <a class="nav-link <?= $currentAction === 'dashboard' ? 'active' : '' ?>" href="?action=dashboard"><i class="bi bi-grid"></i><span>Dashboard</span></a>

            <?php if ( $user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('add_edit_report', $_SESSION['permissions']))): ?>
                <a class="nav-link <?= in_array($currentAction, ['add_edit_report', 'add_edit_report_submit'], true) ? 'active' : '' ?>" href="?action=add_edit_report"><i class="bi bi-calendar3"></i><span>Daily Report</span></a>
            <?php endif; ?>

            <a class="nav-link" href="#"><i class="bi bi-graph-up-arrow"></i><span>Analytics</span></a>

            <?php if ( $user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('projects', $_SESSION['permissions']))): ?>
                <a class="nav-link <?= in_array($currentAction, ['projects', 'add_edit_project'], true) ? 'active' : '' ?>" href="?action=projects"><i class="bi bi-kanban"></i><span>Projects</span></a>
            <?php endif; ?>

            <?php if ( $user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('users', $_SESSION['permissions'])) ): ?>
                <a class="nav-link <?= in_array($currentAction, ['users', 'add_edit_user', 'user_access'], true) ? 'active' : '' ?>" href="?action=users"><i class="bi bi-people"></i><span>Users</span></a>
            <?php endif; ?>

            <?php /* if (isset($_SESSION['permissions']) && in_array('settings', $_SESSION['permissions'])): */ ?>
                <a class="nav-link" href="#"><i class="bi bi-gear"></i><span>Settings</span></a>
            <?php /* endif; */ ?>
            <a class="nav-link" href="?action=logout"><i class="bi bi-lock"></i><span>Logout</span></a>
            <?php else: ?>
            <a class="nav-link" href="?action=login"><i class="bi bi-box-arrow-in-right"></i><span>Login</span></a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="sidebar-card mt-auto">
        <span class="badge text-bg-light">Boost</span>
        <h2>Need a faster workflow?</h2>
        <p>Centralize reports, team activity, and finance metrics in one place.</p>
        <button class="btn btn-light w-100">Upgrade Plan</button>
    </div>
</aside>
