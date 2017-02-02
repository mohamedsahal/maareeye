<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('Open Tickets', 'Open Tickets') ?></h1>
            <div class="section-header-breadcrumb">

            </div>
        </div>
        <div class="row">
            <div class="col-md">
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div class="alert alert-danger d-none" id="add_subscription_result"> </div>
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has("message")) { ?>
            <div class="text-red"><?= session("message"); ?></label></div>
        <?php } ?>

        <div class="section">
            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover table-borderd" data-show-export="true"
                                    data-export-types="['txt','excel','csv']"
                                    data-export-options='{"fileName": "opentickets-list","ignoreColumn": ["action"]}'
                                    id="open_tickets" data-auto-refresh="true" data-show-columns="true"
                                    data-show-toggle="true" data-show-refresh="true" data-toggle="table"
                                    data-search-highlight="true" data-server-sort="false"
                                    data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('admin/open_tickets/open_tickets'); ?>"
                                    data-side-pagination="server" data-pagination="true" data-search="true"
                                    data-server-sort="false" data-sort-name="id" data-sort-order="desc">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="user_id" data-sortable="true">
                                                <?= labels('user_id', 'User Id') ?>
                                            </th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?>
                                            </th>
                                            <th data-field="email" data-sortable="false"><?= labels('email', 'Email') ?>
                                            </th>
                                            <th data-field="priority" data-sortable="false">
                                                <?= labels('priority', 'Priority') ?>
                                            </th>
                                            <th data-field="message" data-visible="true">
                                                <?= labels('message', 'Message') ?>
                                            </th>
                                            <th data-field="message_admin" data-visible="true">
                                                <?= labels('message_admin', 'Message Admin') ?>
                                            </th>
                                            <th data-field="action" data-visible="true">
                                                <?= labels('action', 'Action') ?>
                                            </th>
                                            <th data-field="status" data-visible="true">
                                                <?= labels('status', 'Status') ?>
                                            </th>
                                            <th data-field="created_at" data-visible="false">
                                                <?= labels('created_at', 'Created_at') ?>
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


    </section>
</div>
<div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="editTicketModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #007bff; color: #fff;">
                <h5 class="modal-title" id="editTicketModalLabel" style="margin-right: auto;">Replay To Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #fff; opacity: 1;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <!-- Your edit form fields here -->
                <form action="<?= base_url('admin/open_tickets/submit') ?>" class="form-submit-event"
                    enctype="multipart/form-data" accept-charset="utf-8" method="POST">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message" style="color: #000;">Your Message<span
                                        class="asterisk text-danger"> *</span></label>
                                <textarea name="message_admin" class="form-control" id="message"
                                    style="resize: none;"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id='submit_btn'
                        style="background-color: #28a745; color: #fff; border-color: #28a745;">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>



    function openEditModal(id, userId) {
        // Assuming you have a modal with an id 'editModal'
        $('#editModal').modal('show');

        // Populate form fields with the provided id and user_id
        $('#editForm #id').val(id);
        $('#editForm #user_id').val(userId);
    }

    function acceptTicket(ticketId) {

        // Show a confirmation prompt
        if (confirm("Are you sure you want to accept this ticket?")) {
            $.ajax({
                url: '<?= base_url('admin/open_tickets/updateTicketStatus'); ?>',
                type: 'POST',
                data: { id: ticketId },
                status: 'Active',
                success: function (response) {
                    if (response.success) {
                        console.log(response.message);
                        // You can also update the UI here if needed
                    } else {
                        console.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating ticket status:', error);
                }
            });
        } else {
            console.log("Ticket acceptance cancelled.");
        }
    }

    function closeTicket(ticketId) {
        // Show a confirmation prompt
        if (confirm("Are you sure you want to close this ticket?")) {
            $.ajax({
                url: '<?= base_url('admin/open_tickets/closeTicket'); ?>',
                type: 'POST',
                data: { id: ticketId },
                success: function (response) {
                    if (response.success) {
                        console.log(response.message);
                        // You can also update the UI here if needed
                    } else {
                        console.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error closing ticket:', error);
                }
            });
        } else {
            console.log("Ticket closure cancelled.");
        }
    }

    function openEditModal(ticketId) {
        // Fetch ticket data using AJAX and populate the edit form fields
        // Example:
        // $.ajax({
        //     url: 'fetch_ticket_data.php',
        //     method: 'GET',
        //     data: { id: ticketId },
        //     success: function(response) {
        //         // Populate the edit form fields with ticket data
        //         $('#editTicketModal').modal('show');
        //     }
        // });
        // For simplicity, I'm assuming the edit form is already in your HTML and its id is 'editTicketModal'
        $('#editTicketModal').modal('show');
    }



</script>