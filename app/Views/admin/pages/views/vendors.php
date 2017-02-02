<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('vendors', 'Vendors') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/vendors/create'); ?>"
                        data-toggle="tooltip" data-placement="left"
                        title="<?= labels('create_vendor', 'Create Vendor') ?> " class="btn"><i class="fas fa-plus"></i>
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
                                    data-show-export="true" data-export-types="['txt','excel','csv']"
                                    data-export-options='{"fileName": "vendors-list"}' data-show-columns="true"
                                    data-show-toggle="true" data-show-refresh="true" data-toggle="table"
                                    data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('admin/vendors/vendor_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="first_name" data-sortable="true">
                                                <?= labels('first_name', 'First Name') ?>
                                            </th>
                                            <th data-field="last_name" data-sortable="true">
                                                <?= labels('last_name', 'Last Name') ?>
                                            </th>
                                            <th data-field="mobile" data-sortable="true">
                                                <?= labels('mobile_number', 'Mobile') ?>
                                            </th>
                                            <th data-field="email" data-sortable="true"><?= labels('email', 'Email') ?>
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