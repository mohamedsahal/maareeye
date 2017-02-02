<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Payment Status</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <?php
                if ($status) {
                    ?>
                    <div class="card-payment text-center">
                        <div class="h1-payment p-payment text-center">
                            <i class="bi bi-check-circle-fill text-success checkmark i-payment"></i>
                            <h1 class="text-center"><?= $status ? "Success" : " Failed" ?> </h1>
                        </div>
                        <p>Your subscription has been purchased Successfully! </p>
                        <a href="<?= base_url('vendor/subscription') ?>" class="btn btn-action btn-dark"><i
                                class="fa-arrow-left fas mr-2"></i>Go Back</a>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="card-payment text-center">
                        <div class="h1-payment p-payment text-center">
                            <i class="bi bi-x-circle-fill text-danger checkmark i-payment-failed"></i>
                            <h1 class="text-center"><?= $status ? "Success" : " Failed" ?> </h1>
                        </div>
                        <p>Oops, Your transaction has been failed! </p>
                        <a href="<?= base_url('vendor/subscription') ?>" class="btn btn-action btn-dark"><i
                                class="fa-arrow-left fas mr-2"></i>Go Back</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>