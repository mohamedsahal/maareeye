<?php $data = get_settings('general', true) ?>

<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>Features</li>
        </ol>
    </div>
</section>
<section class="contact wrapper bg-light wrapper-border">
    <div class="container text-center">
        <div class="section-title">
            <h2>Features</h2>
            <p class="my-4"><?= get_app_display_name($data['title'] ?? '') ?> provides product management , orders tracking , inventory management and much more. </p>
        </div>

        <div class="position-relative">
            <div class="row gx-md-5 gy-5 text-center">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body ">
                            <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-1.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                            </span>
                            <h3>Business Management</h3>
                            <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                            <p> Provides features that help manage your multiple enterprise subscriptions on a daily basis. </p>
                            <span id="feature-1">
                                <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#question-1" aria-expanded="false" aria-controls="question-1">+Read more</a>
                            </span>
                            <div id="question-1" class="faq-collapse collapse" aria-labelledby="feature-1" data-bs-parent="#faq_div">
                                <div class="features__content">
                                    <p>Easy transition to enterprise, monitor and manage every dashboard, it offers a seamless experience for users.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body ">
                            <div class="">
                                <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-2.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                                </span>
                                <h3>Inventory Management</h3>
                                <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                                <p>Lets you track the inventory of any product of any kind, so that you can always keep and manage the stock of products. </p>
                                <span id="feature-2">
                                    <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#readmore-1" aria-expanded="false" aria-controls="readmore-1">+Read more</a>
                                </span>
                                <div id="readmore-1" class="faq-collapse collapse" aria-labelledby="feature-2" data-bs-parent="#faq_div">
                                    <div class="features__content">
                                        <p> Our inventory system will help you easily obtain all information about product variants with just one click.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body">
                            <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-3.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                            </span>
                            <h3>Subscription Management</h3>
                            <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                            <p>You can easily create order of services , if your service is subscruption based and </p>
                            <span id="feature-3">
                                <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#readmore-2" aria-expanded="false" aria-controls="readmore-2">+Read more</a>
                            </span>
                            <div id="readmore-2" class="faq-collapse collapse" aria-labelledby="feature-3" data-bs-parent="#faq_div">
                                <div class="features__content">
                                    <p>
                                        which require renewal by times this will auto renew service. It will ease the day-to-day monitoring of subscribers!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body ">
                            <div class="">
                                <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-4.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                                </span>
                                <h3>Tax And System </h3>
                                <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                                <p>Generate sales of goods and services, whether they include taxes or not.</p>
                                <span id="feature-4">
                                    <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#readmore-3" aria-expanded="false" aria-controls="readmore-3">+Read more</a>
                                </span>
                                <div id="readmore-3" class="faq-collapse collapse" aria-labelledby="feature-4" data-bs-parent="#faq_div">
                                    <div class="features__content">
                                        <p>
                                            We create a product sales system that enables you to worry less about calculating the tax manually.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body ">
                            <div class="">
                                <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-7.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                                </span>
                                <h3>Record Transactions</h3>
                                <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                                <p>Enables you to save order transactions as well as other transactions!</p>
                                <span id="feature-5">
                                    <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#readmore-4" aria-expanded="false" aria-controls="readmore-4">+Read more</a>
                                </span>
                                <div id="readmore-4" class="faq-collapse collapse" aria-labelledby="feature-5" data-bs-parent="#faq_div">
                                    <div class="features__content">
                                        <p> Create a record of each client's portfolio transactions and orders, also process partially paid orders, keep a record of the unpaid amount and much more.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="card-body ">
                            <div class="">
                                <span class="features_icon m-auto" data-aos="fade-up" data-aos-delay="100">
                                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/feature-6.json') ?>" background="transparent" speed="1" loop autoplay></lottie-player>
                                </span>
                                <h3>Delivery Note </h3>
                                <span><?= get_app_display_name($data['title'] ?? '') ?></span>
                                <p>Acknowledge multiple orders of product/service at once </p>
                                <span id="feature-6">
                                    <a class="collapsed text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#readmore-5" aria-expanded="false" aria-controls="readmore-5">+Read more</a>
                                </span>
                                <div id="readmore-5" class="faq-collapse collapse" aria-labelledby="feature-6" data-bs-parent="#faq_div">
                                    <div class="features__content">
                                        <p>Consolidated order status updates, with an accurate point-of-sale system to create multiple orders for each company.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!-- /.container -->
</section>