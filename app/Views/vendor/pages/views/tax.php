<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('tax', 'Tax') ?></h1>
        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <table class="table-striped" data-toggle="table"
                                data-url="<?= base_url('vendor/tax-tale') ?>" data-click-to-select="true"
                                data-side-pagination="server" data-pagination="true"
                                data-page-list="[5, 10, 20, 50, 100, 200,All]" data-search="true"
                                data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="true" data-maintain-selected="true"
                                data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="true">Name</th>
                                        <th data-field="percentage" data-sortable="false">Percentage</th>
                                        <th data-field="status" data-sortable="false">Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>