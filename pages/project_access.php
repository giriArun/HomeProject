<?php
    $access = $result['project_access'] ?? null;
?>

<section class="content">
    <article class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <p class="section-label">Projects</p>
                    <h3 class="section-title"><?= is_array($access) && count($access) > 0 ? $access[0]['project_name'] : 'Unknown' ?></h3>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <form class="needs-validation" novalidate method="post" action="?action=project_access_submit">
                    <div class="col-12">
                        <label for="dateSectionA" class="form-label">Users Access</label>
                        <div class="d-flex flex-wrap">
                                <?php if (isset($access) && is_array($access) && count($access) > 0): ?>
                                    <input type="hidden" name="project_id" value="<?= $access[0]['project_id'] ?>">
                                    <?php foreach ($access as $user): ?>
                                        <div class="border border-secondary rounded p-1 m-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" id="user<?= $user['user_id'] ?>" value="<?= $user['user_id'] ?>|<?= $user['project_users_id'] ?>|<?= $user['project_id'] ?>" <?=(isset($user['is_assigned']) && $user['is_assigned'] === 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="user<?= $user['user_id'] ?>">
                                                    <?= $user['user_name'] ?> (<?= $user['user_email'] ?>)
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <div class="invalid-feedback">Please select at least one user.</div>
                    </div>
                    <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                Save Changes
                            </button>
                            <a href="?action=projects" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                </form>
            </div>
        </div>
    </article>
</section>