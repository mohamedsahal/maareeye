<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>FAQs</li>
        </ol>
    </div>
    <?php $data = get_settings('general', true) ?>

</section>
<section class="contact wrapper wrapper-border my-5">
    <div class="section-title mb-5 mt-1">
        <h2 class="my-2">Frequently asked questions</h2>
        <p>Explore some of the most frequently raised queries and their simple answers to help you clear your queries.</p>
    </div>

    <div class="container py-14 py-md-16">
        <div class="row gx-lg-8 gx-xl-12 gy-10">
            <div class="col-md">
                <div class="faq faq-wrapper" id="faq_div">
                    <div class="card plain faq-item">
                        <div class="card-header" id="headingOne-2">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-0" aria-expanded="false" aria-controls="question-1"> What is <?= get_app_display_name($data['title'] ?? '') ?>? </button>
                        </div>
                        <div id="question-0" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                            <div class="card-body">
                                <p>The <?= get_app_display_name($data['title'] ?? '') ?> system will make it possible to keep track of orders ,products and services through Website. an easy approach to turning your traditional way of doing business into a digital platform.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card plain faq-item">
                        <div class="card-header" id="headingOne-2">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-1" aria-expanded="false" aria-controls="question-1"> What / Where this system can be used? </button>
                        </div>
                        <div id="question-1" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                            <div class="card-body">
                                <p>small to medium scale businesses Ex, Wholeseller, Vendor, Retailers, Shop owner, Urban service provider, Good suppliers</p>
                            </div>
                        </div>
                    </div>

                    <div class="card plain faq-item">
                        <div class="card-header" id="headingOne-2">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-2" aria-expanded="false" aria-controls="question-2"> What benefit will it do to my business? </button>
                        </div>
                        <div id="question-2" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                            <div class="card-body">
                                <p>Rapid order placement, payment tracking and inventory management will help businesses that rely on bills and pens.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card plain faq-item">
                        <div class="card-header" id="headingOne-2">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-3" aria-expanded="false" aria-controls="question-3"> Which is the best digital platform for my business? </button>
                        </div>
                        <div id="question-3" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                            <div class="card-body">
                                <p> <?=
                                    get_app_display_name($data['title'] ?? '') ?> </p>
                            </div>
                        </div>
                    </div>
                    <div class="card plain faq-item">
                        <div class="card-header" id="headingOne-2">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-4" aria-expanded="false" aria-controls="question-4"> How it will help your business to transform into digital platform ? </button>
                        </div>
                        <div id="question-4" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                            <div class="card-body">
                                <p>Top features of this system which will transorm your business</p>
                                <p>1.Pos systen for orders</p>
                                <p>2.Auto renew subscriptions</p>
                                <p>3.Delivery Challan</p>
                                <p>4.Cash Flow</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>