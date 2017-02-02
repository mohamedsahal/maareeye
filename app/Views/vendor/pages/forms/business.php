<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('business', 'Business') ?></h1>
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
                            <form action="<?= base_url('vendor/businesses/save_business') ?>" class="form-submit-event"
                                enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <h2 class="section-title"><?= labels('add_business', 'Add Business') ?></h2>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label
                                                for="name"><?= labels('business_name', 'Business Name') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="<?= !empty($business) && !empty($business['name']) ? $business['name'] : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md">


                                        <label for="icon"><?= labels('icon', 'Icon') ?></label><span
                                            class="asterisk text-danger"> *</span>
                                        <div class="row form-group d-flex">

                                            <div class='col-md-8 form-group'>
                                                <div>

                                                    <a class="uploadFile media_link" data-input='business_input_image'
                                                        data-isremovable='0' data-is-multiple-uploads-allowed='0'
                                                        data-bs-toggle="modal" data-bs-target="#media-upload-modal"
                                                        value="Upload Photo">
                                                        <div
                                                            class="col-md-12 file_upload_box border file_upload_border">
                                                            <div class="mt-2">
                                                                <div class="col-md-12  text-center">
                                                                    <div>
                                                                        <p class="caption text-dark-secondary">Choose
                                                                            image
                                                                            for business.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>

                                                <div class="col-md-6 image-upload-section">
                                                    <div
                                                        class="bg-white image-box-200 grow image mb-5 mt-0 p-3 rounded shadow text-center d-none">
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="image-box-200 d-none image_edit_box">
                                                <img class="settings_logo" id="image_edit" src="" alt="">

                                                <input type="hidden" name="edit_business_input_image"
                                                    class="input_image" value=''>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label
                                                for="description"><?= labels('description', 'Description') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <textarea name="description" class="form-control" name="description"
                                                id="description"><?= !empty($business) && !empty($business['description']) ? $business['description'] : "" ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="address"><?= labels('address', 'Address') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <textarea name="address" class="form-control" name="address"
                                                id="address"><?= !empty($business) && !empty($business['address']) ? $business['address'] : "" ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="email"> <?= labels('email', 'Email') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="<?= !empty($business) && !empty($business['email']) ? $business['email'] : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="contact"><?= labels('contact ', 'Contact') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="contact" id="contact"
                                                value="<?= !empty($business) && !empty($business['contact']) ? $business['contact'] : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="website"><?= labels('website', 'Website') ?></label>
                                            <input type="text" class="form-control" name="website" id="website"
                                                value="<?= !empty($business) && !empty($business['website']) ? $business['website'] : "" ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="tax_name"><?= labels('tax_name', 'Tax Name') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="tax_name" id="tax_name"
                                                value="<?= !empty($business) && !empty($business['tax_name']) ? $business['tax_name'] : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="tax_value"><?= labels('tax_value', 'Tax Value') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="tax_value" id="tax_value"
                                                value="<?= !empty($business) && !empty($business['tax_value']) ? $business['tax_value'] : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label
                                                for="bank_details"><?= labels('bank_details', 'Bank Details') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <textarea name="bank_details" class="form-control" name="bank_details"
                                                id="bank_details"><?= !empty($business) && !empty($business['bank_details']) ? $business['bank_details'] : "" ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="status" class="custom-switch p-0">
                                                <input type="checkbox" name="status" id="status"
                                                    class="custom-switch-input" checked>
                                                <span class="custom-switch-indicator"></span>
                                                <span
                                                    class="custom-switch-description"><?= labels('active', 'Active') ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <input type="hidden" class="form-control" name="business_id" id="business_id"
                                        value="<?= !empty($business) && !empty($business['id']) ? $business['id'] : "" ?>">

                                    <button type="submit" class="btn btn-primary" id="submit_btn">
                                        <?= labels('save', 'Save') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover table-borderd" data-show-export="true"
                                    data-export-types="['txt','excel','csv']"
                                    data-export-options='{"fileName": "businesses-list","ignoreColumn": ["action"]}'
                                    id="businesses_table" data-auto-refresh="true" data-show-columns="true"
                                    data-show-toggle="true" data-show-refresh="true" data-toggle="table"
                                    data-search-highlight="true" data-server-sort="false"
                                    data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('vendor/businesses/business_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-server-sort="false" data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true">
                                                <?= labels('business_name', 'Business Name') ?>
                                            </th>
                                            <th data-field="icon" data-sortable="true"><?= labels('icon', 'Icon') ?>
                                            </th>
                                            <th data-field="description" data-sortable="false">
                                                <?= labels('description', 'Description') ?>
                                            </th>
                                            <th data-field="address" data-sortable="false">
                                                <?= labels('address', 'Address') ?>
                                            </th>
                                            <th data-field="email" data-visible="true"> <?= labels('email', 'Email') ?>
                                            </th>
                                            <th data-field="contact" data-visible="true">
                                                <?= labels('contact ', 'Contact') ?>
                                            </th>
                                            <th data-field="website" data-visible="true">
                                                <?= labels('website', 'Website') ?>
                                            </th>
                                            <th data-field="tax_name" data-visible="false">
                                                <?= labels('tax_name', 'Tax Name') ?>
                                            </th>
                                            <th data-field="tax_value" data-visible="false">
                                                <?= labels('tax_value', 'Tax Value') ?>
                                            </th>
                                            <th data-field="bank_details" data-visible="false">
                                                <?= labels('bank_details', 'Bank Details') ?>
                                            </th>
                                            <th data-field="deafault_business" data-visible="true">
                                                <?= labels('default_business', 'Default Business') ?>
                                            </th>
                                            <th data-field="status" data-visible="true">
                                                <?= labels('status', 'Status') ?>
                                            </th>
                                            <th data-field="action" data-visible="true">
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
    </section>
</div>