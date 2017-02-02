<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchase_list', 'Purchases List') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white"
                        href="<?= base_url(relativePath: 'vendor/purchases/purchase_orders/order'); ?>"
                        data-toggle="tooltip" data-bs-placement="bottom"
                        title=" <?= labels('create_purchase', 'Create Purchase Order') ?> "><i
                            class="fas fa-plus"></i></a>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal"
                        data-bs-target="#bulk_upload_modal" data-toggle="tooltip" data-bs-placement="bottom"
                        title="   <?= labels('bulk_upload', 'Bulk Upload Purchases') ?>"><i
                            class="bi bi-cloud-download-fill"></i>
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
                                    data-url="<?= base_url('vendor/purchases/purchase_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="supplier_name" data-sortable="true">
                                                <?= labels('supplier_name', 'Supplier') ?>
                                            </th>
                                            <th data-field="purchase_date" data-sortable="true" data-visible="true">
                                                <?= labels('purchase_data', 'Purchase Date') ?>
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


<div class="modal" id="bulk_upload_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Bulk Upload</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="section">
                    <div class="section-body">
                        <form action="<?= base_url('vendor/bulk_uploads/import_purchase') ?>" method="post"
                            id="bulk_uploads_form">
                            <div class="card ">
                                <div class="row">
                                    <div class="form-group">
                                        <label><?= labels('type<small>(upload)</small>', 'Type <small>(upload)</small>') ?></label>
                                        <select class="form-control" id="type" name="type">
                                            <option value=''>Select</option>
                                            <option value='upload'>Upload</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label><?= labels('file', 'File') ?></label>
                                        <input type="hidden" id="vendor_id" name="vendor_id"
                                            value="<?= $business_id ?>">
                                        <input type="file" class="form-control" id="bulk_upload_file" name="file"
                                            accept=".csv">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary bulk_upload"
                                        value="Save"><?= labels('import', 'Import') ?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="col-12 d-flex gap-3 justify-content-start mt-4">
                        <div>
                            <a href="<?= base_url('public/uploads/bulk-upload-purchase.csv') ?>" class="btn btn-info"
                                download="bulk-upload-purchase-sample.csv">Bulk upload
                                sample
                                file <i class="fas fa-download"></i></a>
                        </div>
                        <div>
                            <a href="<?= base_url('public/uploads/bulk-upload-pruchase-instructions.txt') ?>"
                                class="btn btn-success" download="bulk-upload-pruchase-instructions.txt">Bulk upload
                                instructions <i class="fas fa-download"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>