<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('add_categories', 'Add Categories') ?></h1>

        </div>
        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-danger"><?php $message = session('message');
            echo $message['title']; ?></label></div>
        <?php } ?>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-body">
                        <form class="form-submit-event" method="post"
                            action="<?= base_url('admin/categories/save_categories'); ?>">
                            <div class="row">
                                <div class="form-group col-md">
                                    <label for="name"> <?= labels('name', 'Name') ?> <small
                                            class="text-danger">*</small></label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        placeholder="Ex. clothes,electronics,accessories etc"
                                        value="<?= !empty($category) && !empty($category['name']) ? $category['name'] : "" ?>"
                                        autofocus>
                                    <input id="category_id" type="hidden" class="form-control" name="category_id"
                                        value="<?= !empty($category) && !empty($category['id']) ? $category['id'] : "" ?>"
                                        autofocus>
                                </div>
                                <div class="form-group col-md">
                                    <label for="parent_id"><?= labels('parent_id', 'Parent ID') ?></label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option
                                            value="<?= !empty($category) && !empty($category['parent_id']) ? $category['parent_id'] : "" ?>"
                                            selected>
                                            <?= !empty($parent_category) && !empty($parent_category['name']) ? $parent_category['name'] : "Select Category" ?>
                                        </option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?= $category['id'] ?>"><?= ucwords($category['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="status" class="custom-switch  p-35">
                                            <input type="checkbox" name="status" id="status" class="custom-switch-input"
                                                checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span
                                                class="custom-switch-description"><?= labels('active', 'Active') ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md">
                                <button type="submit" class="btn btn-primary" id='submit_btn' value="categories">
                                    <?= labels('submit', 'Submit') ?>
                                </button>
                            </div>
                        </form>
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
                                    data-export-types="['txt','excel','csv']" id="admin_categories_table"
                                    data-export-options='{"fileName": "categories-list","ignoreColumn": ["state"]}'
                                    data-auto-refresh="true" data-show-columns="true" data-show-toggle="true"
                                    data-show-refresh="true" data-toggle="table" data-search-highlight="true"
                                    data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('admin/categories/category_table'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-server-sort="false" data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <!-- <th data-radio="true"></th> -->
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?>
                                            </th>
                                            <th data-field="parent_id" data-sortable="true">
                                                <?= labels('parent_id', 'Parent ID') ?>
                                            </th>
                                            <th data-field="parent_category" data-sortable="true" data-visible='true'>
                                                <?= labels('parent_category', 'Parent Category') ?>
                                            </th>
                                            <th data-field="status" data-sortable="true">
                                                <?= labels('status', 'Status') ?>
                                            </th>
                                            <th data-field="action" data-sortable="true">
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

<div class="modal  fade " id="update_cayrgory_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="modal-title"> <?= labels('update_unit', 'Update Unit') ?> </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form class="form-submit-event" method="post"
                    action="<?= base_url('admin/categories/save_categories'); ?>">
                    <div class="row">
                        <div class="form-group col-md">
                            <label for="name"> <?= labels('name', 'Name') ?> <small
                                    class="text-danger">*</small></label>
                            <input id="edit_name" type="text" class="form-control" name="name"
                                placeholder="Ex. clothes,electronics,accessories etc"
                                value="<?= !empty($category) && !empty($category['name']) ? $category['name'] : "" ?>"
                                autofocus>
                            <input id="edit_category_id" type="hidden" class="form-control" name="category_id"
                                value="<?= !empty($category) && !empty($category['id']) ? $category['id'] : "" ?>"
                                autofocus>
                        </div>
                        <div class="form-group col-md">
                            <label for="parent_id"><?= labels('parent_id', 'Parent ID') ?></label>
                            <select name="parent_id" id="edit_parent_id" class="form-control">
                                <option
                                    value="<?= !empty($category) && !empty($category['parent_id']) ? $category['parent_id'] : "" ?>"
                                    selected>
                                    <?= !empty($parent_category) && !empty($parent_category['name']) ? $parent_category['name'] : "Select Category" ?>
                                </option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?= $category['id'] ?>"><?= ucwords($category['name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="edit_status" class="custom-switch  p-35">
                                    <input type="checkbox" name="status" id="edit_status" class="custom-switch-input"
                                        checked>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description"><?= labels('active', 'Active') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md">
                        <button type="submit" class="btn btn-primary" id='submit_btn' value="categories">
                            <?= labels('update_category', 'Update Category') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>