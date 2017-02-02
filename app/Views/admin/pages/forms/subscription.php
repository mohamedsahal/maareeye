<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('subscription', 'Subscription') ?></h1>
        </div>
        <div class="row">
            <div class="col-md">
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div class="text-danger" class="alert alert-danger" id="add_subscription_result"> </div>
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has("message")) { ?>
            <div class="text-danger"><?= session("message"); ?></label></div>
        <?php } ?>
        <div class="section-body">
            <div class="row mt-sm-4">
                <div class='col-md-12'>
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('admin/subscriptions/add_subscription'); ?>"
                                class="form-submit-event" method="POST">
                                <h2 class="section-title"><?= labels('add_subscription', 'Add Subscription') ?></h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title"><?= labels('select_user', 'Select User') ?></label><span
                                                class="asterisk text-danger "> *</span>
                                            <select name='user_identity' id="user_identity" class="form-control">
                                                <option value="">Select user</option>
                                                <?php foreach ($vendors as $vendor) {
                                                    $fullname = $vendor['first_name'] . ' ' . $vendor['last_name']; ?>
                                                    <option value="<?= $vendor['id']; ?>" data-fullname="<?= $fullname; ?>">
                                                        <?= $vendor['email']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="user_name"><?= labels('users_full_name ', 'Users full name ') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="user_name" id="user_name"
                                                placeholder="" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for='package_name'><?= labels('select_package', 'Select Package') ?></label>
                                            <select name='package_name' id="package_name" class='form-control'>
                                                <option value="">Select Package</option>
                                                <?php foreach ($packages as $package) {
                                                    ?>
                                                    <option value="<?= $package['title']; ?>"
                                                        data-package_name="<?= $package['title']; ?>"
                                                        data-products="<?= $package['no_of_products']; ?>"
                                                        data-customers="<?= $package['no_of_customers']; ?>"
                                                        data-warehouse="<?= $package['no_of_warehouse']; ?>"
                                                        data-delivery_boys="<?= $package['no_of_delivery_boys']; ?>"
                                                        data-businesses="<?= $package['no_of_businesses']; ?>"
                                                        data-package_id="<?= $package['id']; ?>"><?= $package['title']; ?>
                                                    </option>

                                                <?php } ?>
                                                <input type="hidden" name="p_id" id="p_id" value="">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for='package_tenure'><?= labels('select_packag_enure', 'Select Packag Tenure') ?></label>
                                            <select name='package_tenure' name="package_tenure" id='package_tenure'
                                                class="form-control" placeholder="select tenure">
                                                <option value="">Select Tenure</option>
                                            </select>
                                            <input type="hidden" value="" name="tenure_name" id="tenure_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="no_of_businesses"><?= labels('no_of_businesses', 'No. of businesses ') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="number" class="form-control" name="no_of_businesses"
                                                id="no_of_businesses" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="no_of_delivery_boys"><?= labels('No_of_delivery_boys', 'No. of delivery boys') ?></label>
                                            <small>(Enter -1 to allow unlimited delivery boys)</small>
                                            <input type="number" class="form-control" name="no_of_delivery_boys"
                                                id="no_of_delivery_boys" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="no_of_warehouse"><?= labels('no_of_warehouse', 'No. of Warehouse') ?></label>
                                            <input type="number" class="form-control" name="no_of_warehouse"
                                                id="no_of_warehouse" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="no_of_products"><?= labels('No_of_products', 'No. of products') ?></label>
                                            <small>(Enter -1 to allow unlimited products)</small>
                                            <input type="number" class="form-control" name="no_of_products"
                                                id="no_of_products" value="" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="no_of_customers"><?= labels('No_of_customers', 'No. of customers') ?></label>
                                            <small>(Enter -1 to allow unlimited customers)</small>
                                            <input type="number" class="form-control" name="no_of_customers"
                                                id="no_of_customers" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for='price'><?= labels('price', 'Price(₹)') ?></label>
                                            <input class="form-control" name='price' id='price' value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for='starts_from'><?= labels('subscription_start_from', 'Subscription Start From') ?></label>
                                            <input type='date' class="form-control" value="" name='starts_from'
                                                id='starts_from'>
                                            <input type='hidden' class="form-control" value="" name='months'
                                                id='months'>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for='ends_at'><?= labels('subscription_end_at', 'Subscription End At') ?></label>
                                            <input type='date' class="form-control" name='ends_at' id='ends_from'
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="align-items-center col-md-12 d-flex gap-3 justify-content-start">
                                        <button class='btn btn-primary'
                                            id="submit_btn"><?= labels('add_subscription', 'Add subscription') ?></button>
                                        <button id='reset' type="reset"
                                            class='btn btn-dark'><?= labels('reset', 'Reset') ?></button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date"><?= labels('filter_date_by', 'Filter date by') ?></label>
                                            <select name="date_filter_by" id="date_filter_by"
                                                class="form-control selectric">
                                                <option value="">-Select-</option>
                                                <option value="starts_from">
                                                    <?= labels('subscription_start_from', 'Subscription Start From') ?>
                                                </option>
                                                <option value="expires_on">
                                                    <?= labels('subscription_end_at', 'Subscription End At') ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label
                                                for="date_range"><?= labels('date_range_filter', 'Date Range') ?></label>
                                            <input type="text" name="date_range" id="date_range" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label
                                                for="date"><?= labels('filter_by_subscription_type', 'Filter by subscription Type') ?></label>
                                            <select name="subscription_type" class="form-control selectric"
                                                id="subscription_type">
                                                <option value="">All</option>
                                                <option value="active">Active</option>
                                                <option value="upcoming">Upcoming</option>
                                                <option value="expired">Expired</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="align-items-center col-md-3 d-flex gap-3 justify-content-start">

                                        <button class="btn btn-primary d-block" id="filter">
                                            <?= labels('apply', 'Apply') ?>
                                        </button>
                                        <button class="btn btn-dark" name="clear" id="clear">
                                            Clear </button>
                                    </div>
                                    <table class="table table-bordered table-hover" id="subscription_table"
                                        data-show-export="true" data-export-types="['txt','excel','csv']"
                                        data-export-options='{"fileName": "subscription-list"}' data-auto-refresh="true"
                                        data-show-columns="true" data-show-toggle="true" data-show-refresh="true"
                                        data-toggle="table" data-search-highlight="true" data-server-sort="true"
                                        data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                        data-url="<?= base_url('admin/subscriptions/subscription_table'); ?>"
                                        data-side-pagination="server" data-pagination="true" data-search="true"
                                        data-query-params="subscriptions_query" data-sort-name="id"
                                        data-sort-order="desc">
                                        <thead>
                                            <tr>
                                                <th data-field="user_id" data-sortable="true"><?= labels('id', 'ID') ?>
                                                </th>
                                                <th data-field="full_name" data-sortable="true">
                                                    <?= labels('name', 'Name') ?>
                                                </th>
                                                <th data-field="package_name" data-sortable="true">
                                                    <?= labels('package_name', 'Package Name') ?>
                                                </th>
                                                <th data-field="status"><?= labels('status', 'Status') ?></th>
                                                <th data-field="no_of_businesses" data-sortable="true"
                                                    data-visible="false">
                                                    <?= labels('no_of_businesses', 'No. of businesses ') ?>
                                                </th>
                                                <th data-field="no_of_delivery_boys" data-sortable="true"
                                                    data-visible="false">
                                                    <?= labels('No_of_delivery_boys', 'No. of delivery boys') ?>
                                                </th>
                                                <th data-field="no_of_warehouse" data-sortable="true"
                                                    data-visible="false">
                                                    <?= labels('no_of_warehouse', 'No. of warehouse') ?>
                                                </th>
                                                <th data-field="no_of_products" data-sortable="true"
                                                    data-visible="false">
                                                    <?= labels('No_of_products', 'No. of products') ?>
                                                </th>
                                                <th data-field="no_of_customers" data-sortable="true"
                                                    data-visible="false">
                                                    <?= labels('No_of_customers', 'No. of customers') ?>
                                                </th>
                                                <th data-field="tenure" data-sortable="true">
                                                    <?= labels('tenure', 'Tenure') ?>
                                                </th>
                                                <th data-field="price" data-visible="true" data-sortable="true">
                                                    <?= labels('price', 'Price') ?>
                                                </th>
                                                <th data-field="months" data-visible="true" data-sortable="true">
                                                    <?= labels('months', 'Month(s)') ?>
                                                </th>
                                                <th data-field="start_date">
                                                    <?= labels('subscription_start_from', 'Subscription Start From') ?>
                                                </th>
                                                <th data-field="end_date">
                                                    <?= labels('subscription_end_at', 'Subscription End At') ?>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--MAIN CONTENT div  -->