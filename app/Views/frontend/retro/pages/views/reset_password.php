<section class="py-5 mt-4 bg-white text-dark">
    <div class="text-center">
        <h2>Forgot Password</h2>
    </div>

    <div class="container">

        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-md-6">
                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/forgot-password.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
            </div>
            <div id="infoMessage" ><?= $message?></div>
            <div class="col-md-4 offset-1">
                <form name="user_id" method="post" action="<?= base_url('auth/reset_password/'. $code)  ?>">
                    <?php
                    $session = session();
                    if ($session->has("message")) { ?>
                        <?= session("message"); ?>
                    <?php } ?>
                    <div class="p-md-5 mx-md-4">
                        <div id="infoMessage" class='alert'></div>
                        <?php
                        if (isset($message) && $message != '') {
                        ?><script>
                                document.getElementById('infoMessage').style.display = 'none';
                            </script><?php
                                    } else {
                                        ?> <script>
                                document.getElementById('infoMessage').style.display = 'block';
                            </script><?php
                                    }
                                    if (isset($_SESSION['no_id'])) {
                                        echo $_SESSION['no_id'];
                                    }
                                        ?>
                    </div>
                    <div class="form-group">
                        <p>
                            <label class="form-group" for="new_password"><?php echo sprintf(lang('Auth.reset_password_new_password_label'), $minPasswordLength); ?></label> <br />
                            <input type="password" class="form-group" name="new_password" >
                            
                        </p>

                        <p>
                            <label class="form_group" for="new_password_confirm"></label>
                            <?php echo form_label(lang('Auth.reset_password_new_password_confirm_label'), 'new_password_confirm'); ?> <br />
                            <input type="password" name="new_password_confirm" class="form-group">
                            
                        </p>

          
                        <button class="btn btn-primary" name="Auth.reset_password_submit_btn" type="submit">Submit</button>
                        
                    </div>
                    <div class="text-center form-outline mb-4 pb-1">
                            
                        <a href="<?= base_url('login') ?>" class=" mb-2 btn btn-get-maareeye w-10em">Go Back</a><br>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>