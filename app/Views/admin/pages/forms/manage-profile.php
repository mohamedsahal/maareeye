<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('my_profile', 'My Profile') ?></h1>
        </div>
        <?php
        $id = $profile->id
            ?>
        <?php
        $session = session();
        if ($session->has("message")) { ?>
            <div class="text-danger"><?= session("message"); ?></label></div>
        <?php } ?>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-body">
                        <form method="post" id="admin_profile_form" class="form-submit-event"
                            action="<?= base_url("admin/profile/update"); ?>">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="first_name"><?= labels('first_name', 'First Name') ?> <small
                                            class="text-danger">*</small></label>
                                    <input id="first_name" type="text" class="form-control" name="first_name"
                                        value="<?= $profile->first_name ?>" autofocus>
                                </div>
                                <div class="form-group col-6">
                                    <label for="last_name"><?= labels('last_name', 'Last Name') ?> <small
                                            class="text-danger">*</small></label>
                                    <input id="last_name" type="text" class="form-control" name="last_name"
                                        value="<?= $profile->last_name ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email"><?= labels('email', 'Email') ?> <small
                                        class="text-danger">*</small></label>
                                <input id="email" type="text" class="form-control" name="email"
                                    value="<?= $profile->email ?>">
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="identity"><?= labels('mobile_number', 'Mobile') ?> <small
                                        class="text-danger">*</small></label>
                                <input type="text" id="identity" class="form-control phone-number" name="identity"
                                    value="<?= $profile->mobile ?>">
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label><?= labels('old_password', "Old Password") ?> (
                                        <?= labels('leave_blank', "Leave it blank to disable it") ?> )</label>
                                    <div class="input-group">
                                        <input id="password" type="password" class="form-control pwstrength"
                                            data-indicator="pwindicator" name="password">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    <div id="pwindicator" class="pwindicator">
                                        <div class="bar"></div>
                                        <div class="label"></div>
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label><?= labels('new_password', "new Password") ?> (
                                        <?= labels('leave_blank', "Leave it blank to disable it") ?> )</label>
                                    <div class="input-group">
                                        <input id="password_confirm" type="password" class="form-control"
                                            name="password_confirm">
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg " id="submit_btn">
                                    <?= labels('update', 'Update') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>