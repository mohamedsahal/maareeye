<section class="breadcrumbs">
    <div class="container">
        <ol class='floatc-right'>
            <li><a href="<?= base_url() ?>">Home</a></li>
            <li>Pricing</li>
        </ol>
    </div>
</section>
<section class="pricing contact wrapper bg-light wrapper-border">
    <div class="container">
        <div class="section-title p-2">
            <h2>Choose your package</h2>
        </div>
        <div class="row gy-4 py-5">
            <?php if (isset($packages) && !empty($packages)) { ?>
                <?php
                $i = 0;
                foreach ($packages as $key => $value) {
                    $tenures = $value['tenures'];
                   
                ?>
                    <div class="col-lg-3 col-md-6 my-4">
                        <div class="box">
                            <h3 class="title"><?= ucwords($value['title']) ?> </h3>
                            <div class="price d-inline-flex"><?= $currency ?>
                                <div id='price<?= $value['id'] ?>'><?php if ($tenures[0]['discounted_price'] != 0) {
                                                                        echo number_format($tenures[0]['discounted_price']) ?> <small class="discount-font">(<del><?= $currency.$tenures[0]['price'] ?></del>)</small>
                                    <?php } else {
                                                                        echo number_format($tenures[0]['price']);
                                                                    } ?>

                                </div>

                            </div>
                            <div class="container">
                                <div class="form-group">
                                    <select class="form-control tenures" data-package_id="<?= $value['id'] ?>" name="tenures">
                                        <?php for ($j = 0; $j < count($tenures); $j++) { ?>
                                            <option value="<?= $tenures[$j]['price'] ?>" data-price='<?= $tenures[$j]['price'] ?>' data-discount="<?= $tenures[$j]['discounted_price'] ?>"><?= $tenures[$j]['tenure'] ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                            <ul>
                                <li></li>
                                <li><?= "No. of businesses " . $value['no_of_businesses']; ?></li>
                                <li><?= "No. of customers " . $value['no_of_customers']; ?></li>
                                <li><?= "No. of delivery boys " . $value['no_of_delivery_boys']; ?></li>
                                <li><?= "No. of products " . $value['no_of_products']; ?></li>
                            </ul>
                            <div class="link">
                                <a class="btn btn-sm btn-get-maareeye rounded m-1" href="<?= isset($vendor) ? base_url('vendor/subscription/packages') : base_url('login') ?>">Buy Now</a>
                            </div>
                        </div>
                    </div>

                <?php $i++;
                }
            } else { ?>
        </div>
        <div class="section-title">
            <h4>Package doesn't exist yet!</h4>
        </div>
    <?php } ?>
    </div>
</section>