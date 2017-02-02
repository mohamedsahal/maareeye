<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Package</h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/packages'); ?>" class="btn"><i
                            class="fas fa-list"></i> Packages</a>
                </div>
            </div>
        </div>

        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-danger"><?php $message = session('message');
            echo $message['title']; ?></label></div>
        <?php }
        $count = count($tenure);
        foreach ($packages as $package) {
            ?>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class='col-md-12'>
                        <div class="card">
                            <div class="card-body">
                                <form action="<?= base_url('admin/packages/update_package'); ?>" id="edit_package_form"
                                    class="form-submit-event" method="POST">
                                    <h2 class="section-title"> Create Package </h2>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="title">Title</label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="title" id="title"
                                                    placeholder="" value="<?php echo $package['title']; ?>">
                                                <input type="hidden" name="id" id="id" placeholder=""
                                                    value="<?= $package['id']; ?>">
                                                <textarea class="d-none"
                                                    id="tenures"><?= json_encode($package['tenures']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_of_businesses">No. of businesses</label><span
                                                    class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" name="no_of_businesses"
                                                    id="no_of_businesses" placeholder="" min="1"
                                                    value="<?php echo $package['no_of_businesses']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_of_delivery_boys">No. of delivery boys</label> <small>(Enter
                                                    -1 to allow unlimited delivery boys)</small>
                                                <input type="number" class="form-control" name="no_of_delivery_boys"
                                                    id="no_of_delivery_boys" placeholder=""
                                                    value="<?php echo $package['no_of_delivery_boys']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_of_products">No. of products</label> <small>(Enter -1 to
                                                    allow unlimited products)</small>
                                                <input type="number" class="form-control" name="no_of_products"
                                                    id="no_of_products" placeholder=""
                                                    value="<?php echo $package['no_of_products']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_of_customers">No. of customers</label> <small>(Enter -1 to
                                                    allow unlimited customers)</small>
                                                <input type="number" class="form-control" name="no_of_customers"
                                                    id="no_of_customers" placeholder=""
                                                    value="<?php echo $package['no_of_customers']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label
                                                    for="no_of_businesses"><?= labels('no_of_warehouse', 'No. of warehouse ') ?></label><span
                                                    class="asterisk text-danger"> *</span><small>(Enter -1 to allow
                                                    unlimited warehouses)</small>
                                                <input type="number" class="form-control" name="no_of_warehouse"
                                                    id="no_of_warehouse" placeholder=""
                                                    value="<?= isset($package['no_of_warehouse']) ? $package['no_of_warehouse'] : "-1" ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label
                                                    for="no_of_brands"><?= labels('no_of_brands', 'No. of Brands ') ?></label><span
                                                    class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" name="no_of_brands"
                                                    id="no_of_brands" placeholder=""
                                                    value="<?= isset($package['no_of_brands']) ? $package['no_of_brands'] : "" ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="description">Description</label><span
                                                    class="asterisk text-danger"> *</span>
                                                <textarea class="form-control h-100" name="description"
                                                    id="description"><?php echo $package['description']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?= labels('package_type', 'Package type') ?></label><span
                                                    class="asterisk text-danger"> *</span>

                                                <select name="package_type" id="package_type" class="form-control">
                                                    <?php if (isset($package['type']) && !empty($package['type']) && $package['type'] == "free") { ?>
                                                        <option value="<?= $package['type'] ?>" selected="">
                                                            <?= ucwords($package['type']) ?>
                                                        </option>
                                                        <option value="paid">Paid</option>
                                                    <?php } else if (isset($package['type']) && !empty($package['type']) && $package['type'] == "paid") { ?>
                                                            <option value="<?= $package['type'] ?>" selected="">
                                                            <?= ucwords($package['type']) ?>
                                                            </option>
                                                            <option value="free">Free</option>
                                                    <?php } else { ?>
                                                            <option value="paid" selected>Paid</option>
                                                            <option value="free">Free</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label class="custom-switch pl-0"> Status
                                                    &nbsp;&nbsp;
                                                    <input type="checkbox" name="status" id="status"
                                                        class="custom-switch-input" class="form-control" checked>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h2 class="section-title"> Tenure details </h2>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="container-fluid">
                                                <div class="row custom-table-header">

                                                    <div class="col-md-3 custom-col">
                                                        Tenure<span class="asterisk text-danger"> *</span>
                                                    </div>
                                                    <div class="col-md-3 custom-col">
                                                        Month(s) </div>
                                                    <div class="col-md-2 custom-col">
                                                        Price(₹)<span class="asterisk text-danger"> *</span>
                                                    </div>

                                                    <div class="col-md-2 custom-col">
                                                        Discounted Price(₹)
                                                    </div>
                                                    <div class="col-md-1 custom-col">
                                                        Action </div>
                                                </div><br>


                                                <div id="tenure_items">
                                                    <div class="tenure-item py-1">
                                                        <div class="row">
                                                            <div class="col-md-3 custom-col">
                                                                <input type="text" class="form-control" id="tenure"
                                                                    name="tenure[]"
                                                                    placeholder="Ex.Monthly,Quarterly,Yearly">
                                                            </div>
                                                            <div class="col-md-3 custom-col">
                                                                <select class="form-control" id="months" name="months[]">
                                                                    <option value="">Select Months</option>
                                                                    <?php
                                                                    for ($i = 1; $i <= 36; $i++) { ?>
                                                                        <option value="<?= $i ?>"><?= $i ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2 custom-col">
                                                                <input type="number" class="form-control" id="price"
                                                                    name="price[]" min="0" placeholder="0.00">
                                                            </div>

                                                            <div class="col-md-2 custom-col">
                                                                <input type="number" class="form-control"
                                                                    id="discounted_price" name="discounted_price[]" min="0"
                                                                    placeholder="0.00">
                                                            </div>
                                                            <div class="col-md-1 custom-col">
                                                                <button class="btn btn-icon btn-success" id="add_tenure"><i
                                                                        class="fas fa-plus"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- tenure data -->
                                                <div id="tenures_div">
                                                </div>
                                                <hr>
                                                <!--  -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md">
                                                <div class="alert alert-danger d-none" id="create_package_result"> </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="card-footer col-md-6">
                                                    <button class="btn btn-primary mb-2" type="submit"
                                                        id='submit_btn'>Update</button>
                                                    <div id="result" class="disp-none"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
</div>