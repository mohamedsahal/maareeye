<!-- contact us  -->
<?php $data = get_settings('general', true) ?>

<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>Contact Us</li>
        </ol>
    </div>
</section>
<section id="contact" class="contact">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-4">
                <div class="contact-detail-box">
                    <i class="ic fa-th fa-3x text-colored"></i>
                    <h4>Get In Touch</h4>
                    <abbr title="Phone"><i class="bi bi-phone"></i></abbr> <?= !empty($data['phone']) ? $data['phone'] : "+1 5589 55488 55" ?><br>
                    <i class="bi bi-envelope"></i> <a href="mailto:email@email.com" class="text-muted"><?= !empty($data['support_email']) ? $data['support_email'] : "support@example.com" ?></a>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box">
                    <h4>Our Location</h4>
                    <address>
                        <?= !empty($data['address']) ? $data['address'] : "" ?>
                    </address>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="contact-detail-box">
                    <i class="ic fa-book fa-3x text-colored"></i>
                    <h4>24x7 Support</h4>
                    <!-- <p>24/7 and 365 days support is available.</p> -->
                    <address>
                        <?= !empty($data['support_hours']) ? $data['support_hours'] : "24/7 and 365 days support is available." ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
    <section class="wrapper bg-light">
        <div class="container py-14 py-md-16">
            <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
                <div class="col-md-6">
                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/contact-us-1.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
                </div>
                <div class="col-md-6 ">
                    <div class="feature-card">
                        <div class="">
                            <form action="<?= base_url('contact/sendMail') ?>" id="contact_form" method="post" class="php-email-form">
                                <input type="hidden" id="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" id="csrf_hash" value="<?= csrf_hash() ?>">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" placeholder="Your Name" required />
                                    </div>

                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email" placeholder="Your Email" required />
                                    </div>

                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="subject" placeholder="Subject" required />
                                    </div>

                                    <div class="col-md-12">
                                        <textarea class="form-control" name="message" rows="10" placeholder="Message" required></textarea>
                                    </div>

                                    <div class="col-md-12 text-center">
                                        <div class="loading">Loading</div>
                                        <div class="error-message"></div>
                                        <div class="sent-message">
                                            Your message has been sent. Thank you!
                                        </div>

                                        <button id="contact_submit" class="btn btn-get-maareeye" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">

                </div>

            </div>
        </div>
    </section>
</section>