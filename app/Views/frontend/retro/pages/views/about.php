<?php $data = get_settings('general', true) ?>

<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>About Us</li>
        </ol>
    </div>
</section>
<section class="contact wrapper bg-light">
    <div class="text-center">
        <div class="section-title">
            <h2>About us</h2>
            <p class="my-4"><?= get_app_display_name($data['title'] ?? '') ?> is a website solution to manage your business.</p>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6 p-3 p-lg-5 pt-lg-3 text-dark">
                <div class="description">
                    <p><?= ($text) ?></p>
                </div>
            </div>
            <div class="col-md-6 p-3 overflow-hidden">
                <div class="shadow p-3 mb-5 bg-body rounded" data-aos="fade-up" data-aos-delay="100">
                    <img class="rounded-lg-3 img-fluid" src="<?= base_url('public/frontend/assets/retro/img/maareeye-1.png') ?>" alt="">
                </div>
            </div>
        </div>
    </div>
</section>