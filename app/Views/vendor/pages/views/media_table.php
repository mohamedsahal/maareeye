<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('media', 'Media') ?></h1>

        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="co;-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="section-title mb-5">
                                    <?= labels('upload_new_media', 'Upload New Media') ?>
                                </h2>
                                <form action="<?= base_url('vendor/media/upload'); ?>" enctype="multipart/form-data"
                                    method="POST" class="form-submit-event">

                                    <input type="file" class="filepond" name="documents[]" multiple
                                        data-max-file-size="30MB" data-max-files="20" />
                                    <button type="submit" class="btn btn-primary submit_button float-end"
                                        id="submit_btn">
                                        <?= labels('upload', 'Upload') ?> </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-auto-refresh="true"
                                    data-show-columns="true" data-show-toggle="true" data-show-refresh="true"
                                    data-toggle="table" data-search-highlight="true"
                                    data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('vendor/media/media_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true" data-visible="false">
                                                <?= labels('id', 'Id') ?>
                                            </th>
                                            <th data-field="vendor_id" data-sortable="true" data-visible="false">
                                                <?= labels('vendor_id', 'Vendor Id') ?>
                                            </th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?>
                                            </th>
                                            <th data-field="image" data-sortable="true" data-visible="true">
                                                <?= labels('image', 'Image') ?>
                                            </th>
                                            <th data-field="extension" data-sortable="true" data-visible="true">
                                                <?= labels('extension', 'Extension') ?>
                                            </th>
                                            <th data-field="sub_directory" data-sortable="true" data-visible="true">
                                                <?= labels('sub_directory', 'Sub directory') ?>
                                            </th>
                                            <th data-field="size" data-sortable="true" data-visible="true">
                                                <?= labels('size', 'Size') ?>
                                            </th>
                                            <th data-field="action" data-sortable="true" data-visible="true">
                                                <?= labels('action', 'Action') ?>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                                <script>
                                    function queryParams(params) {
                                        console.log(params); // Debug: Check parameters
                                        return {
                                            limit: params.limit,
                                            offset: params.offset,
                                            sort: params.sort,
                                            search: params.search
                                        };
                                    }
                                </script>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('customers_subscription', 'Customers Subscription') ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="section">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="<?= base_url('vendor/bulk_uploads/import_suppliers') ?>"
                                            method="post" id="bulk_uploads_form">
                                            <div class="row">
                                                <div class="form-group">
                                                    <label><?= labels('type<small>(upload)</small>', 'Type <small>(upload)</small>') ?></label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value='upload' selected>Upload</option>
                                                        <option value='update'>Update</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    <label><?= labels('file', 'File') ?></label>
                                                    <input type="hidden" id="vendor_id" name="vendor_id"
                                                        value="<?= $business_id ?>">
                                                    <input type="file" class="form-control" id="bulk_upload_file"
                                                        name="file" accept=".csv">
                                                </div>
                                            </div>


                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary bulk_upload"
                                                    value="Save"><?= labels('import', 'Import') ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-3 justify-content-start mt-4">
                            <div>
                                <a href="<?= base_url('public/uploads/bulk-upload-supplier.csv') ?>"
                                    class="btn btn-info" download="bulk-upload-supplier.csv">Bulk upload
                                    sample file <i class="fas fa-download"></i></a>
                            </div>

                            <div>
                                <a href="<?= base_url('public/uploads/insert_bulk_suppliers_instruction.txt') ?>"
                                    class="btn btn-success" download="bulk-upload-supplier-upload.txt">Bulk
                                    upload instructions <i class="fas fa-download"></i></a>
                            </div>

                            <div>
                                <a href="<?= base_url('public/uploads/bulk-update-supplier.csv') ?>"
                                    class="btn btn-info" download="bulk-update-supplier.csv">Bulk update
                                    sample file <i class="fas fa-download"></i></a>
                            </div>

                            <div>
                                <a href="<?= base_url('public/uploads/update_bulk_suppliers_instruction.txt') ?>"
                                    class="btn btn-success"
                                    download="bulk-upload-instructions-for-supplier-update.txt">Bulk
                                    update
                                    instructions <i class="fas fa-download"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>