<div class="modal  fade" id='media-upload-modal'>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="content-area p-4">
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <input type='hidden' name='media_type' id='media_type' value='image'>
                            <input type='hidden' name='current_input'>
                            <input type='hidden' name='remove_state'>
                            <input type='hidden' name='multiple_images_allowed_state'>
                            <div class="col-md-12 mt-3 mb-5">
                                <form action="<?= base_url('vendor/media/upload'); ?>" enctype="multipart/form-data"
                                    method="POST" class="form-submit-event">

                                    <input type="file" class="filepond" name="documents[]" multiple
                                        data-max-file-size="30MB" data-max-files="20" />
                                    <button type="submit" id="submit_btn"
                                        class="btn btn-primary submit_button float-end">
                                        <?= labels('upload', 'Upload') ?> </button>
                                </form>
                            </div>
                            <div class="pt-4"></div>
                            <div id="toolbar">
                                <div class="alert alert-warning">Select media and click choose media</div>
                                <button id='upload-media' class="btn btn-danger">
                                    <i class="fa fa-plus"></i> Choose Media
                                </button>
                            </div>
                            <table class="table table-hover table-borderd" data-auto-refresh="true"
                                data-show-columns="true" data-show-toggle="true" data-show-refresh="true"
                                data-toggle="table" data-search-highlight="true"
                                data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                data-url="<?= base_url('vendor/media/media_table'); ?>" data-side-pagination="server"
                                data-pagination="true" data-search="true" data-sort-name="id" data-sort-order="desc"
                                id='media-upload-table' data-click-to-select="true">
                                <thead>
                                    <tr>
                                        <th data-field="state" data-checkbox="true"></th>
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

                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="main-footer mb-0 bg-white">
    <div class="footer-right  ">
        <p> Copyright &copy; <?= date("Y") ?> <?= $company ?>
            Design & Developed By <a href="https://mohamedsahal.com" target="_blank">Mohamed Sahal</a>
        </p>
    </div>
</footer>