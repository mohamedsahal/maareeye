<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchase_return_list', 'Puchases Return List') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white"
                        href="<?= base_url('vendor/purchases/purchase_orders/return'); ?>" data-toggle="tooltip"
                        data-bs-placement="bottom"
                        title=" <?= labels('create_purchase', 'Create Purchase Order') ?> "><i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-auto-refresh="true"
                                    data-show-columns="true" data-show-toggle="true" data-show-refresh="true"
                                    data-toggle="table" data-search-highlight="true"
                                    data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('vendor/purchases/purchase_return_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="supplier_name" data-sortable="true">
                                                <?= labels('supplier_name', 'Supplier') ?>
                                            </th>
                                            <th data-field="purchase_date" data-sortable="true" data-visible="true">
                                                <?= labels('return_date', 'Return Date') ?>
                                            </th>
                                            <th data-field="return_status" data-sortable="true" data-visible="true">
                                                <?= labels('return_status', 'Return Status') ?>
                                            </th>
                                            <th data-field="status" data-sortable="true" data-visible="true">
                                                <?= labels('payment_status', 'Payment Status') ?>
                                            </th>
                                            <th data-field="amount_paid" data-sortable="true" data-visible="true">
                                                <?= labels('amount_paid', 'Amount Paid') ?>
                                            </th>
                                            <th data-field="purchase_total" data-sortable="true" data-visible="true">
                                                <?= labels('purchase_total', 'Total') ?>
                                            </th>
                                            <th data-field="order_status" data-sortable="false" data-visible="true">
                                                <?= labels('order_status', 'Order Status') ?>
                                            </th>

                                            <th data-field="action" data-sortable="false" data-visible="true">
                                                <?= labels('action', 'Action') ?>
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
        <!--container div  -->
    </section>
</div>