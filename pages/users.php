<?php
$users = $result['users'] ?? [];


//TODO: Remove this dummy data after database integration
if (count($users) === 0) {
    $users = [
        ['user_id' => 1, 'user_name' => 'Morgan Lee', 'user_email' => 'morgan@pulseadmin.com', 'is_admin' => 1, 'is_active' => 1, 'modified' => '2026-04-12 09:20:00'],
        ['user_id' => 2, 'user_name' => 'Ava Martinez', 'user_email' => 'ava@pulseadmin.com', 'is_admin' => 0, 'is_active' => 1, 'modified' => '2026-04-11 08:05:00'],
        ['user_id' => 3, 'user_name' => 'Noah Wilson', 'user_email' => 'noah@pulseadmin.com', 'is_admin' => 0, 'is_active' => 0, 'modified' => '2026-04-10 18:12:00'],
    ];
}

$editUser = $users[0];
?>
<?php isset($userSuccess) && print('<div class="alert alert-success" role="alert">' . $userSuccess . '</div>'); ?>
<?php isset($userError) && print('<div class="alert alert-danger" role="alert">' . $userError . '</div>'); ?>
<section class="content">
    <article class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-primary fw-bold small mb-1">User Management</p>
                    <h2 class="mb-1">User List</h2>
                    <p class="text-secondary mb-0">Add/Edit forms below are design-only preview.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark"><i class="bi bi-upload me-1"></i>Import</button>
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addEditUserModal">
                        <i class="bi bi-plus-lg me-1"></i>Add User
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $isAdmin = (int) ($user['is_admin'] ?? 0) === 1;
                                $isActive = (int) ($user['is_active'] ?? 0) === 1;
                                $tone = $isActive ? 'success' : 'warning';
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars((string) ($user['user_name'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string) ($user['user_email'] ?? '')) ?></td>
                                <td><span class="badge text-bg-light border"><?= $isAdmin ? 'Admin' : 'User' ?></span></td>
                                <td><span class="badge text-bg-<?= $tone ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></td>
                                <td class="text-secondary small"><?= htmlspecialchars((string) ($user['modified'] ?? 'N/A')) ?></td>
                                <td class="text-end">
                                    <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('add_edit_user', $_SESSION['permissions']) && !$isAdmin)): ?> 
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-primary m-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addEditUserModal" 
                                            data-user-id="<?= (int) ($user['user_id'] ?? 0) ?>" 
                                            data-user-name="<?= htmlspecialchars((string) ($user['user_name'] ?? '')) ?>" 
                                            data-user-email="<?= htmlspecialchars((string) ($user['user_email'] ?? '')) ?>" 
                                            data-user-is-admin="<?= (int) ($user['is_admin'] ?? 0) ?>" 
                                            data-user-is-active="<?= (int) ($user['is_active'] ?? 0) ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    <?php endif; ?>

                                    <?php if (($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('user_access', $_SESSION['permissions']))) && !$isAdmin): ?>
                                        <a type="button" class="btn btn-sm btn-outline-primary m-1" href="?action=user_access&user_id=<?= (int) ($user['user_id'] ?? 0) ?>">
                                            <i class="bi bi-universal-access-circle"></i> Access
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('user_delete', $_SESSION['permissions']) && !$isAdmin)): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-secondary">Showing <?= count($users) ?> users</small>
            </div>
        </div>
    </article>
</section>

<div class="modal fade" id="addEditUserModal" tabindex="-1" aria-labelledby="addEditUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="?action=add_edit_user_submit" class="needs-validation" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditUserModalLabel">Add/Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">User Name</label>
                            <input type="text" name="user_name" class="form-control" placeholder="Enter user name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="user_email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="user_password" class="form-control" placeholder="Enter password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="user_confirm_password" class="form-control" placeholder="Re-enter password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="user_role">
                                <option value="0" selected>User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="user_status">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="user_id" value="0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

