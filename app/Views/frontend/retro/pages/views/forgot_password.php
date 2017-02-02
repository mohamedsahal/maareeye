<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>Forgot Password</li>
        </ol>
    </div>
</section>
<section class="py-5 mt-4 bg-white text-dark">
    <div class="text-center">
        <h2>Forgot Password</h2>
    </div>

    <div class="container">

        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-md-6">
                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/forgot-password.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
            </div>
            <div class="col-md-4 offset-1">
                <form method="post" action="<?= base_url('auth/forgot_password')  ?>">
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
                        <label for="identity"></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>

                            <input id="identity" type="text" placeholder="Enter registered Mobile Number" class="form-control" name="identity" autofocus>
                        </div>

                    </div>
                    <div class="text-center form-outline mb-4 pb-1">
                        <input type="submit" class="mb-2 btn btn-get-maareeye w-10em" value="Submit">
                        <a href="<?= base_url('login') ?>" class=" mb-2 btn btn-get-maareeye w-10em">Go Back</a><br>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>