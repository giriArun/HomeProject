<?php
    $user = $result['user'] ?? null;
    $permissions = $result['permissions'] ?? null;
?>
<section class="content">
    <article class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="?action=user_access_submit" class="auth-form">
                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?? '' ?>">
                <div class="row g-3">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="userAccessHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#userAccessCollapse" aria-expanded="true" aria-controls="userAccessCollapse">
                                User Access Permissions
                            </button>
                            </h2>
                            <div id="userAccessCollapse" class="accordion-collapse collapse show" aria-labelledby="userAccessHeading" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php
                                        $user_permission = [
                                            ['title' => 'User List', 'key' => 'users', 'value' => ['users']],
                                            ['title' => 'Edit User Access', 'key' => 'user_access', 'value' => ['user_access', 'user_access_submit']],
                                            ['title' => 'Edit User', 'key' => 'marketing', 'value' => ['marketing']],
                                            ['title' => 'Delete User', 'key' => 'user_delete', 'value' => ['user_delete']],
                                        ];

                                        foreach ($user_permission as $permission):
                                    ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="<?= $permission['key'] ?>" name="<?= $permission['key'] ?>" <?= isset($permissions[$permission['key']]) && $permissions[$permission['key']] === '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="<?= $permission['key'] ?>"><?= $permission['title'] ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                    <input type="text" name="user_permission" value='<?= json_encode($user_permission) ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Accordion Item #2
                            </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                            </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Accordion Item #3
                            </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                            </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-12">
                       
                        <input type="submit" class="btn btn-primary mt-3" value="Save Changes">
                    </div>
                </div>
            </form>
        </div>
    </article>
</section>