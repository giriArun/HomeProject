
<?php
    $project = $result['project'] ?? null;
?>
<?php isset($projectSuccess) && print('<div class="alert alert-success" role="alert">' . $projectSuccess . '</div>'); ?>
<?php isset($projectError) && print('<div class="alert alert-danger" role="alert">' . $projectError . '</div>'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo ((int) ($project['project_id'] ?? 0) > 0) ? 'Edit Project Details' : 'Enter Project Details'; ?></h3>
                    </div>

                    <form id="projectForm" method="POST" action="?action=add_edit_project_submit" class="needs-validation" novalidate>
                        <div class="card-body">
                            <?php if ((int) ($project['project_id'] ?? 0) > 0): ?>
                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>">
                            <?php endif; ?>

                            <div class="form-group mb-3">
                                <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="project_name" 
                                    name="project_name" 
                                    placeholder="Enter project name"
                                    value="<?= htmlspecialchars((string) ($project['project_name'] ?? '')) ?>"
                                    required
                                    minlength="3"
                                    maxlength="255"
                                >
                                <small class="form-text text-muted">Project name must be 3-255 characters.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="project_start_year">Project Start Year <span class="text-danger">*</span></label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="project_start_year" 
                                    name="project_start_year" 
                                    placeholder="Enter project start year"
                                    value="<?= htmlspecialchars((string) ($project['project_start_year'] ?? '')) ?>"
                                    required
                                    min="1901"
                                    max="2155"
                                    step="1"
                                >
                                <small class="form-text text-muted">Project start year must be a 4-digit number.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="project_end_year">Project End Year <span class="text-danger">*</span></label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="project_end_year" 
                                    name="project_end_year" 
                                    placeholder="Enter project end year"
                                    value="<?= htmlspecialchars((string) ($project['project_end_year'] ?? '')) ?>"
                                    required
                                    min="1901"
                                    max="2155"
                                    step="1"
                                >
                                <small class="form-text text-muted">Project end year must be a 4-digit number.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="project_detail">Project Details</label>
                                <textarea 
                                    class="form-control" 
                                    id="project_detail" 
                                    name="project_detail" 
                                    placeholder="Enter project details (optional)"
                                    rows="5"
                                ><?= htmlspecialchars((string) ($project['project_detail'] ?? '')) ?></textarea>
                                <small class="form-text text-muted">Add a detailed description of the project.</small>
                            </div>

                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input 
                                        type="checkbox" 
                                        class="custom-control-input" 
                                        id="is_active" 
                                        name="is_active" 
                                        value="1"
                                        <?php echo ((int) ($project['is_active'] ?? 0) === 1 ? 'checked' : ''); ?>
                                    >
                                    <label class="custom-control-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                                <small class="form-text text-muted">Check to make this project active.</small>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?php echo (int) ($project['project_id'] ?? 0) > 0 ? 'Update Project' : 'Create Project'; ?>
                            </button>
                            <a href="?action=projects" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    document.getElementById('projectForm').addEventListener('submit', function(e) {
        // Basic client-side validation
        const projectName = document.getElementById('project_name').value.trim();
        const startYear = document.getElementById('project_start_year').value.trim();
        const endYear = document.getElementById('project_end_year').value.trim();
        const yearPattern = /^[0-9]{4}$/;

        if (projectName.length < 3) {
            e.preventDefault();
            alert('Project name must be at least 3 characters long.');
            return false;
        }

        if (projectName.length > 255) {
            e.preventDefault();
            alert('Project name cannot exceed 255 characters.');
            return false;
        }

        if (!yearPattern.test(startYear)) {
            e.preventDefault();
            alert('Project start year must be a 4-digit number.');
            return false;
        }

        if (!yearPattern.test(endYear)) {
            e.preventDefault();
            alert('Project end year must be a 4-digit number.');
            return false;
        }

        if (parseInt(endYear, 10) < parseInt(startYear, 10)) {
            e.preventDefault();
            alert('Project end year cannot be earlier than the start year.');
            return false;
        }
    });
</script>
