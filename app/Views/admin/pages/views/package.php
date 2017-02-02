<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1> <?= labels('packages', 'Packages') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/packages/create'); ?>" data-toggle="tooltip" data-placement="left" title="<?= labels('create_package', 'Create Package') ?> " class="btn"><i class="fas fa-plus"></i> </a>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <h2 class="section-title"><?= labels('active_packages', 'Active packages') ?></h2>
            </div>
        </div>

        <div class="row">
            <!-- -------------------------------------For loop test -->

            <?php foreach ($packages as $package) {
                $tenures = $package['tenures'];
            ?>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="pricing pricing-highlight shadow">
                        <div class="pricing-title ">
                            <?= $package['title']; ?>
                        </div>

                        <div class="pricing-padding">
                            <div class="pricing-price">
                                <div><?= $currency ?><span class="price price-font" id="price<?= $package['id'] ?>"><?php if ($tenures[0]['discounted_price'] != 0) {
                                                                                                                        echo $tenures[0]['discounted_price'] ?> <small class="discount-font">(<del> <?= currency_location(decimal_points($tenures[0]['price'])) ?></del>)</small>
                                        <?php } else {
                                                                                                                        echo $tenures[0]['price'];
                                                                                                                    } ?>
                                    </span></div>
                                <div class="col-md-6 offset-md-3">
                                    <select class="form-control tenures" data-package_id="<?= $package['id'] ?>" name="tenures">
                                        <options>Select Tenure</option>
                                            <?php for ($j = 0; $j < count($tenures); $j++) { ?>
                                                <option value="<?= $tenures[$j]['price'] ?>" data-price='<?= $tenures[$j]['price'] ?>' data-discount="<?= $tenures[$j]['discounted_price'] ?>"><?= $tenures[$j]['tenure'] ?></option>
                                            <?php } ?>
                                    </select>
                                </div>


                            </div>
                            <div class="pricing-details" id="discount_price<?= $package['id'] ?>">
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label"> <?= labels('no_of_businesses', 'No. of businesses ') . " " . $package['no_of_businesses']; ?></div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label"><?= labels('No_of_customers', 'No. of customers') . " " . (($package['no_of_customers'] == -1) ? "unlimited" : $package['no_of_customers']); ?></div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label"><?= labels('No_of_delivery_boys', 'No. of delivery boys') . " " . (($package['no_of_delivery_boys'] == -1) ? "unlimited" : $package['no_of_delivery_boys']); ?></div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon "><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label"><?= labels('No_of_products', 'No. of products') . " " . (($package['no_of_products'] == -1) ? "unlimited" : $package['no_of_products']); ?></div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label">
                                        <?= labels('no_of_warehouse', 'No. of warehouse ') . " " . (($package['no_of_warehouse'] == -1) ? "unlimited" : $package['no_of_warehouse']); ?>
                                    </div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label">
                                    <?= labels('No_of_brands', 'No. of Brands') . " " . $package['no_of_brands']; ?>
                                    </div>
                                </div>


                                <div class="pricing-item ">
                                    <div class="pricing-item-icon <?= $tenures[0]['discounted_price'] > 0 ? 'bg-success' : 'bg-danger' ?>"><i class="fas <?= $tenures[0]['discounted_price'] > 0 ? ' fa-check' : ' fa-times' ?>"></i></div>
                                    <div class="pricing-item-label"><?= labels('discounted_price', 'Discounted Price') ?>
                                        <span id="discount_price<?= $package['id'] ?>">
                                            <?= $tenures[0]['discounted_price'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md">
                                    <a href="<?= site_url('admin/packages/edit_package/' . $package['id']) ?>" class="btn btn-primary btn-block width-package-btn"> <i class="fa fa-pen"></i> <?= labels('edit', "Edit") ?></a>
                                </div>
                                <div class="col-md">
                                    <button onclick="delete_plan(this);" data-plan-id="<?= $package['id'] ?>" class="btn btn-danger btn-block  width-package-btn"> <i class="fa fa-trash"></i> <?= labels('delete', "Delete") ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            ?>

        </div>

    </section>
</div>