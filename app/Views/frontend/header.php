<?php
$data = [];
try {
    $data = get_settings('general', true);
} catch (Exception $e) {
    echo "<script>console.log('$e')</script>";
}
?>


<header id="header" class="d-flex align-items-center sticky-top">
    <div class="container d-flex align-items-center justify-content-between">

        <h1 class="logo">
            <a href="<?= base_url() ?>" class="logo d-flex align-items-center">
                <img src="<?= isset($data['logo']) && $data['logo'] != "" ? base_url($data['logo']) : base_url('public/uploads/maareeye-logo.png') ?>" alt="Maareeye" />
            </a>
        </h1>

        <nav id="navbar" class="navbar">
            <ul>
                <li><a class="nav-link active" href="<?= base_url('home') ?>">Home</a></li>
                <li><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                <li><a class="nav-link" href="<?= base_url('features') ?>">Features</a></li>
                <li><a class="nav-link" href="<?= base_url('faqs') ?>">FAQs</a></li>
                <li><a class="nav-link" href="<?= base_url('pricing') ?>">Pricing</a></li>
                <li><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>

                <?php if (isset($who_user_is) && $who_user_is == "admin") {  ?>
                    <li class="px-0">
                        <div class="text-center"> <a id="get_maareeye" class="btn btn-sm rounded m-1" type="button" href="<?= base_url('admin/home') ?>"> Dashboard </a> </div>
                    </li>
                <?php  } elseif (isset($who_user_is) && $who_user_is == "vendor") {  ?>
                    <li class="px-0">
                        <div class="text-center"> <a id="get_maareeye" class="btn btn-sm rounded m-1" type="button" href="<?= base_url('vendor/home') ?>"> Dashboard </a> </div>
                    </li>
                <?php  } else { ?>
                    <li class="px-0">
                        <div class="text-center"> <button id="get_maareeye" class="btn btn-sm btn-get-maareeye rounded m-1" type="button">get <?= get_app_display_name($data['title'] ?? '') ?></button> </div>
                    </li>
                <?php } ?>



            </ul>
            <!-- <button class="btn btn-primary d-sm-block d-md-none d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">Toggle top offcanvas</button> -->

            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>

    </div>
</header>


<div class="offcanvas offcanvas-top " tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasTopLabel">Offcanvas top</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body mobile-navbar">


    </div>
</div>