<!-- contact us  -->
<?php $data = get_settings('refund_policy', true) ?>

<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>Refund Policy</li>
        </ol>
    </div>
</section>
<section id="contact" class="contact">
    <div class="container py-14 py-md-16">
        <div class="text-center">
            <div class="section-title">
                <h2>Refund Policy</h2>
            </div>
        </div>
        <div class="text-justify">
            <p><?= $text ?></p>
        </div>
    </div>
    <!-- /.container -->
</section>