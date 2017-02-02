<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('invoice', 'Invoice') ?></h1>
        </div>
        <?= session("message"); ?>
        <?php if (!empty($order)) {
            ?>
            <div class="section">
                <div class="section-body">
                    <div class="invoice section-to-print">
                        <div class="invoice-print">
                            <div class="row">
                                <div class="align-items-center col-md-12 d-flex justify-content-between">
                                    <div class="invoice-title">
                                        <h2>Invoice</h2>
                                    </div>
                                    <div class="invoice-number"><?= "#INVOC-" . $order['id'] ?></div>
                                </div>

                                <hr>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 d-flex justify-content-between">
                                    <h2 class="text-left invoice-logo">
                                        <img src="<?= base_url($order['icon']) ?>" class="d-block img-fluid">
                                    </h2>
                                    <div id="section-not-to-print">
                                        <a href="<?= base_url('vendor/invoices/view_invoice/' . $order['id']); ?>"
                                            class="btn btn-primary" target="_blank"><i class="bi bi-file-pdf"></i> PDF</a>
                                        <a class="btn btn-success" data-order_id="<?= $order['id'] ?>"
                                            data-email="<?= $order['email']; ?>" id="send_invoice" target="_blank"><i
                                                class="bi bi-envelope"></i> Send</a>
                                        <a href="<?= base_url('vendor/invoices/thermal_print/' . $order['id']) ?>"
                                            class="btn btn-warning icon-left" target="_blank"><i
                                                class="fas fa-print"></i>Thermal Print</a>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="align-items-baseline col-md-12 d-flex justify-content-between">
                                        <div>
                                            <strong><?= $order['business_name'] . " - " . $order['description'] ?></strong><br>
                                            <address>
                                                <strong>Address : </strong><?= $order['address'] ?><br>
                                                <strong>Contact : </strong> <?= $order['contact'] ?><br>
                                                <?php if (isset($order['warehouse_id']) && !empty($order['warehouse_id'])) { ?>
                                                    Warehouse : <?= ucfirst($order['warehouse_name']) ?><br>
                                                <?php } ?>
                                                <strong><?= $order['b_tax'] . " : " ?></strong><?= $order['tax_value'] . '%' ?>
                                            </address>
                                        </div>
                                        <div class="text-right">
                                            <address>
                                                <strong>Billed To:</strong><br>
                                                <?= $order['first_name'] . " " . $order['last_name'] ?><br>
                                                <?= $order['mobile'] ?><br>
                                                <?= $order['email'] ?><br>
                                            </address>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-between align-items-baseline">
                                        <div>
                                            <address>
                                                <strong>Payment Method:</strong><br>
                                                <?= $order['payment_method'] ?><br>
                                            </address>
                                        </div>
                                        <div class="text-right">
                                            <address>
                                                <strong>Order Date:</strong><br>
                                                <?= $order['created_at'] ?><br><br>
                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="section-title">Order Summary</div>
                                <p class="section-lead">All items here cannot be deleted.</p>
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderd" id="invoice_table"
                                        data-show-export="false" data-export-types="['txt','excel','csv']"
                                        data-export-options='{"fileName": "invoice-order-list","ignoreColumn": ["action"]}'
                                        data-auto-refresh="false" data-show-columns="false" data-show-toggle="false"
                                        data-show-refresh="false" data-toggle="table" data-search-highlight="true"
                                        data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                        data-url="<?= base_url('vendor/invoices/invoice_table/' . $order['id']); ?>"
                                        data-side-pagination="server" data-pagination="false" data-search="false"
                                        data-sort-name="id" data-sort-order="desc">
                                        <thead>
                                            <tr>
                                                <th data-field="order_type" data-sortable="true" data-visible="true">
                                                    Product/Service</th>
                                                <th data-field="name" data-sortable="true" data-visible="true">Name</th>
                                                <th data-field="price" data-sortable="true" data-visible="true">Price</th>
                                                <th data-field="tax" data-sortable="true" data-visible="true">Tax</th>
                                                <th data-field="tax_amount" data-sortable="true" data-visible="true">Tax
                                                    amount ( <?= currency_location('') ?> )</th>
                                                <th data-field="quantity" data-sortable="true" data-visible="true">Quantity
                                                </th>
                                                <th data-field="subtotal" data-sortable="true" data-visible="true">Subtotal
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-6">
                                        <div class="section-title">Payment Summary</div>
                                        <?php if ($order['payment_status'] == "fully_paid") { ?>
                                            <p class="section-lead"><?= "<strong>Fully Paid</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "partially_paid") { ?>
                                            <p class="section-lead"><?= "<strong>Partially Paid</strong>" ?></p>
                                            <p class="section-lead"><?= "<strong>" . $order['amount_paid'] . "</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "unpaid") { ?>
                                            <p class="section-lead"><?= "<strong>No Payment of order found!</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "cancelled") { ?>
                                            <p class="section-lead"><?= "<strong>Cancelled</strong>" ?></p>
                                        <?php } ?>

                                    </div>
                                    <div class="col-lg-6">
                                        <table class="table table-borderless table-sm text-right">
                                            <tbody>
                                                <tr>
                                                    <th>Delivery Charges</th>
                                                    <td><?= currency_location(number_format($order['delivery_charges'], 2)) ?>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <th>Discount</th>
                                                    <td><?= currency_location(number_format($order['discount'], 2)) ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Total</th>
                                                    <td><strong><?= currency_location(number_format($order['final_total'], 2)) ?></strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
    </div>
<?php } else { ?>
    <div class="section">
        <div class="section-body">
            <div class="invoice">
                <div class="invoice-print">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 d-flex justify-content-between">
                            <h2 class="text-left invoice-logo">
                                <img class="d-block img-fluid">
                            </h2>
                        </div>
                        <h6 class="text-left">
                            <?php
                            if ($order['order_type'] === 'product') {
                                echo $order['product_name'] . " - " . $order['description'];
                            } elseif ($order['order_type'] === 'service') {
                                echo $order['service_name'] . " - " . $order['description'];
                            }
                            ?>
                        </h6>
                        <address>
                            Address: <br>
                            Contact: <br>
                            <strong></strong>
                        </address>
                        <div class="invoice-title col-md-12 col-sm-12 d-flex justify-content-between">
                            <h2>Invoice</h2>
                        </div>
                        <div class="invoice-number"> "#INVOC-"</div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <address>
                                    <strong>Billed To:</strong><br>

                                </address>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <address>
                                    <strong>Payment Method:</strong><br>
                                    <br>
                                </address>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <address>
                                    <strong>Order Date:</strong><br>


                                    <br><br>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="section-title">Order Summary</div>
                        <p class="section-lead">All items here cannot be deleted.</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-md">

                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-8">
                                <div class="section-title">Payment Method</div>
                                <p class="section-lead">The payment method that we provide is to make it easier for
                                    you to pay invoices.</p>
                                <div class="images">

                                </div>
                            </div>
                            <div class="col-lg-6 text-right">
                                <div class="invoice-detail-item">
                                    <div class="invoice-detail-name">Subtotal</div>
                                    <div class="invoice-detail-value"></div>
                                </div>
                                <div class="invoice-detail-item">
                                    <div class="invoice-detail-name">Shipping</div>
                                    <div class="invoice-detail-value"></div>
                                </div>
                                <hr class="mt-2 mb-2">
                                <div class="invoice-detail-item">
                                    <div class="invoice-detail-name">Total</div>
                                    <div class="invoice-detail-value invoice-detail-value-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
<?php } ?>
</section>
</div>