<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('database_backup', 'Database Backup') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card card-primary">
            <div class="card-body">
                <div class="align-items-center col-md-12 d-flex gap-5 justify-content-start">
                    <div>
                        <h4><?= labels('backup_database', 'Database Backup') ?></h4>
                        <span class="form-group texted-muted">
                            Backup Your Current Database From Here
                        </span>
                    </div>
                    <div>
                        <button id="backup_database" class="btn btn-primary m-2">Backup</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card-body">
                    <table class="table table-hover table-borderd" data-show-export="true"
                        data-export-types="['txt','excel','csv','json']"
                        data-export-options='{"fileName": "Best Customers","ignoreColumn": ["action"]}'
                        data-auto-refresh="true" data-show-columns="true" data-show-toggle="true"
                        data-show-refresh="true" data-toggle="table" data-search-highlight="true"
                        data-page-list="[5, 10, 25, 50, 100, 200, All]"
                        data-url="<?= base_url('admin/database/backup'); ?>" data-side-pagination="server"
                        data-pagination="true" data-search="true" data-query-params="backup_query" id="backup_table"
                        data-sort-name="id" data-sort-order="desc">
                        <thead>
                            <tr>
                                <th data-field="no_of_files" data-sortable="true" data-visible="true">
                                    <?= labels('no_of_files', 'N.O.') ?>
                                </th>
                                <th data-field="file" data-sortable="true" data-visible="true">
                                    <?= labels('file', 'File') ?>
                                </th>
                                <th data-field="date" data-sortable="true" data-visible="true">
                                    <?= labels('date', 'Date') ?>
                                </th>
                                <th data-field="server_path" data-sortable="true" data-visible="true">
                                    <?= labels('server_path', 'Server Path') ?>
                                </th>
                                <th data-field="action" data-sortable="true" data-visible="true">
                                    <?= labels('action', 'Action') ?>
                                </th>

                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>

<div class="modal" id="mail_DBbackup">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Mail Database Backup</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="">
                    <div class="container">
                        <div class="row">
                            <form class="form-submit-event" action="<?= site_url('admin/database/mail_database') ?>"
                                method="POST">
                                <div class="form-group row align-items-center">
                                    <label for="email-set" class="control-label"><?= labels('email', 'Email') ?> <span
                                            class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-12 col-md-12">
                                        <input type="text" name="email" class="form-control" id="email-set" value=""
                                            required="" dir="ltr">
                                    </div>
                                </div>
                                <input type="hidden" id="file_id" name="file_name" />
                                <div class="form-group row align-items-center">
                                    <label for="message"
                                        class="control-label"><?= labels('message', 'Message') ?></label>
                                    <div class="col-sm-12 col-md-12">
                                        <input type="text" name="message" class="form-control" id="message" value=""
                                            dir="ltr">
                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <div class="form-group  align-items-center">
                                        <button type="submit" id='submit_btn' class="btn btn-primary" value="mail">
                                            <?= labels('mail', 'Mail') ?>
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>