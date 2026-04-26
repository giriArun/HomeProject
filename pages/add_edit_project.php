
<?php
    $project = $result['project'] ?? null;
    $project_detail = $result['detail'] ?? null;
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
                        <div class="card-body row g-3">
                            <?php if ((int) ($project['project_id'] ?? 0) > 0): ?>
                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>">
                            <?php endif; ?>

                            <div class="form-group col-md-6 mb-3">
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
                            <div class="form-group col-md-6 mb-3">
                                <label for="project_status">Project Status <span class="text-danger">*</span></label>
                                <select id="project_status" name="project_status" class="form-control" required>
                                    <option value="status_change|1" <?=((int) ($project['is_active'] ?? 0) === 1 ? 'selected' : '')?>>Active</option>
                                    <option value="status_change|0" <?=((int) ($project['is_active'] ?? 0) === 0 ? 'selected' : '')?>>InActive</option>
                                    <option value="renewal|0">Renewal</option>
                                    <option value="cost_update|0">Cost Update</option>
                                    <option value="date_change|0">Date Change</option>
                                </select>
                                <small class="form-text text-muted">Project status must be select.</small>
                            </div>

                            <div class="form-group col-md-4 mb-3" id="startYearGroup">
                                <label for="project_start_date">Project Start Date <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="project_start_date" 
                                    name="project_start_date" 
                                    placeholder="Enter project start date"
                                    value="<?= htmlspecialchars((string) ($project_detail['latest_dates']['start_date'] ?? '')) ?>"
                                >
                                <small class="form-text text-muted">Project start date must be a valid date.</small>
                            </div>

                            <div class="form-group col-md-4 mb-3" id="endYearGroup">
                                <label for="project_end_date">Project End Date <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="project_end_date" 
                                    name="project_end_date" 
                                    placeholder="Enter project end date"
                                    value="<?= htmlspecialchars((string) ($project_detail['latest_dates']['end_date'] ?? '')) ?>"
                                >
                                <small class="form-text text-muted">Project end date must be a valid date.</small>
                            </div>

                            <div class="form-group col-md-4 mb-3" id="costUpdateGroup">
                                <label for="project_cost">Price Update <span class="text-danger">*</span></label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="project_cost" 
                                    name="project_cost" 
                                    placeholder="Enter project cost update"
                                    value="<?= htmlspecialchars((string) ($project_detail['latest_cost']['new_cost'] ?? '')) ?>"
                                >
                                <small class="form-text text-muted">Project cost must be a valid number.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="project_detail">Project Details</label>
                                <textarea 
                                    class="form-control" 
                                    id="project_detail" 
                                    name="project_detail" 
                                    placeholder="Enter project details (optional)"
                                    rows="5"
                                ></textarea>
                                <small class="form-text text-muted">Add a detailed description of the project with in 255 characters.</small>
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
    function toggleYearFields() {
        const status = document.getElementById('project_status').value;
        const startYearGroup = document.getElementById('startYearGroup');
        const endYearGroup = document.getElementById('endYearGroup');
        const costUpdateGroup = document.getElementById('costUpdateGroup');
        const startYearInput = document.getElementById('project_start_date');
        const endYearInput = document.getElementById('project_end_date');
        const costUpdateInput = document.getElementById('project_cost');
        const isDateChange = status.startsWith('date_change');
        const isRenewal = status.startsWith('renewal');
        const isCostUpdate = status.startsWith('cost_update');
        
        if (isDateChange || isRenewal) {
            startYearGroup.classList.remove('d-none');
            endYearGroup.classList.remove('d-none');
            startYearInput.setAttribute('required', 'required');
            endYearInput.setAttribute('required', 'required');

            if (isRenewal) {
                costUpdateGroup.classList.remove('d-none');
                costUpdateInput.setAttribute('required', 'required');
            }else {
                costUpdateGroup.classList.add('d-none');
                costUpdateInput.removeAttribute('required');
            }
        } else if (isCostUpdate) {
            costUpdateGroup.classList.remove('d-none');
            costUpdateInput.setAttribute('required', 'required');
            
            startYearGroup.classList.add('d-none');
            endYearGroup.classList.add('d-none');
            startYearInput.removeAttribute('required');
            endYearInput.removeAttribute('required');                  
        } else {
            startYearGroup.classList.add('d-none');
            endYearGroup.classList.add('d-none');
            costUpdateGroup.classList.add('d-none');
            startYearInput.removeAttribute('required');
            endYearInput.removeAttribute('required');
            costUpdateInput.removeAttribute('required');
        }
    }

    document.getElementById('project_status').addEventListener('change', toggleYearFields);
    
    // Initial check on page load
    toggleYearFields();

    document.getElementById('projectForm').addEventListener('submit', function(e) {
        // Basic client-side validation
        const projectName = document.getElementById('project_name').value.trim();
        const status = document.getElementById('project_status').value;
        const isDateChange = status.startsWith('date_change');
        const isRenewal = status.startsWith('renewal');
        const startDate = document.getElementById('project_start_date').value.trim();
        const endDate = document.getElementById('project_end_date').value.trim();

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

        if (isDateChange || isRenewal) {
            if (!startDate) {
                e.preventDefault();
                alert('Project start date is required.');
                return false;
            }

            if (!endDate) {
                e.preventDefault();
                alert('Project end date is required.');
                return false;
            }

            const start = new Date(startDate);
            const end = new Date(endDate);

            if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                e.preventDefault();
                alert('Please enter valid project start and end dates.');
                return false;
            }

            if (end < start) {
                e.preventDefault();
                alert('Project end date cannot be earlier than the start date.');
                return false;
            }
        }
    });
</script>
