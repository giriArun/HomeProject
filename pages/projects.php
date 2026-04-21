<?php
    $projects = $result['projects'] ?? null;
?>

<?php isset($projectSuccess) && print('<div class="alert alert-success" role="alert">' . $projectSuccess . '</div>'); ?>
<?php isset($projectError) && print('<div class="alert alert-danger" role="alert">' . $projectError . '</div>'); ?>
<section class="content">
    <article class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-primary fw-bold small mb-1">Project Management</p>
                    <h2 class="mb-1">Project List</h2>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('add_edit_project', $_SESSION['permissions']))): ?>
                         <a href="?action=add_edit_project" type="button" class="btn btn-dark">
                             <i class="bi bi-plus-lg me-1"></i>Add Project
                         </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Start Year</th>
                            <th>End Year</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <?php
                                $isActive = (int) ($project['is_active'] ?? 0) === 1;
                                $tone = $isActive ? 'success' : 'warning';
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars((string) ($project['project_name'] ?? '')) ?></td>
                                <td><span class="badge text-bg-<?= $tone ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></td>
                                <td><?= htmlspecialchars((int) ($project['project_start_year'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((int) ($project['project_end_year'] ?? '')) ?></td>
                                <td class="text-end">
                                    <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('add_edit_project', $_SESSION['permissions']))): ?> 
                                        <a type="button" class="btn btn-sm btn-outline-primary m-1" href="?action=add_edit_project&project_id=<?= (int) ($project['project_id'] ?? 0) ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('update_project_tags', $_SESSION['permissions']))): ?> 
                                        <button type="button" class="btn btn-sm btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#editTagsModal" data-project-id="<?= (int) ($project['project_id'] ?? 0) ?>" data-project-tags='<?= htmlspecialchars($project['project_tags'] ?? '') ?>'>
                                            <i class="bi bi-tags"></i> Tags
                                        </button>
                                    <?php endif; ?>

                                    <?php if (($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('user_access', $_SESSION['permissions'])))): ?>
                                        <!-- <a type="button" class="btn btn-sm btn-outline-primary m-1" href="?action=user_access&user_id=<?= (int) ($user['user_id'] ?? 0) ?>">
                                            <i class="bi bi-universal-access-circle"></i> Access
                                        </a> -->
                                    <?php endif; ?>

                                    <!-- TODO: it is under discussion, is it a good idea to delete a project? may be old history still remain -->
                                    <!-- <?php if ($user_is_admin === 1 || (isset($_SESSION['permissions']) && in_array('project_delete', $_SESSION['permissions']))): ?>
                                        <a type="button" class="btn btn-sm btn-outline-danger" href="?action=project_delete&project_id=<?= (int) ($project['project_id'] ?? 0) ?>" onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-secondary">Showing <?= count($projects) ?> projects</small>
            </div>
        </div>
    </article>
</section>


<div class="modal fade" id="editTagsModal" tabindex="-1" aria-labelledby="editTagsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="?action=update_project_tags" class="needs-validation" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTagsModalLabel">Edit Tags</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Tags</label>
                                <textarea class="form-control" name="project_tags" rows="3" placeholder="Enter tags for this project"></textarea>
                                <input type="hidden" name="project_id" value="0">
                                <span class="form-text">Comma separated tags, for example: tag1, tag2, tag3</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Update Tags</button>
                    </div>
            </form>
        </div>
    </div>
</div>
