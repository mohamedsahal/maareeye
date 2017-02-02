<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Create Vendor</h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/vendors'); ?>" data-toggle="tooltip"
                        data-placement="left" title=" Vendors " class="btn"><i class="fas fa-list"></i> </a>
                </div>
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-danger"><?php $message = session('message');
            echo $message; ?></label></div>
        <?php } ?>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-body">
                        <form method="post" class="form-submit-event" action="<?= base_url('auth/create_user'); ?>">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="first_name">First Name <small class="text-danger">*</small></label>
                                    <input id="first_name" type="text" class="form-control" name="first_name" autofocus>
                                </div>
                                <div class="form-group col-6">
                                    <label for="last_name">Last Name <small class="text-danger">*</small></label>
                                    <input id="last_name" type="text" class="form-control" name="last_name">
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group col-6">
                                    <label for="email">Email <small class="text-danger">*</small></label>
                                    <input id="email" type="text" class="form-control" name="email">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="identity">Mobile Number <small class="text-danger">*</small></label>
                                    <input type="text" id="identity" class="form-control phone-number" name="identity">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="password" class="d-block">Password</label>
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
                                    <label for="password_confirm" class="d-block">Password Confirmation</label>
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
                                <button type="submit" class="btn btn-primary" value="vendor" id='submit_btn'>
                                    Register
                                </button>
                                <button type="reset" class="btn btn-dark">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>