<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nested Multi-Section Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: linear-gradient(180deg, #eef4ff 0%, #f9fbff 55%, #ffffff 100%);
            --panel-border: rgba(13, 110, 253, 0.12);
            --panel-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        body {
            min-height: 100vh;
            background: var(--page-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

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
</head>
<body>
    <main class="container py-4 py-lg-5">
        <div class="form-shell mx-auto">
            <section class="card hero-panel border-0 rounded-4 mb-4 mb-lg-5">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row gap-4 justify-content-between align-items-lg-center">
                        <div>
                            <span class="badge rounded-pill text-bg-primary-subtle text-primary px-3 py-2 mb-3">
                                Responsive Bootstrap 5 Form
                            </span>
                            <h1 class="display-6 fw-bold mb-3">Nested multi-section form UI</h1>
                            <p class="text-secondary mb-0">
                                Section A contains nested Section B1, Section C1, Section C2, and Section B2 in a clean layout that scales from mobile to desktop.
                            </p>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-4 p-4 text-center">
                            <i class="bi bi-ui-checks-grid fs-1 d-block mb-2"></i>
                            <strong>Required fields + responsive grid</strong>
                        </div>
                    </div>
                </div>
            </section>

            <form class="needs-validation" novalidate>
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
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
    </script>
</body>
</html>
