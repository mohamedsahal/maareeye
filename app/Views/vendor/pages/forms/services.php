<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('services', 'Services') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('vendor/services'); ?>" class="btn"><i
                            class="fas fa-list"></i> <?= labels('services', 'Services') ?></a>
                </div>
            </div>
        </div>

        <?php

        $session = session();
        if ($session->has("message")) { ?>
            <div class="text-red"><?= session("message"); ?></label></div>
        <?php } ?>
        <div class="section-body">
            <div class="row mt-sm-4">
                <div class='col-md-12'>
                    <h2 class="section-title">
                        <?= !empty($services) ? labels('update_service', 'Update Service') : labels('add_service', 'Add Service') ?>
                    </h2>
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('vendor/services/save_services') ?>" class="form-submit-event"
                                enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="name"><?= labels('name', 'Name') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="<?= !empty($services) && !empty($services['name']) ? $services['name'] : "" ?>">
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
                                                id="description"><?= !empty($services) && !empty($services['description']) ? $services['description'] : "" ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md d-flex gap-5">

                                        <div class='col-md-6 form-group'>
                                            <a class="uploadFile media_link" data-input='service_input_image'
                                                data-isremovable='0' data-is-multiple-uploads-allowed='0'
                                                data-bs-toggle="modal" data-bs-target="#media-upload-modal"
                                                value="Upload Photo">
                                                <div class="col-md-12 file_upload_box border file_upload_border">
                                                    <div class="mt-2">
                                                        <div class="col-md-12  text-center">
                                                            <div>
                                                                <p class="caption text-dark-secondary">Choose image
                                                                    for service.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php if (isset($services) && !empty($services['image'])) { ?>
                                                <div class="row image-upload-section">
                                                    <div
                                                        class="bg-white image-box-200 grow image mb-5 mt-4 p-3 rounded shadow text-center">
                                                        <div class="image-upload-div"><img class="img-fluid mb-2"
                                                                src="<?= base_url($services['image']) ?>"
                                                                alt="Image Not Found"></div>
                                                        <input type="hidden" name="service_input_image"
                                                            value='<?= $services['image'] ?>'>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="row image-upload-section">
                                                    <div
                                                        class="bg-white image-box-200 grow image mb-5 mt-4 p-3 rounded shadow text-center d-none">
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md form-group">
                                            <label for="unit_id"><?= labels('unit', 'Unit') ?></label>
                                            <select class="form-control" id="unit_id" name="unit_id">
                                                <option value="">-<?= labels('none', 'None') ?>-</option>
                                                <?php foreach ($units as $unit) { ?>
                                                    <option value="<?= $unit['id'] ?>" <?= (!empty($services['unit_id']) && $services['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                                        <?= $unit['name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="price"><?= labels('price', 'Price') ?></label><span
                                                    class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" id="price" name="price"
                                                    min="0.00" placeholder="0.00"
                                                    value="<?= !empty($services) && !empty($services['price']) ? $services['price'] : "" ?>">
                                            </div>
                                        </div>

                                        <div class="col-md">
                                            <div class="form-group">
                                                <label
                                                    for="cost_price"><?= labels('cost_price', 'Cost Price') ?></label><span
                                                    class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" name="cost_price"
                                                    id="cost_price" min="0.00" placeholder="0.00"
                                                    value="<?= !empty($services) && !empty($services['cost_price']) ? $services['cost_price'] : "" ?>">
                                            </div>
                                        </div>

                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="is_tax_inlcuded" class="custom-switch  p-35">
                                                    <?php if (!empty($services['is_tax_included']) && $services['is_tax_included'] == "1") { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded"
                                                            class="custom-switch-input" checked>
                                                    <?php } elseif (isset($services['is_tax_included']) && $services['is_tax_included'] == "0") { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded"
                                                            class="custom-switch-input">
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded"
                                                            class="custom-switch-input" checked>
                                                    <?php } ?>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description"><?= labels('is_tax_included', 'Is tax included?') ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="tax_rows">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="service_taxes"><?= labels('order_tax', 'Order Tax') ?>
                                                </label>
                                                <div>
                                                    <input name='service_taxes' id="service_taxes"
                                                        class='some_class_name mb-3 p-1' placeholder='write some tags'>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="is_recursive" class="custom-switch  p-35">
                                                    <?php if (!empty($services['is_recursive']) && $services['is_recursive'] == "1") { ?>
                                                        <input type="checkbox" name="is_recursive" id="is_recursive"
                                                            class="custom-switch-input" checked>
                                                    <?php } elseif (isset($services['is_recursive']) && $services['is_recursive'] == "0") { ?>
                                                        <input type="checkbox" name="is_recursive" id="is_recursive"
                                                            class="custom-switch-input">
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="is_recursive" id="is_recursive"
                                                            class="custom-switch-input">
                                                    <?php } ?>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description"><?= labels('is_recursive', 'is recursive?') ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md recursive">
                                            <div class="form-group">
                                                <label
                                                    for="recurring_days"><?= labels('recurring_days', 'Recurring Days') ?></label>
                                                <input type="number" class="form-control" name="recurring_days"
                                                    id="recurring_days"
                                                    value="<?= !empty($services) && !empty($services['recurring_days']) ? $services['recurring_days'] : "" ?>">
                                            </div>
                                        </div>
                                        <div class="col-md recursive">
                                            <div class="form-group">
                                                <label
                                                    for="recurring_price"><?= labels('recurring_price', 'Recurring Price') ?></label>
                                                <input type="number" class="form-control" name="recurring_price"
                                                    id="recurring_price" min="0.00" placeholder="0.00"
                                                    value="<?= !empty($services) && !empty($services['recurring_price']) ? $services['recurring_price'] : "" ?>">
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
                                </div>
                                <div class="card-footer text-left">
                                    <input type="hidden" name="service_id" id="service_id"
                                        value="<?= !empty($services) && !empty($services['id']) ? $services['id'] : "" ?>">
                                    <input type="hidden"
                                        value='<?= !empty($services_tax_value) ? $services_tax_value : '' ?>'
                                        id="service_taxes_values">
                                    <button id="submit_btn"
                                        class="btn btn-primary"><?= !empty($services['id']) ? labels('update', 'Update') : labels('add', 'Add') ?></button>
                                    <button type="reset" class="btn btn-info"><?= labels('reset', 'Reset') ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </section>
</div>