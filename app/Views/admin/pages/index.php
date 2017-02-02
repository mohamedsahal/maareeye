<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('package_statistics', 'Total Available Packages') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-briefcase text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_packages', 'Total Packages') ?></h4>

                            </div>
                            <div class="card-body">
                                <?php echo isset($total_packages) ? $total_packages : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">
                            <?= labels('sold_packages_statistics', 'Packages Sold (All Time)') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-cart-plus-fill text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('sold_packages', 'Sold Packages') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($sold_packages) ? $sold_packages : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('vendor_statistics', 'Active Vendors') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_vendors', 'Total Vendors') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($vendors_count) ? $vendors_count : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">
                            <?= labels('total_package_payment', 'Total Earnings (All Time)') ?>

                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total', 'Total ') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($earning) ? currency_location(decimal_points($earning)) : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('products_sales', 'Total Sales') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="158"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('business_insights', 'Business Insights') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </section>
</div>