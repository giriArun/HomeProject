<?php
$users = [
    ['name' => 'Morgan Lee', 'email' => 'morgan@pulseadmin.com', 'role' => 'Admin', 'status' => 'Active', 'last_login' => 'Today, 09:20 AM'],
    ['name' => 'Ava Martinez', 'email' => 'ava@pulseadmin.com', 'role' => 'Manager', 'status' => 'Active', 'last_login' => 'Today, 08:05 AM'],
    ['name' => 'Noah Wilson', 'email' => 'noah@pulseadmin.com', 'role' => 'Support', 'status' => 'Inactive', 'last_login' => 'Apr 10, 06:12 PM'],
    ['name' => 'Olivia Smith', 'email' => 'olivia@pulseadmin.com', 'role' => 'Editor', 'status' => 'Pending', 'last_login' => 'Apr 09, 11:30 AM'],
];
?>

<section class="content">
    <article class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-primary fw-bold small mb-1">User Management</p>
                    <h2 class="mb-1">User List</h2>
                    <p class="text-secondary mb-0">Design preview only (no backend actions).</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-dark"><i class="bi bi-upload me-1"></i>Import</button>
                    <button class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Add User</button>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Search users..." />
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>All Roles</option><option>Admin</option><option>Manager</option><option>Support</option><option>Editor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>All Status</option><option>Active</option><option>Inactive</option><option>Pending</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge text-bg-light border"><?= htmlspecialchars($user['role']) ?></span></td>
                            <td>
                                <?php $tone = $user['status'] === 'Active' ? 'success' : ($user['status'] === 'Pending' ? 'warning' : 'secondary'); ?>
                                <span class="badge text-bg-<?= $tone ?>"><?= htmlspecialchars($user['status']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($user['last_login']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i> Edit</button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-secondary">Showing 1-4 of 24 users</small>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm">Previous</button>
                    <button class="btn btn-outline-secondary btn-sm">Next</button>
                </div>
            </div>
        </div>
    </article>
</section>
