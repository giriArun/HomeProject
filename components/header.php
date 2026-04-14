<header class="mobile-topbar d-lg-none">
    <button class="menu-toggle btn btn-dark" type="button" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false" data-sidebar-toggle>
        <i class="bi bi-list"></i>
    </button>
    <div>
        <p class="mobile-topbar-label">Workspace</p>
        <h1 class="mobile-topbar-title"><?= htmlspecialchars(ucwords($_SESSION['user_name'] ?? $brand_name)) ?></h1>
    </div>
    <div class="mobile-topbar-avatar"><?= htmlspecialchars(ucfirst($_SESSION['user_name'] ?? $brand_name))[0] ?></div>
</header>

<header class="topbar">
    <div>
        <p class="eyebrow">Overview</p>
        <h2 class="page-title">Welcome back, <?= htmlspecialchars(ucwords($_SESSION['user_name'] ?? $brand_name)) ?></h2>
        <p class="page-copy">Here’s what’s happening across your business today.</p>
    </div>

    <div class="topbar-actions">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Search anything">
        </div>
        <button class="btn btn-dark px-4">Generate Report</button>
    </div>
</header>