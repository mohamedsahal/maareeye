<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('customers', 'Customers') ?> </h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal"
                        data-bs-target="#bulk_upload_modal" data-toggle="tooltip" data-bs-placement="bottom"
                        title=" <?= labels('bulk_upload', 'Bulk Upload Customers') ?>"><i
                            class="bi bi-cloud-download-fill"></i>
                    </a>
                </div>
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
                            <h2 class="section-title">
                                <?= labels('register_customer_here', 'Register Customer Here') ?>!
                            </h2>
                            <div class="mt-3">
                                <form method="post" action='<?= base_url('vendor/orders/save') ?>'
                                    class="form-submit-event">
                                    <div class="row">
                                        <div class="form-group col-md">
                                            <label for="first_name"><?= labels('name', 'Name') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" id="name"
                                                placeholder="Enter Your Name" name="first_name">
                                            <input type="hidden" name="customer_id" id="customer_id">
                                            <input type="hidden" name="user_id" id="user_id">
                                            <input type="hidden" name="business_id" id="business_id"
                                                value="<?= $business_id ?>">
                                        </div>
                                        <div class="form-group col-md">
                                            <label for="identity"><?= labels('mobile_number', 'Mobile') ?>
                                                <small>(<?= labels('identity', 'Identity') ?>)</small></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" id="identity"
                                                placeholder="Enter Your Mobile Number" name="identity">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md">
                                            <label for="password"><?= labels('password', 'Password') ?>
                                                <small>(<?= labels('password_delivery_boy_text', 'Enter new password if you want to update current password') ?>)</small></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" id="password" value=""
                                                placeholder="Enter Password" name="password">
                                        </div>
                                        <div class="form-group col-md">
                                            <label for="email"><?= labels('email', 'Email') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" id="email"
                                                placeholder="abc@gmail.com" name="email">
                                        </div>
                                        <div class="form-group col-md">
                                            <label for="email"><?= labels('address', 'Address') ?></label><span
                                                class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" id="address"
                                                placeholder="Enter Your Address" name="address">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="status" class="custom-switch p-0">
                                            <input type="checkbox" name="status" id="status" class="custom-switch-input"
                                                checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span
                                                class="custom-switch-description"><?= labels('active', 'Active') ?></span>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="submit_btn" name="register"
                                        value="Save"><?= labels('save', 'Save') ?></button>
                                    <div class="mt-3">
                                        <div id="save-register-result"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-show-export="true"
                                    data-export-types="['txt','excel','csv']"
                                    data-export-options='{"fileName": "customers-list"}' id="customers_table"
                                    data-auto-refresh="true" data-show-columns="true" data-show-toggle="true"
                                    data-show-refresh="true" data-toggle="table" data-search-highlight="true"
                                    data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('vendor/customers/customers_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-server-sort="false" data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-radio="true"></th>
                                            <th data-field="id" data-sortable="true">
                                                <?= labels('customer_id', 'Customer ID') ?>
                                            </th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?>
                                            </th>
                                            <th data-field="email" data-sortable="true"><?= labels('email', 'Email') ?>
                                            </th>
                                            <th data-field="mobile" data-sortable="true">
                                                <?= labels('mobile_number', 'Mobile') ?>
                                            </th>
                                            <th data-field="address" data-sortable="true">
                                                <?= labels('address', 'Address') ?>
                                            </th>
                                            <th data-field="balance" data-sortable="true">
                                                <?= labels('balance', 'Balance') ?>
                                            </th>
                                            <th data-field="status" data-sortable="true">
                                                <?= labels('status', 'Status') ?>
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
<div class="modal" id="customer_register">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Register User</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('vendor/orders/register') ?>' class="form-submit-event">
                    <div class="form-group">
                        <label for="first_name">Name</label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="name" placeholder="Enter Your Name"
                            name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="identity">Mobile <small>(identity)</small></label><span
                            class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="identity" placeholder="Enter Your Mobile Number"
                            name="identity">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="password" value="12345678"
                            placeholder="Enter Password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" placeholder="abc@gmail.com" name="email">
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit_btn" name="register"
                        value="Save">Register</button>
                    <div class="mt-3">
                        <div id="save-register-result"></div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="customer_status">
    <div class="modal-dialog modal-s">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Customer</h4>
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
                                        <form class="form-submit-event"
                                            action="<?= base_url("vendor/customers/save_status") ?>" method="post">
                                            <input type="hidden" value="" name="customer_id">
                                            <div class="row">
                                                <div class="form-group col-md">
                                                    <label for="status"> Status <small
                                                            class="text-danger">*</small></label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="1">Active</option>
                                                        <option value="0">Deactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn  btn-primary" id="submit_btn">Save</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
                        <form action="<?= base_url('vendor/bulk_uploads/import_customers') ?>" method="post"
                            id="bulk_uploads_form">
                            <div class="card ">
                                <div class="row">
                                    <div class="form-group">
                                        <label><?= labels('type<small>(upload)</small>', 'Type <small>(upload)</small>') ?></label>
                                        <select class="form-control" id="type" name="type">
                                            <option value='upload'>Upload</option>
                                            <option value='update'>Update</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label><?= labels('file', 'File') ?></label>
                                        <input type="hidden" id="user_id" name="user_id" value="<?= $business_id ?>">
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
                        <div class="col-12 d-flex gap-3 justify-content-start mt-4">
                            <div>
                                <a href="<?= base_url('public/uploads/bulk_upload_customer.csv') ?>"
                                    class="btn btn-info" download="bulk_upload_customer_sample.csv">Bulk upload
                                    sample file <i class="fas fa-download"></i></a>
                            </div>
                            <div>
                                <a href="<?= base_url('public/uploads/bulk-upload-customers-instructions.txt') ?>"
                                    class="btn btn-success" download="bulk-upload-customers-instructions.txt">Bulk
                                    upload instructions <i class="fas fa-download"></i></a>
                            </div>
                            <div>
                                <a href="<?= base_url('public/uploads/bulk_update_customer.csv') ?>"
                                    class="btn btn-info" download="bulk_update_customer_sample.csv">Bulk update
                                    sample file <i class="fas fa-download"></i></a>
                            </div>
                            <div>
                                <a href="<?= base_url('public/uploads/bulk-update-customers-instructions.txt') ?>"
                                    class="btn btn-success" download="bulk-update-customers-instructions.txt">Bulk
                                    update instructions <i class="fas fa-download"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>