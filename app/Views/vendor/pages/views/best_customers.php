<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('best_customers', 'Best Customers') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date"><?= labels('date_range_filter', "Date Range Filter") ?></label>
                            <input type="text" name="daterange" id="date_top_selling_products" class="form-control">
                        </div>
                    </div>

                    <div class="align-items-center col-md-3 d-flex justify-content-start gap-3">
                        <button type="button" class="btn btn-primary" id="apply">
                            Apply
                        </button>
                        <button class="btn btn-dark" name="clear" id="clear"> Clear
                        </button>
                    </div>
                    <div class="row">

                        <table class="table table-hover table-borderd" data-show-export="true"
                            data-export-types="['txt','excel','csv','json']"
                            data-export-options='{"fileName": "Best Customers","ignoreColumn": ["action"]}'
                            data-auto-refresh="true" data-show-columns="true" data-show-toggle="true"
                            data-show-refresh="true" data-toggle="table" data-search-highlight="true"
                            data-page-list="[5, 10, 25, 50, 100, 200, All]"
                            data-url="<?= base_url('vendor/best_customers/best_customers_table'); ?>"
                            data-side-pagination="server" data-pagination="true" data-search="true"
                            data-query-params="best_customers_query" id="best_customers_table" data-sort-name="u.id"
                            data-sort-order="desc">
                            <thead>
                                <tr>
                                    <th data-field="customer_id" data-sortable="true" data-visible="true">
                                        <?= labels('customer_id', 'Customer ID') ?>
                                    </th>
                                    <th data-field="first_name" data-sortable="true" data-visible="true">
                                        <?= labels('name', 'Name') ?>
                                    </th>
                                    <th data-field="mobile" data-sortable="true" data-visible="true">
                                        <?= labels('mobile_number', 'Mobile Number') ?>
                                    </th>
                                    <th data-field="email" data-sortable="true" data-visible="true">
                                        <?= labels('email', 'Email') ?>
                                    </th>
                                    <th data-field="total_sales" data-sortable="true" data-visible="true"><span
                                            class=" badge bg-dark"><?= labels('total_sales', 'Total Sales') ?></span>
                                    </th>
                                    <th data-field="total_amount" data-sortable="true" data-visible="true">
                                        <?= labels('total_amount', 'Total Amount') ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
    </section>
</div>