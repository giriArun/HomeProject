
<?php
//declare(strict_types=1);

//require_once '../includes/db_connect.php';
//require_once '../controllers/report_service.php';

//$reportService = new ReportService($conn);
$report = null;
$message = '';
$isEdit = false;


// TODO: Query section - load customers, projects, users, tags from database later
$customers = $result['customers'] ?? null;
$projects = $result['projects'] ?? null;
$users = $result['users'] ?? null;
$tags = $result['tags'] ?? null;

?>
<style>
        .form-shell {
            max-width: 1120px;
        }

        .hero-panel,
        .section-panel {
            border: 1px solid var(--panel-border);
            box-shadow: var(--panel-shadow);
        }

        .section-panel {
            border-radius: 1.25rem;
        }

        .section-panel .card-header,
        .accordion-button {
            background: #fff;
        }

        .section-badge {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .accordion-button:not(.collapsed) {
            color: inherit;
            box-shadow: none;
        }

        .accordion-button:focus,
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .nested-block {
            border: 1px dashed rgba(13, 110, 253, 0.22);
            border-radius: 1rem;
        }
    </style>


<section class="content-grid">
    <article class="card section-panel border-0 mb-4 pt-3 shadow-sm">
        <form class="needs-validation" novalidate method="post" action="?action=add_edit_report_submit">
            <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['id'] ?? ''); ?>">
           
            <div class="card-header border-0 p-4 pb-0">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="section-badge bg-primary-subtle text-primary">
                            <i class="bi bi-diagram-3"></i>
                        </span>
                        <div>
                            <h2 class="h4 mb-1">Add Report</h2>
                            <p class="text-secondary mb-0 d-none d-md-block">Daily reports help track financial transactions, customer interactions, and project progress efficiently.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <small class="text-muted mt-1"><?php echo date('M d, Y H:i'); ?></small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <fieldset class="border rounded-4 p-3 p-md-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary mb-3">Price Details</legend>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="priceSectionA" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" id="priceSectionA" placeholder="Enter a price" required value="<?php echo htmlspecialchars($report['costs'] ?? ''); ?>">
                            <div class="invalid-feedback">Please enter the price.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="dateSectionA" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" id="dateSectionA" placeholder="Enter a date" required value="<?php echo htmlspecialchars($report['date'] ?? ''); ?>">
                            <div class="invalid-feedback">Please enter the date.</div>
                        </div>
                        <div class="col-12">
                            <div class="accordion" id="customerListAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customerList" aria-expanded="true" aria-controls="customerList">
                                            Customer List
                                        </button>
                                    </h2>
                                    <div id="customerList" class="accordion-collapse collapse" data-bs-parent="#customerListAccordion">
                                        <div class="accordion-body">
                                            <div class="d-flex flex-wrap">
                                                <div class="border border-secondary rounded p-1 m-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="customer" id="customer_list_0" value="0" <?php echo (($report['customer_id'] ?? null) === 0) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="customer_list_0">
                                                            None
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php foreach ($customers as $customer): ?>
                                                    <div class="border border-secondary rounded p-1 m-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="customer" id="customer_list_<?= $customer['customer_id'] ?>" value="<?= $customer['customer_id'] ?>" <?php echo (($report['customer_id'] ?? null) == $customer['customer_id']) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="customer_list_<?= $customer['customer_id'] ?>">
                                                                <?= $customer['customer_name'] ?> (<?= $customer['customer_address'] ?>)
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                
                                                <div class="border border-secondary rounded p-1 m-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="customer" id="customer_list_-1" value="-1" <?php echo (($report['customer_id'] ?? null) === -1) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="customer_list_-1">
                                                            Add new customer
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-1 d-none" id="newCustomerFields">
                                                <div class="col-md-4">
                                                    <label for="customerName" class="form-label">Customer Name</label>
                                                    <input type="text" name="customer_name" class="form-control" id="customerName" placeholder="Enter customer name">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="customerAddress" class="form-label">Customer Address</label>
                                                    <input type="text" name="customer_address" class="form-control" id="customerAddress" placeholder="Enter customer address">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="customerPhone" class="form-label">Customer Phone</label>
                                                    <input type="text" name="customer_phone" class="form-control" id="customerPhone" placeholder="Enter phone number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="priceSectionA" class="form-label">Select Project</label>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($projects as $project): ?>
                                    <div class="border border-secondary rounded p-1 m-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="project" id="project_list_<?= $project['project_id'] ?>" value="<?= $project['project_id'] ?>" required <?php echo (($report['project_id'] ?? null) == $project['project_id']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="project_list_<?= $project['project_id'] ?>">
                                                <?= $project['project_name'] ?> 
                                            </label>
                                            <div class="invalid-feedback">Please select a project.</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border rounded-4 p-3 p-md-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-success mb-3">Recent Used Tags</legend>
            
                    <div class="accordion" id="userListAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#userList" aria-expanded="true" aria-controls="userList">
                                    User List
                                </button>
                            </h2>
                            <div id="userList" class="accordion-collapse collapse" data-bs-parent="#userListAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex flex-wrap">
                                        <div class="border border-secondary rounded p-1 m-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="user" id="user_list_0" value="0" <?php echo (($report['user_id'] ?? null) === 0) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="user_list_0">
                                                    None
                                                </label>
                                            </div>
                                        </div>
                                        <?php foreach ($users as $user): ?>
                                            <div class="border border-secondary rounded p-1 m-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="user" id="user_list_<?= $user['user_id'] ?>" value="<?= $user['user_id'] ?>" <?php echo (($report['user_id'] ?? null) == $user['user_id']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="user_list_<?= $user['user_id'] ?>">
                                                        <?= $user['user_name'] ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php foreach ($projects as $project): ?>
                        <?php
                            $project_id = $project['project_id'];
                        ?>
                        <div class="row g-3" id="projectSection<?= $project_id ?>">
                            <div class="col-12">
                                <div class="d-flex flex-wrap">
                                    <?php if (isset($project['recent_tags']) && is_array($project['recent_tags'])): ?>
                                        <?php foreach ($project['recent_tags'] as $tag): ?>
                                            <div class="border border-secondary rounded p-1 m-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tags<?= $project_id ?>[]" id="tag_list<?= $tag['tag_name'] ?><?= $project_id ?>" value="<?= trim($tag['tag_name']) ?>" <?php echo (isset($report['tags']) && in_array($tag['tag_name'], $report['tags'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="tag_list<?= $tag['tag_name'] ?><?= $project_id ?>">
                                                        <?= $tag['tag_name'] ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="accordion" id="tagListAccordion<?= $project_id ?>">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#allTagsList<?= $project_id ?>" aria-expanded="true" aria-controls="allTagsList<?= $project_id ?>">
                                                More Tag List
                                            </button>
                                        </h2>
                                        <div id="allTagsList<?= $project_id ?>" class="accordion-collapse collapse" data-bs-parent="#tagListAccordion<?= $project_id ?>">
                                            <div class="accordion-body">
                                                <div class="d-flex flex-wrap">
                                                    <?php if (isset($project['other_tags'])): 
                                                        $tag_array = explode(",", $project['other_tags']);
                                                        foreach ($tag_array as $tag): ?>
                                                            <div class="border border-secondary rounded p-1 m-1">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="tags<?= $project_id ?>[]" id="tag_list<?= $tag ?><?= $project_id ?>" value="<?= trim($tag) ?>" <?php echo (isset($report['tags']) && in_array($tag, $report['tags'])) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="tag_list<?= $tag ?><?= $project_id ?>">
                                                                        <?= $tag ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                    <?php endforeach; endif; ?>
                                                    <div class="border border-secondary rounded p-1 m-1">
                                                        <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="tags<?= $project_id ?>[]" id="tag_list-1" value="-1" data-new-tag="newTagFields<?= $project_id ?>" <?php echo (isset($report['tags']) && in_array(-1, $report['tags'])) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="tag_list-1">
                                                                Other
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-1 d-none" id="newTagFields<?= $project_id ?>">
                                                    <div class="col-12">
                                                        <label for="tagName<?= $project_id ?>" class="form-label">Tag Name</label>
                                                        <input type="text" name="tagName<?= $project_id ?>" class="form-control" id="tagName<?= $project_id ?>" placeholder="Enter tag name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="border rounded-4 p-3 p-md-4">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-danger mb-3">Notes Details</legend>
                    <div class="row g-3">
                        <div class="col-12">
                            <textarea name="notes" class="form-control" rows="4" placeholder="Add your notes here..."><?php echo htmlspecialchars($report['notes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </fieldset>


                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-2">
                    <!-- <p class="text-secondary mb-0">All fields in this demo are marked as required to show Bootstrap validation behavior clearly.</p> -->
                    <button type="submit" class="btn btn-success btn-lg px-4" name="credit" value="1">
                        <i class="bi bi-arrow-down me-2"></i><i class="bi bi-currency-rupee"></i>
                        Receive
                    </button>
                    <button type="submit" class="btn btn-warning btn-lg px-4" name="credit" value="0">
                        <i class="bi bi-arrow-up me-2"></i><i class="bi bi-currency-rupee"></i>
                        Sent
                    </button>
                </div>
            </div>
            
        </form>


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
    

            <!-- <form class="needs-validation" novalidate>
                <section class="card section-panel border-0 mb-4">
                    <div class="card-header border-0 p-4 pb-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="section-badge bg-primary-subtle text-primary">
                                    <i class="bi bi-diagram-3"></i>
                                </span>
                                <div>
                                    <h2 class="h4 mb-1">Section A</h2>
                                    <p class="text-secondary mb-0">Main container for top-level form details and nested sub-sections.</p>
                                </div>
                            </div>
                            <span class="badge text-bg-light border px-3 py-2">Main Container</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <fieldset class="border rounded-4 p-3 p-md-4 mb-4">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary mb-3">Section A Details</legend>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="sectionAName" class="form-label">Primary name</label>
                                    <input type="text" class="form-control" id="sectionAName" placeholder="Enter a name" required>
                                    <div class="invalid-feedback">Please enter the primary name.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="sectionAEmail" class="form-label">Primary email</label>
                                    <input type="email" class="form-control" id="sectionAEmail" placeholder="name@example.com" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="sectionAType" class="form-label">Category</label>
                                    <select class="form-select" id="sectionAType" required>
                                        <option value="" selected disabled>Select a category</option>
                                        <option>Internal</option>
                                        <option>External</option>
                                        <option>Partner</option>
                                    </select>
                                    <div class="invalid-feedback">Please choose a category.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="sectionAOwner" class="form-label">Owner / team</label>
                                    <input type="text" class="form-control" id="sectionAOwner" placeholder="Operations team" required>
                                    <div class="invalid-feedback">Please enter the owner or team.</div>
                                </div>
                                <div class="col-12">
                                    <label for="sectionANotes" class="form-label">Overview notes</label>
                                    <textarea class="form-control" id="sectionANotes" rows="4" placeholder="Add context for Section A" required></textarea>
                                    <div class="invalid-feedback">Please add a short overview.</div>
                                </div>
                            </div>
                        </fieldset>

                        <div class="accordion" id="sectionAAccordion">
                            <div class="accordion-item border rounded-4 overflow-hidden mb-3">
                                <h2 class="accordion-header" id="headingB1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB1" aria-expanded="true" aria-controls="collapseB1">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="section-badge bg-success-subtle text-success">
                                                <i class="bi bi-folder2-open"></i>
                                            </span>
                                            <span>
                                                <span class="d-block fw-semibold">Section B1</span>
                                                <span class="small text-secondary">Contains nested Section C1 and Section C2.</span>
                                            </span>
                                        </span>
                                    </button>
                                </h2>
                                <div id="collapseB1" class="accordion-collapse collapse show" aria-labelledby="headingB1" data-bs-parent="#sectionAAccordion">
                                    <div class="accordion-body p-4">
                                        <fieldset class="border rounded-4 p-3 p-md-4 mb-4">
                                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-success mb-3">Section B1 Details</legend>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB1Title" class="form-label">Section B1 title</label>
                                                    <input type="text" class="form-control" id="sectionB1Title" placeholder="Enter title" required>
                                                    <div class="invalid-feedback">Please enter the Section B1 title.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB1Email" class="form-label">Contact email</label>
                                                    <input type="email" class="form-control" id="sectionB1Email" placeholder="contact@example.com" required>
                                                    <div class="invalid-feedback">Please enter a valid contact email.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB1Status" class="form-label">Status</label>
                                                    <select class="form-select" id="sectionB1Status" required>
                                                        <option value="" selected disabled>Select status</option>
                                                        <option>Draft</option>
                                                        <option>Active</option>
                                                        <option>Archived</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a status.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB1Lead" class="form-label">Lead person</label>
                                                    <input type="text" class="form-control" id="sectionB1Lead" placeholder="Lead name" required>
                                                    <div class="invalid-feedback">Please enter the lead person.</div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="sectionB1Description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="sectionB1Description" rows="3" placeholder="Describe Section B1" required></textarea>
                                                    <div class="invalid-feedback">Please add a description.</div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <div class="row g-4">
                                            <div class="col-12 col-xl-6">
                                                <section class="nested-block h-100 p-3 p-md-4 bg-light-subtle">
                                                    <div class="d-flex align-items-center gap-3 mb-3">
                                                        <span class="section-badge bg-info-subtle text-info">
                                                            <i class="bi bi-layers"></i>
                                                        </span>
                                                        <div>
                                                            <h3 class="h5 mb-1">Section C1</h3>
                                                            <p class="text-secondary mb-0">First nested child section under B1.</p>
                                                        </div>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label for="sectionC1Name" class="form-label">Text input</label>
                                                            <input type="text" class="form-control" id="sectionC1Name" placeholder="C1 text value" required>
                                                            <div class="invalid-feedback">Please enter a value for Section C1.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC1Email" class="form-label">Email input</label>
                                                            <input type="email" class="form-control" id="sectionC1Email" placeholder="c1@example.com" required>
                                                            <div class="invalid-feedback">Please enter a valid email for Section C1.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC1Type" class="form-label">Select dropdown</label>
                                                            <select class="form-select" id="sectionC1Type" required>
                                                                <option value="" selected disabled>Select an option</option>
                                                                <option>Option 1</option>
                                                                <option>Option 2</option>
                                                                <option>Option 3</option>
                                                            </select>
                                                            <div class="invalid-feedback">Please select an option for Section C1.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC1Notes" class="form-label">Textarea</label>
                                                            <textarea class="form-control" id="sectionC1Notes" rows="4" placeholder="Add notes for Section C1" required></textarea>
                                                            <div class="invalid-feedback">Please enter notes for Section C1.</div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <div class="col-12 col-xl-6">
                                                <section class="nested-block h-100 p-3 p-md-4 bg-light-subtle">
                                                    <div class="d-flex align-items-center gap-3 mb-3">
                                                        <span class="section-badge bg-warning-subtle text-warning">
                                                            <i class="bi bi-stack"></i>
                                                        </span>
                                                        <div>
                                                            <h3 class="h5 mb-1">Section C2</h3>
                                                            <p class="text-secondary mb-0">Second nested child section under B1.</p>
                                                        </div>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label for="sectionC2Name" class="form-label">Text input</label>
                                                            <input type="text" class="form-control" id="sectionC2Name" placeholder="C2 text value" required>
                                                            <div class="invalid-feedback">Please enter a value for Section C2.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC2Email" class="form-label">Email input</label>
                                                            <input type="email" class="form-control" id="sectionC2Email" placeholder="c2@example.com" required>
                                                            <div class="invalid-feedback">Please enter a valid email for Section C2.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC2Type" class="form-label">Select dropdown</label>
                                                            <select class="form-select" id="sectionC2Type" required>
                                                                <option value="" selected disabled>Select an option</option>
                                                                <option>Option A</option>
                                                                <option>Option B</option>
                                                                <option>Option C</option>
                                                            </select>
                                                            <div class="invalid-feedback">Please select an option for Section C2.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="sectionC2Notes" class="form-label">Textarea</label>
                                                            <textarea class="form-control" id="sectionC2Notes" rows="4" placeholder="Add notes for Section C2" required></textarea>
                                                            <div class="invalid-feedback">Please enter notes for Section C2.</div>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border rounded-4 overflow-hidden">
                                <h2 class="accordion-header" id="headingB2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB2" aria-expanded="false" aria-controls="collapseB2">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="section-badge bg-danger-subtle text-danger">
                                                <i class="bi bi-folder"></i>
                                            </span>
                                            <span>
                                                <span class="d-block fw-semibold">Section B2</span>
                                                <span class="small text-secondary">Independent sibling section inside Section A.</span>
                                            </span>
                                        </span>
                                    </button>
                                </h2>
                                <div id="collapseB2" class="accordion-collapse collapse" aria-labelledby="headingB2" data-bs-parent="#sectionAAccordion">
                                    <div class="accordion-body p-4">
                                        <fieldset class="border rounded-4 p-3 p-md-4">
                                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-danger mb-3">Section B2 Details</legend>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB2Name" class="form-label">Text input</label>
                                                    <input type="text" class="form-control" id="sectionB2Name" placeholder="Enter a value" required>
                                                    <div class="invalid-feedback">Please enter a value for Section B2.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB2Email" class="form-label">Email input</label>
                                                    <input type="email" class="form-control" id="sectionB2Email" placeholder="sectionb2@example.com" required>
                                                    <div class="invalid-feedback">Please enter a valid email for Section B2.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB2Type" class="form-label">Select dropdown</label>
                                                    <select class="form-select" id="sectionB2Type" required>
                                                        <option value="" selected disabled>Select an option</option>
                                                        <option>General</option>
                                                        <option>Priority</option>
                                                        <option>Optional</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please choose an option for Section B2.</div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="sectionB2Ref" class="form-label">Reference code</label>
                                                    <input type="text" class="form-control" id="sectionB2Ref" placeholder="REF-001" required>
                                                    <div class="invalid-feedback">Please enter the reference code.</div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="sectionB2Notes" class="form-label">Textarea</label>
                                                    <textarea class="form-control" id="sectionB2Notes" rows="4" placeholder="Add notes for Section B2" required></textarea>
                                                    <div class="invalid-feedback">Please enter notes for Section B2.</div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-2">
                            <p class="text-secondary mb-0">All fields in this demo are marked as required to show Bootstrap validation behavior clearly.</p>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-send-check me-2"></i>
                                Submit Form
                            </button>
                        </div>
                    </div>
                </section>
            </form> -->
            

    
    <script>
        (() => {
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Handle customer radio button changes
        document.querySelectorAll('input[name="customer"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const newCustomerFields = document.getElementById('newCustomerFields');
                if (this.value === '-1') {
                    newCustomerFields.classList.remove('d-none');
                } else {
                    newCustomerFields.classList.add('d-none');
                }
            });
        });

        // Handle tag checkbox changes for "Other" option
        document.querySelectorAll('input[type="checkbox"][value="-1"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const targetId = this.getAttribute('data-new-tag');
                const newTagFields = document.getElementById(targetId);
                if (this.checked) {
                    newTagFields.classList.remove('d-none');
                } else {
                    newTagFields.classList.add('d-none');
                }
            });
        });
    </script>