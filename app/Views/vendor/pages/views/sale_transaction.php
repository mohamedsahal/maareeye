<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchase_transactions', 'Purchase Transactions') ?></h1>
        </div>
        <div class="row">
            <div class="col-md">
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has("message")) { ?>
            <div class="flash-message-custom"><?= session("message"); ?></label></div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="section-title ml-3"><?= labels('all_transactions', 'All Transactions') ?></div>

                    <div class="card-body">
                        <div class="col-md">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table table-bordered table-hover" data-show-export="true"
                                        data-export-types="['txt','excel','csv']"
                                        data-export-options='{"fileName": "customers-sale-transaction-list","ignoreColumn": ["action"]}'
                                        id="transactions_table" data-auto-refresh="true" data-show-columns="true"
                                        data-show-toggle="true" data-show-refresh="true" data-toggle="table"
                                        data-search-highlight="true" data-server-sort="true"
                                        data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                        data-url="<?= base_url('vendor/transactions/sale_transactions_table'); ?>"
                                        data-side-pagination="server" data-pagination="true" data-search="true"
                                        data-query-params="t_query" data-sort-name="id" data-sort-order="desc">
                                        <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                                <th data-field="order_id" data-visible="true">
                                                    <?= labels('order_id', 'Order Id') ?>
                                                </th>
                                                <th data-field="supplier" data-sortable="false">
                                                    <?= labels('supplier_name', 'Supplier Name') ?>
                                                </th>
                                                <th data-field="payment_type" data-sortable="true" data-visible="true">
                                                    <?= labels('payment_mode', 'Payment Mode') ?>
                                                </th>
                                                <th data-field="type" data-sortable="true"><?= labels('type', 'Type') ?>
                                                </th>
                                                <th data-field="payment_type" data-sortable="true" data-visible="true">
                                                    <?= labels('type', 'Payment Type') ?>
                                                </th>
                                                <th data-field="amount" data-sortable="true" data-visible="true">
                                                    <?= labels('amount', 'Amount') ?>
                                                </th>
                                                <th data-field="created_by" data-sortable="true" data-visible="true">
                                                    <?= labels('created_by', 'Created by') ?>
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