    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Vendors</h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/vendors/create_vendor'); ?>" class="btn"><i class="fas fa-plus"></i> Create Vendor</a>
                    </div>

                </div>
            </div>
            <?= session("message") ?>
            <!--container div  -->
            <div class="row">
                <div class="col-md">
                    <h2 class="section-title">Vendors</h2>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table data-auto-refresh="true" data-show-export="true" data-export-options='{"fileName": "delivery-boys-list"}' data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/vendors/vendor_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true">ID</th>
                                            <th data-field="first_name" data-sortable="true">First Name</th>
                                            <th data-field="last_name" data-sortable="true">Last Name</th>
                                            <th data-field="mobile" data-sortable="true">Mobile</th>
                                            <th data-field="email" data-sortable="true">Email</th>
                                            <th data-field="status" data-visible="true">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--container div  -->
        </section>
    </div>