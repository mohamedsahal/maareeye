<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('status', 'Status') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" id="" href="<?= base_url('vendor/orders/create_status'); ?>"
                        data-bs-toggle="modal" data-bs-target="#status_modal" data-bs-placement="bottom"
                        title="<?= labels('add_status', 'Add Status') ?> "><i class="fas fa-plus"></i> </a>
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
                                    data-url="<?= base_url('vendor/orders/status_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true" data-visible="true">
                                                <?= labels('id', 'Id') ?>
                                            </th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?>
                                            </th>
                                            <th data-field="operation" data-sortable="true">
                                                <?= labels('operation', 'Operation (what to do with wallet balance?)') ?>
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

<div class="modal" id="status_modal">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('create_status', "Create Status") ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('vendor/orders/create_status') ?>' class="form-submit-event">
                    <div class="form-group">
                        <label for="status"><?= labels('status_name', "Status Name") ?></label><span
                            class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="status" placeholder="Ex. Ordered ,pending,delivered"
                            name="status">
                    </div>
                    <div class="form-group">
                        <label for="operation"><?= labels('operation', "Operation") ?></label><span
                            class="asterisk text-danger"> *</span>
                        <button type="button" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Ex. Debit From wallet balance, Credit balance in wallet ,do nothing etc.">
                            <small>(
                                <?= labels('what_to_do_with_wallet_balance', "what to do with wallet balance?") ?>)</small>
                        </button>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="do_nothing" name="operation" value="0" class="custom-control-input"
                                checked>
                            <label class="custom-control-label"
                                for="do_nothing"><?= labels('do_nothing', "Do nothing") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="credit" name="operation" value="1" class="custom-control-input">
                            <label class="custom-control-label"
                                for="credit"><?= labels('credit_wallet_balance', "Credit wallet balance") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="debit" name="operation" value="2" class="custom-control-input">
                            <label class="custom-control-label"
                                for="debit"><?= labels('debit_wallet_balance', "Debit wallet balance") ?></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"
                            id="submit_btn"><?= labels('save', 'Save') ?></button>
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>