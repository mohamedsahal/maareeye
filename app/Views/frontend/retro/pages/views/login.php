<section id="contact" class="contact">
    <div>
        <div class="container py-14 py-md-16">
            <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                ?>
                <div class="col-12 text-center mb-5">
                    <div class="alert alert-warning mb-0">
                        <b>Note:</b> If you cannot login here, please close the codecanyon frame by clicking on <b>x Remove
                            Frame</b> button from top right corner on the page or <a
                            href="https://maareeye.taskhub.company" target="_blank">&gt;&gt; Click here &lt;&lt;</a>
                    </div>
                </div>
            <?php } ?>
            <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
                <div class="col-lg-7">
                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/login.json') ?>"
                        background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
                </div>
                <!--/column -->

                <div class="col-lg-5" id="sign_in">
                    <?php
                    $session = session();
                    if ($session->has("message")) { ?>
                        <?= session("message"); ?>
                    <?php } ?>
                    <form method="POST" action="<?= base_url('auth/login') ?>" id="login_form" novalidate="">
                        <div class="form-group">
                            <label for="identity"><?= ucwords(config('IonAuth')->identity); ?></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                </div>
                                <input id="identity" type="text" class="form-control" name="identity" autofocus>

                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </div>
                                </div>
                                <input type="password" id="password" name="password" class="form-control pwstrength"
                                    data-indicator="pwindicator">
                                <span class="input-group-text togglePassword" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                                    id="remember_me">
                                <label class="custom-control-label" for="remember_me">Remember Me</label>
                            </div>
                        </div>

                        <div class="d-flex ">
                            <button type="submit" class="btn btn-get-maareeye" tabindex="4">
                                Login
                            </button>

                            <button type="button" id="register_btn_of_login" class="btn btn-video mx-2" tabindex="4">
                                Register
                            </button>
                        </div>
                        <div class="form-group d-flex">
                            <a href="<?= base_url('forgot_password') ?>" class="float-left mt-3">
                                Forgot Password?
                            </a>
                        </div>


                    </form>

                    <?php

                    if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                        ?>
                        <div class="row  text-center mt-2">

                            <div class="row">
                                <div class="col-md m-1">
                                    <button class="mb-2 btn btn-buy btn-buy-primary w-100 w-50" onclick="set_admin()">
                                        Login as Admin
                                    </button>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md m-1">
                                    <button class="mb-2 btn btn-buy btn-buy-danger w-100 w-50" onclick="set_vendor()">
                                        Login as Vendor
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md m-1">
                                    <button class="mb-2 btn btn-buy btn-buy-warning w-100 w-50"
                                        onclick="set_delivery_boy()">
                                        Login as Delivery Boy
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div id="sign_up" class="col-lg-5">
                    <?php
                    $session = session();
                    if ($session->has("message")) { ?>
                        <div class="text-danger"><?= session("message"); ?></label></div>
                    <?php } ?>
                    <form method="POST" id="register_form" action="<?= base_url('auth/create_user'); ?>">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name</label>
                                <input id="first_name" type="text" class="form-control" name="first_name" autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name</label>
                                <input id="last_name" type="text" class="form-control" name="last_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="text" class="form-control" name="email">

                            <div class="invalid-feedback">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identity">Mobile</label>
                            <input type="text" id="identity" class="form-control phone-number" name="identity">
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password" class="form-control pwstrength"
                                    data-indicator="pwindicator" name="password">
                                <div id="pwindicator" class="pwindicator">
                                    <div class="bar"></div>
                                    <div class="label"></div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_confirm" class="d-block">Password Confirmation</label>
                                <input id="password_confirm" type="password" class="form-control"
                                    name="password_confirm">
                            </div>
                        </div>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-get-maareeye btn-lg btn-block">
                                Register
                            </button>
                            <button type="button" id="login_btn_of_register" class="btn btn-video mx-2 " tabindex="4">
                                Login
                            </button>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <!--/column -->
    </div>
    <!--/.row -->
    </div>
    <!-- /.container -->
</section>