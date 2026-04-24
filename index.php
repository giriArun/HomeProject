<?php
$stats = [
    [
        'label' => 'Total Revenue',
        'value' => '$24,780',
        'change' => '+12.4%',
        'icon' => 'bi-currency-dollar',
        'tone' => 'success',
    ],
    [
        'label' => 'New Orders',
        'value' => '1,248',
        'change' => '+8.1%',
        'icon' => 'bi-bag-check',
        'tone' => 'primary',
    ],
    [
        'label' => 'Open Tickets',
        'value' => '56',
        'change' => '-3.2%',
        'icon' => 'bi-life-preserver',
        'tone' => 'warning',
    ],
    [
        'label' => 'Team Members',
        'value' => '34',
        'change' => '+2 new',
        'icon' => 'bi-people',
        'tone' => 'info',
    ],
];

$projects = [
    ['name' => 'Redesign System', 'owner' => 'Ava Martinez', 'status' => 'In Progress', 'progress' => 74],
    ['name' => 'Client Portal', 'owner' => 'Liam Johnson', 'status' => 'Review', 'progress' => 88],
    ['name' => 'Sales Report API', 'owner' => 'Olivia Smith', 'status' => 'Planning', 'progress' => 41],
    ['name' => 'Mobile Admin App', 'owner' => 'Noah Wilson', 'status' => 'On Track', 'progress' => 63],
];

$activities = [
    ['title' => 'Quarterly sales report generated', 'time' => '8 minutes ago'],
    ['title' => '3 new support tickets assigned', 'time' => '25 minutes ago'],
    ['title' => 'Marketing budget approved', 'time' => '1 hour ago'],
    ['title' => 'Inventory sync completed', 'time' => '2 hours ago'],
];

function progressTone(int $progress): string
{
    if ($progress >= 80) {
        return 'success';
    }

    if ($progress >= 60) {
        return 'primary';
    }

    return 'warning';
}
    date_default_timezone_set('Asia/Kolkata');
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'components/head.php'; ?>
</head>
<body>
    <div class="sidebar-backdrop" data-sidebar-close></div>
    <div class="admin-shell">
        <?php include 'components/sidebar.php'; ?>


        <main class="main-content">

            <?php
                if (isset($_SESSION['action'])) {

                    if ($_SESSION['action'] !== 'login') {
                        include 'components/header.php';
                    }

                    switch ($_SESSION['action']) {
                        case 'dashboard_1':
                            include 'pages/dashboard.php'; //todo: need modification to load dashboard for admin and non-admin users
                            break;
                        case 'login':
                            include 'pages/login.php';
                            break;
                        case 'logout':
                            /* session_unset();
                            session_destroy();
                            header('Location: index.php'); */
                            exit;
                        case 'users':
                            include 'pages/users.php';
                            break;
                        case 'user_access':
                            include 'pages/user_access.php';
                            break;
                        case 'projects':
                            include 'pages/projects.php';
                            break;
                        case 'add_edit_project':
                            include 'pages/add_edit_project.php';
                            break;
                        case 'add_edit_report':
                            include 'pages/add_edit_report.php';
                            break;

                        default:
                            ?>

                                <section class="hero-card">
                                    <div>
                                        <span class="hero-tag">Live Metrics</span>
                                        <h3>Admin panel built for quick decisions</h3>
                                        <p>Track revenue, monitor tasks, and keep your team aligned with a clean Bootstrap 5 layout.</p>
                                    </div>
                                    <div class="hero-chart">
                                        <div class="chart-bars">
                                            <span style="height: 42%"></span>
                                            <span style="height: 68%"></span>
                                            <span style="height: 58%"></span>
                                            <span style="height: 82%"></span>
                                            <span style="height: 74%"></span>
                                            <span style="height: 94%"></span>
                                        </div>
                                        <small>Performance trend</small>
                                    </div>
                                </section>

                                <section class="stats-grid">
                                    <?php foreach ($stats as $stat): ?>
                                        <article class="card stat-card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="stat-header">
                                                    <span class="stat-icon bg-<?= htmlspecialchars($stat['tone']) ?>-subtle text-<?= htmlspecialchars($stat['tone']) ?>">
                                                        <i class="bi <?= htmlspecialchars($stat['icon']) ?>"></i>
                                                    </span>
                                                    <span class="stat-change text-<?= htmlspecialchars($stat['tone']) ?>"><?= htmlspecialchars($stat['change']) ?></span>
                                                </div>
                                                <p class="stat-label"><?= htmlspecialchars($stat['label']) ?></p>
                                                <h3 class="stat-value"><?= htmlspecialchars($stat['value']) ?></h3>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </section>

                                <section class="content-grid">
                                    <article class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="section-head">
                                                <div>
                                                    <p class="section-label">Projects</p>
                                                    <h3 class="section-title">Active workflow</h3>
                                                </div>
                                                <button class="btn btn-outline-dark btn-sm">View All</button>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table align-middle admin-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Project</th>
                                                            <th>Owner</th>
                                                            <th>Status</th>
                                                            <th>Progress</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($projects as $project): ?>
                                                            <tr>
                                                                <td class="fw-semibold"><?= htmlspecialchars($project['name']) ?></td>
                                                                <td><?= htmlspecialchars($project['owner']) ?></td>
                                                                <td>
                                                                    <span class="badge text-bg-light border"><?= htmlspecialchars($project['status']) ?></span>
                                                                </td>
                                                                <td>
                                                                    <div class="progress progress-thin">
                                                                        <div class="progress-bar bg-<?= progressTone($project['progress']) ?>" role="progressbar" style="width: <?= (int) $project['progress'] ?>%" aria-valuenow="<?= (int) $project['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <small class="text-body-secondary"><?= (int) $project['progress'] ?>%</small>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="section-head">
                                                <div>
                                                    <p class="section-label">Activity</p>
                                                    <h3 class="section-title">Recent updates</h3>
                                                </div>
                                            </div>

                                            <div class="activity-list">
                                                <?php foreach ($activities as $activity): ?>
                                                    <div class="activity-item">
                                                        <span class="activity-dot"></span>
                                                        <div>
                                                            <p class="mb-1 fw-semibold"><?= htmlspecialchars($activity['title']) ?></p>
                                                            <small class="text-body-secondary"><?= htmlspecialchars($activity['time']) ?></small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="mini-card">
                                                <p class="section-label mb-2">Team efficiency</p>
                                                <h4>87%</h4>
                                                <p class="mb-0 text-body-secondary">Your operations score improved by 6% this week.</p>
                                            </div>
                                        </div>
                                    </article>
                                </section>

                            <?php
                    }
                }
            ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
