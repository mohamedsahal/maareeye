/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
$(function () {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);
    path = document.location.href;

    $(".sidebar-menu li a").each(function () {
        var href = $(this).attr("href");
        if (href === path) {
            $('.navbar li a').removeClass('active')
            $(this).closest("li").addClass("active");
            $(this).closest("li .nav-item .dropdown").addClass("active");
            if ($(this).parents().hasClass('dropdown-menu')) {
                $(this).parents().addClass('active')
                $(this).parents().show();
            }

        }
    });
});

let userId = $("#user_id").val();

if (jQuery().summernote) {
    $(".summernote").summernote({
        dialogsInBody: true,
        minHeight: 250,
    });
    $(".summernote-simple").summernote({
        dialogsInBody: true,
        minHeight: 150,
        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["font", ["strikethrough"]],
            ["para", ["paragraph"]],
        ],
    });
}
tinymce.init({
    selector: '.texteditor',
    height: "480"
});


// Login form 
$('#login_form').on('submit', function (e) {

    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#login_btn').html('Please Wait..');
            $('#login_btn').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                var message = "";
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message[key],
                        position: 'topRight'
                    });
                });
            } else {
                if (result.vendor == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/vendor/home";
                    }, 500);
                }
                if (result.admin == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/admin/home";
                    }, 500);

                }
                if (result.delivery_boy == true) {
                    showToastMessage(result.message, "success")
                    setTimeout(function () {
                        location.href = base_url + "/delivery_boy/home";
                    }, 500);
                }
            }
        }
    });

});

$(document).on('click', '.remove_tenure', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
});
// Edit Package-form
$(document).ready(function () {
    if ($('#edit_package_form').length > 0) {
        var tenures = $('#tenures').val();
        if (tenures) {
            tenures = JSON.parse(tenures);
            var html = "";
            $.each(tenures, function (i, tenure) {

                html = '<div class="tenure-item py-1"><div class="row"><div class="col-md-3 custom-col">' +
                    '<input type="text" class="form-control" class="tenure" name="tenure[]" placeholder="Ex.Monthly,Quarterly,Yearly" value="' + tenure['tenure'] + '" required></div>' +
                    '<div class="col-md-3 custom-col">' +
                    '<select class="form-control" class="months" name="months[]" value="' + tenure['months'] + '" required>' +
                    ' <option value="">Select Months</option>' +
                    '<option value="1" ' + ((tenure['months'] == 1) ? "selected" : "") + '>1</option><option value="2" ' + ((tenure['months'] == 2) ? "selected" : "") + '>2</option><option value="3" ' + ((tenure['months'] == 3) ? "selected" : "") + '>3</option><option value="4" ' + ((tenure['months'] == 4) ? "selected" : "") + '>4</option>' +
                    '<option value="5" ' + ((tenure['months'] == 5) ? "selected" : "") + '>5</option><option value="6" ' + ((tenure['months'] == 6) ? "selected" : "") + '>6</option><option value="7" ' + ((tenure['months'] == 7) ? "selected" : "") + '>7</option><option value="8" ' + ((tenure['months'] == 8) ? "selected" : "") + '>8</option><option value="9" ' + ((tenure['months'] == 9) ? "selected" : "") + '>9</option><option value="10" ' + ((tenure['months'] == 10) ? "selected" : "") + '>10</option>' +
                    '<option value="11" ' + ((tenure['months'] == 11) ? "selected" : "") + '>11</option><option value="12" ' + ((tenure['months'] == 12) ? "selected" : "") + '>12</option><option value="13" ' + ((tenure['months'] == 13) ? "selected" : "") + '>13</option><option value="14" ' + ((tenure['months'] == 14) ? "selected" : "") + '>14</option><option value="15" ' + ((tenure['months'] == 15) ? "selected" : "") + '>15</option><option value="16" ' + ((tenure['months'] == 16) ? "selected" : "") + '>16</option>' +
                    '<option value="17" ' + ((tenure['months'] == 17) ? "selected" : "") + '>17</option><option value="18" ' + ((tenure['months'] == 18) ? "selected" : "") + '>18</option><option value="19" ' + ((tenure['months'] == 19) ? "selected" : "") + '>19</option><option value="20" ' + ((tenure['months'] == 20) ? "selected" : "") + '>20</option><option value="21" ' + ((tenure['months'] == 21) ? "selected" : "") + '>21</option><option value="22" ' + ((tenure['months'] == 22) ? "selected" : "") + '>22</option>' +
                    '<option value="23" ' + ((tenure['months'] == 23) ? "selected" : "") + '>23</option><option value="24" ' + ((tenure['months'] == 24) ? "selected" : "") + '>24</option><option value="25" ' + ((tenure['months'] == 25) ? "selected" : "") + '>25</option><option value="26" ' + ((tenure['months'] == 26) ? "selected" : "") + '>26</option><option value="27" ' + ((tenure['months'] == 27) ? "selected" : "") + '>27</option><option value="28" ' + ((tenure['months'] == 28) ? "selected" : "") + '>28</option>' +
                    '<option value="29" ' + ((tenure['months'] == 29) ? "selected" : "") + '>29</option><option value="30" ' + ((tenure['months'] == 30) ? "selected" : "") + '>30</option><option value="31" ' + ((tenure['months'] == 31) ? "selected" : "") + '>31</option><option value="32" ' + ((tenure['months'] == 32) ? "selected" : "") + '>32</option><option value="33">33</option><option value="34" ' + ((tenure['months'] == 34) ? "selected" : "") + '>34</option>' +
                    '<option value="35" ' + ((tenure['months'] == 35) ? "selected" : "") + '>35</option><option value="36" ' + ((tenure['months'] == 36) ? "selected" : "") + '>36</option></select></div>' +
                    '<div class="col-md-2 custom-col"><input type="number" class="form-control" class="price" name="price[]" min="0.00" placeholder="0.00" value="' + tenure['price'] + '" required>' +
                    '</div><div class="col-md-2 custom-col"><input type="number" class="form-control" class="discounted_price" name="discounted_price[]" min="0" value="' + tenure['discounted_price'] + '" placeholder="0.00"></div>' +
                    ' <div class="col-md-1 custom-col"><button class="btn btn-icon btn-danger remove-tenure-item remove_tenure" data-tenure_id="' + tenure['id'] + '" name="remove_tenure"><i class="fas fa-trash"></i></button></div>' +
                    '<input type="hidden" name="tenure_id[]" id="tenure_id"  value="' + tenure['id'] + '">'
                '</div></div>';
                $('#tenures_div').append(html);
            });
        }
    }
    $(document).on("click", "#add_tenure", function (e) {
        e.preventDefault();
        validate_tenure();
    });

    function validate_tenure() {
        var tenure = $('#tenure').val();
        var price = $('#price').val();
        var months = $('#months').val();
        var discounted_price = $('#discounted_price').val();
        //Ajax post
        if (tenure == null || tenure == "") {
            iziToast.error({
                title: 'Error!',
                message: "Tenure cannot be blank",
                position: 'topRight'
            });
            return;
        } else if (price == null || price == "") {
            iziToast.error({
                title: 'Error!',
                message: "Price cannot be blank",
                position: 'topRight'
            });
            return;
        } else {
            html = '<div class="tenure-item py-1"><div class="row"><div class="col-md-3 custom-col">' +
                '<input type="text" class="form-control" class="tenure" name="tenure[]" placeholder="Ex.Monthly, Quarterly, Yearly" value="' + tenure + '" required></div>' +
                '<div class="col-md-3 custom-col">' +
                '<select class="form-control" class="months" name="months[]" required>' +
                ' <option value="">Select Months</option>' +
                '<option value="1" ' + ((months == 1) ? "selected" : "") + '>1</option><option value="2" ' + ((months == 2) ? "selected" : "") + '>2</option><option value="3" ' + ((months == 3) ? "selected" : "") + '>3</option><option value="4" ' + ((months == 4) ? "selected" : "") + '>4</option>' +
                '<option value="5" ' + ((months == 5) ? "selected" : "") + '>5</option><option value="6" ' + ((months == 6) ? "selected" : "") + '>6</option><option value="7" ' + ((months == 7) ? "selected" : "") + '>7</option><option value="8" ' + ((months == 8) ? "selected" : "") + '>8</option><option value="9" ' + ((months == 9) ? "selected" : "") + '>9</option><option value="10" ' + ((months == 10) ? "selected" : "") + '>10</option>' +
                '<option value="11" ' + ((months == 11) ? "selected" : "") + '>11</option><option value="12" ' + ((months == 12) ? "selected" : "") + '>12</option><option value="13" ' + ((months == 13) ? "selected" : "") + '>13</option><option value="14" ' + ((months == 14) ? "selected" : "") + '>14</option><option value="15" ' + ((months == 15) ? "selected" : "") + '>15</option><option value="16" ' + ((months == 16) ? "selected" : "") + '>16</option>' +
                '<option value="17" ' + ((months == 17) ? "selected" : "") + '>17</option><option value="18" ' + ((months == 18) ? "selected" : "") + '>18</option><option value="19" ' + ((months == 19) ? "selected" : "") + '>19</option><option value="20" ' + ((months == 20) ? "selected" : "") + '>20</option><option value="21" ' + ((months == 21) ? "selected" : "") + '>21</option><option value="22" ' + ((months == 22) ? "selected" : "") + '>22</option>' +
                '<option value="23" ' + ((months == 23) ? "selected" : "") + '>23</option><option value="24" ' + ((months == 24) ? "selected" : "") + '>24</option><option value="25" ' + ((months == 25) ? "selected" : "") + '>25</option><option value="26" ' + ((months == 26) ? "selected" : "") + '>26</option><option value="27" ' + ((months == 27) ? "selected" : "") + '>27</option><option value="28" ' + ((months == 28) ? "selected" : "") + '>28</option>' +
                '<option value="29" ' + ((months == 29) ? "selected" : "") + '>29</option><option value="30" ' + ((months == 30) ? "selected" : "") + '>30</option><option value="31" ' + ((months == 31) ? "selected" : "") + '>31</option><option value="32" ' + ((months == 32) ? "selected" : "") + '>32</option><option value="33">33</option><option value="34" ' + ((months == 34) ? "selected" : "") + '>34</option>' +
                '<option value="35" ' + ((months == 35) ? "selected" : "") + '>35</option><option value="36" ' + ((months == 36) ? "selected" : "") + '>36</option></select></div>' +
                '<div class="col-md-2 custom-col"><input type="number" class="form-control" class="price" name="price[]" min="0" placeholder="0.00" value="' + price + '" required>' +
                '</div><div class="col-md-2 custom-col"><input type="number" class="form-control" class="discounted_price" name="discounted_price[]" min="0.00" value="' + discounted_price + '" placeholder="0.00"></div>' +
                ' <div class="col-md-1 custom-col"><button class="btn btn-icon btn-danger remove-tenure-item remove_tenure" name="remove_tenure"><i class="fas fa-trash"></i></button></div>' +
                '<input type="hidden" class="remove_tenure" name="tenure_id[]" id="tenure_id" placeholder="" value="">'
            '</div></div></div>';
            $('#tenures_div').append(html);
            $('#tenure').val('');
            $('#price').val('');
            $('#months').val('');
            $('#discounted_price').val('');
        }
    }

    $(document).on('click', '.remove_tenure', function (e) {
        e.preventDefault();
        if (!confirm("Are you sure want to delete?")) {
            return false;
        }
        e.stopPropagation();
        e.stopImmediatePropagation();
        var tenure_id = $(this).attr("data-tenure_id");
        $.ajax({
            type: "get",
            url: site_url + '/admin/packages/remove_tenure/' + tenure_id,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (result) {
                if (result.error == false) {
                    iziToast.success({
                        title: 'Success!',
                        message: result.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message,
                        position: 'topRight'
                    });
                }
            }
        });
        $(this).parent().parent().parent().remove();
    });
});

function delete_plan(element) {
    if (!confirm("Are you sure you want to delete this plan?")) {
        return false;
    }
    var plan_id = $(element).data("plan-id");
    let req_body = {
        [csrf_token]: csrf_hash,
        plan_id: plan_id,
    };
    $.ajax({
        url: base_url + "/admin/packages/delete_plan",
        type: "POST",
        data: req_body,
        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error) {
                showToastMessage(result.message, "error");
                3;
                return;
            } else {
                window.location.reload();
                showToastMessage(result.message, "success");
                3;
            }
        },
        error: function (error) {
            console.log(error);
        },
    });
}

// view packages js
$('.tenures').on('change', function () {
    var id = $(this).attr("data-package_id");
    var discount_value = $(this).find(":selected").attr("data-discount");
    var price = $(this).find(":selected").attr("data-price");
    var status;
    var icon;
    if (discount_value == '0') {
        status = "bg-danger";
        icon = " fa-times";
    } else {
        status = "bg-success";
        icon = " fa-check";
    };
    var myvar = '<div class="pricing-item  ">' +
        '<div class="pricing-item-icon ' + status + '"><i class="fa ' + icon + '"></i></div>' +
        '<div class="pricing-item-label">Discounted price' +
        '<span class="discount_price"> ' + discount_value + '</span>' +
        '</div>' +
        '</div>';

    $('#price' + id).empty(this);
    $('#price' + id).append(this.value);
    $("#discount_price" + id).children().last().remove()
    $('#discount_price' + id).append(myvar);
    if (discount_value == 0) {
        var price = $(this).find(":selected").attr("data-price");
        $('#price' + id).empty(price);
        $('#price' + id).append(price);
    } else {
        var discount = discount_value + ' <small class="discount-font">(<del>₹ ' + price + '</del>)</small>';
        $('#price' + id).empty(discount);
        $('#price' + id).append(discount);
    }

});
// subscription view JS......................

var start_date = "";
var end_date = "";
var subscription_type = "";
var date_filter_by = "";
$('#subscription_type').on('change', function () {
    subscription_type = $(this).find('option:selected').val();

});

$('#date_filter_by').on('change', function () {
    date_filter_by = $(this).find('option:selected').val();

});
$(function () {
    $('input[name="date_range"]').daterangepicker({
        opens: 'left'
    }, function (start, end, label) {
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');
    });
});
$('#date_range').on('change', function () { });

function subscriptions_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        subscription_type: $('#subscription_type').val(),
        date_filter_by: $('#date_filter_by').val(),
    };
}
function backup_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,

    };
}
$('#filter').on('click', function (e) {
    $('#subscription_table').bootstrapTable('refresh');
});


let today_date;
$(document).ready(function () {
    var date = (new Date()).toISOString().split('T')[0];
    $('#starts_from').val(date);
    $('#reset').on('click', function (e) {
        e.preventDefault();
    });
    $('#user_identity').on('change', function () {
        var id = $(this).find('option:selected').val();
        var user_name = $(this).find(":selected").attr("data-fullname");
        $('#user_name').val(user_name);
    });
    $('#package_name').on('change', function (e) {
        var id = $(this).find(":selected").attr("data-package_id");
        var no_of_businessesme = $(this).find(":selected").attr("data-businesses");
        var no_of_delivery_boys = $(this).find(":selected").attr("data-delivery_boys");
        var no_of_products = $(this).find(":selected").attr("data-products");
        var no_of_customers = $(this).find(":selected").attr("data-customers");
        var no_of_warehouse = $(this).find(":selected").attr("data-warehouse");
        $('#no_of_businesses').val(no_of_businessesme);
        $('#no_of_delivery_boys').val(no_of_delivery_boys);
        $('#no_of_products').val(no_of_products);
        $('#no_of_customers').val(no_of_customers);
        $('#no_of_warehouse').val(no_of_warehouse);
        $('#p_id').val(id);
        $.ajax({
            type: "get",
            url: site_url + '/admin/subscriptions/tenures/' + id,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (result) {
                if (result.error == false) {
                    var options = "<option value=''>Select Tenure</option>";
                    var price = 0;
                    result.data.map((tenure) => {
                        price = (parseFloat(tenure.discounted_price) < parseFloat(tenure.price) && parseFloat(tenure.discounted_price) > 0) ? tenure.discounted_price : tenure.price;
                        options += "<option data-tenure_name='" + tenure.tenure + "' data-tenure='" + tenure.months + "'  data-price='" + price + "' value=" + tenure.id + ">" + tenure.tenure + " </option>";
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: result.message,
                        position: 'topRight'
                    });
                    var options = " <option value=''>No Tenures Found</option> ";
                }
                $('#package_tenure').html(options);
                $('#price').val(price);

            }
        });

    });
    $('#package_tenure').on('change', function (e) {
        var price = $(this).find(":selected").attr("data-price");
        var months = $(this).find(":selected").attr("data-tenure");
        var tenure_name = $(this).find(":selected").attr("data-tenure_name");
        $('#price').val(price);
        $('#months').val(months);
        $('#tenure_name').val(tenure_name);
        var start_date = document.getElementById("starts_from");
        var end_date = document.getElementById("ends_from");
        end_date_handler(start_date, end_date);
    });
    $('#starts_from').on('change', function () {
        var start_date = document.getElementById("starts_from");
        var end_date = document.getElementById("ends_from");
        end_date_handler(start_date, end_date);

    });


    function end_date_handler(start_date, end_date) {
        var currentDate = moment(start_date.value);
        var futureMonth = moment(currentDate).add($("#months").val(), "M");
        var futureMonthEnd = moment(futureMonth).endOf("month");

        if (currentDate.date() != futureMonth.date() && futureMonth.isSame(futureMonthEnd.format("YYYY-MM-DD"))) {
            futureMonth = futureMonth.add(1, "d");
        }
        $("#ends_from").val(futureMonth.format("YYYY-MM-DD"));
    }


});
// create-vendor form 
$(document).ready(function () {
    $("#register_form").validate({
        rules: {
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            email: {
                required: true,
            },
            identity: {
                required: true,
            },
            password: {
                required: true,
            },
            password_confirm: {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "First name can not be empty."
            },
            last_name: {
                required: "Last name can not be empty."
            },
            email: {
                required: "Email can not be empty."
            },
            identity: {
                required: "Identity can not be empty."
            },
            password: {
                required: "Password can not be empty."
            },
            password_confirm: {
                required: "Password confirm can not be empty."
            }
        },
        errorClass: 'text-danger'
    });
});

$('#clear').on('click', function (e) {
    e.preventDefault();

    start_date = "";
    end_date = "";
    $('#title').val("");
    $('#support_email').val("");
    $('#logo').val("");
    $('#half_logo').val("");
    $('#favicon').val("");
    $('#currency_symbol').val("");
    $('#select_time_zone').val("");
    $('#phone').val("");
    $('#address').val("");
    $('#short_description').val("");
    $('#copyright_details').val("");
    $('#support_hours').val("");
    $('#payment_method').val("");
    $('input[name="date_range"]').val("Date Range Picker");
    $('#transaction_status').val("");
    $('#subscription_type').val("");
    $('#date_filter_by').val("");

    $(".table").bootstrapTable("refresh");
});

// units form
function select_parent_id() {
    var unit = $('#unit').val();
}
$(document).ready(function () {
    $('#unit').on('change', function () {
        select_parent_id();
    });

});

$('#admin_units_table').on('check.bs.table', function (e, row) {
    e.preventDefault();
    $('#name').val(row.name);
    $('#unit_id').val(row.id);
    $('#symbol').val(row.symbol);
    $('#parent_id').val(row.parent_id);
    $('#conversion').val(row.conversion);
});

let txn_start_date = "";
let txn_end_date = "";
let transaction_status = "";
let txn_provider = "";

function transaction_params(p) {
    return {
        user_id: userId,
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        txn_provider: $("#payment_method").val(),
        transaction_status: $("#transaction_status").val(),
    };
}
$("#payment_method").on("change", function () {
    txn_provider = $(this).val();
});
$("#transaction_status").on("change", function () {
    transaction_status = $(this).val();

});
$('#transaction_filter_btn').on('click', function (e) {
    $('#admin_transactions_table').bootstrapTable('refresh');
});

function refresh_table(id) {
    $('#' + id).bootstrapTable('refresh');
}

//  dashboard chart 
if ($("#myChart").length > 0) {
    var total_sale = [];
    var month_name;
    var data = [];

    $.ajax({
        type: "get",
        url: site_url + '/admin/home/fetch_sales',
        cache: false,
        dataType: 'json',
        success: function (result) {
            total_sale = result.total_sale
            month_name = result.month_name
            var data = {
                labels: month_name,
                datasets: [{
                    label: 'sale',
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 1,
                    data: total_sale,
                }]
            };

            var config = {
                type: 'bar',
                data: data,
                options: {}
            };
            var myChart = new Chart(
                document.getElementById('myChart'),
                config
            );

        }
    });



}

if ($("#pieChart").length > 0) {
    $.ajax({
        type: "get",
        url: site_url + '/admin/home/fetch_data',
        cache: false,
        dataType: 'json',
        success: function (result) {
            const data = {
                labels: [
                    'vendors',
                    'packages',
                    'No. of transactions'
                ],
                datasets: [{
                    label: 'sale',
                    data: [result.vendors, result.sold_packages, result.earnings],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    hoverOffset: 4
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
            };

            const myChart = new Chart(
                document.getElementById('pieChart'),
                config
            );
        }
    });

}

function set_locale(language_code) {
    $.ajax({
        url: base_url + "/admin/languages/change/" + language_code,
        type: "GET",
        success: function (result) {

        }
    }).then(() => {
        location.reload();
    });
}
//database backup
$("#backup_database").on("click", function (e) {
    Swal.fire({
        title: "Create Database Backup",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {

                var data = {
                    ['csrf_hash']: csrf_hash,
                    ['csrf_token']: csrf_token
                };

                $.ajax({
                    type: "POST",
                    url: site_url + "admin/database/backup_database",
                    data: data,
                    dataType: "json",
                    success: function (result) {
                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];
                        if (result.error == false) {

                            Swal.fire('Success', result.message, 'success');

                        } else {

                            Swal.fire('Error!', result.message, 'error');
                        }
                        $("#backup_table").bootstrapTable("refresh");
                    },
                })


            });
        },
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Backup is Created.', 'error');
        }
    }
    )
});



function delete_backup(e) {
    Swal.fire({
        title: "Delete Database Backup",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                var file_name = (e['id']);

                data = {
                    'file_name': file_name,
                    ['csrf_hash']: csrf_hash,
                    ['csrf_token']: csrf_token
                };

                $.ajax({
                    type: "POST",
                    url: site_url + "admin/database/delete",
                    data: data,
                    dataType: "json",
                    success: function (result) {
                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];
                        if (result.error == false) {

                            Swal.fire('Success', result.message, 'success');
                            $("#backup_table").bootstrapTable("refresh");
                        } else {

                            Swal.fire('Error!', result.message, 'error');
                        }
                    },
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    })
};


function mail_backup(e) {
    var file_name = (e['id']);

    data = {
        'file_name': file_name,
        ['csrf_hash']: csrf_hash,
        ['csrf_token']: csrf_token
    };
    $('#file_id').val(file_name);
}
$("#mail_DBbackup").on("hidden.bs.modal", function (e) {
    $("#email-set").empty();
    $("#message").empty();
});


function download_backup(e) {
    Swal.fire({
        title: "Download Database Backup",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                var file_name = (e['id']);

                data = {
                    'file_name': file_name,
                    ['csrf_hash']: csrf_hash,
                    ['csrf_token']: csrf_token
                };

                var uri = site_url + ('public/database_backup/' + file_name);
                var link = document.createElement("a");
                // If you don't know the name or want to use
                // the webserver default set name = ''
                link.setAttribute('download', '');
                link.href = uri;
                document.body.appendChild(link);
                link.click();
                link.remove();

                Swal.fire('Success', 'Backup File Download Successfully', 'success');
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'The file didn’t download', 'error');
        }
    });
}

$('#select_time_zone').on('change', function () {
    var mysql_timezone = $(this).find(':selected').data('gmt');
    $('#mysql_timezone').val(mysql_timezone);

})
$("#no_of_warehouse,#no_of_delivery_boys,#no_of_products,#no_of_customers").on("input", function (event) {
    if ((this).value < -1) {
        iziToast.error({
            title: "Error!",
            message: "only -1 is allowed in Negative numbers",
            position: "topRight",
        });

        (this).value = '';
    }
});

$(document).on('click', ".changeVendorStatus", function (e) {
    let vendorId = $(this).data('userid');
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, change the status!",

        preConfirm: function () {
            return new Promise((resolve, reject) => {
                let url = base_url + "admin/vendors/change-vendor-status";
                const formdata = new FormData();
                formdata.append('user_id', vendorId);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formdata,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        let result = response;
                        if (result.error == false) {
                            Swal.fire('Success', result.message, 'success');
                        } else {
                            Swal.fire('Error!', result.message, 'error');
                        }
                        $(".table").bootstrapTable("refresh");
                    }
                });
            });
        },

    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'The status could not be updated.', 'error');
        }
    });

});

function deleteUnit(id, route) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                let formData = new FormData();
                formData.append('id', id);

                $.ajax({
                    type: "post",
                    url: `${route}`,
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (result) {

                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];
                        if (result.error == true) {
                            Object.keys(result.message).forEach(function (key) {
                                Swal.fire('Error!', result.message[key], 'error');
                                return;
                            });
                            Swal.fire('Error!', result.message, 'error');
                        } else {
                            Object.keys(result.message).forEach(function (key) {
                                Swal.fire('Success', result.message[key], 'success');
                                return;
                            });
                        }
                        $("#admin_units_table").bootstrapTable("refresh");
                    },
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
}

$(document).on("click", ".editUnit", function (event) {

    var id = $(this).data('id');
    let formData = new FormData();
    formData.append(csrf_token, csrf_hash);
    formData.append('id', id);

    $.ajax({
        type: "post",
        url: base_url + 'admin/units/get-unit',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {

            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            if (result.error == true) {
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: "Error!",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
            } else {

                let data = result.data[0];

                $("#edit_name").val(data.name);
                $("#edit_unit_id").val(data.id);
                $("#edit_symbol").val(data.symbol);
                $("#edit_parent_id").val(data.parent_id);
                $("#edit_conversion").val(data.conversion);
            }
        },
    });
});
function deleteCategory(id, route) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                let formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    type: "post",
                    url: `${route}`,
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (result) {

                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];

                        if (result.error == true) {
                            Object.keys(result.message).forEach(function (key) {
                                Swal.fire('Error!', result.message[key], 'error');
                                return;
                            });
                            Swal.fire('Error!', result.message, 'error');
                        } else {
                            Object.keys(result.message).forEach(function (key) {
                                Swal.fire('Success', result.message[key], 'success');
                                return;
                            });
                        }
                        $("#admin_categories_table").bootstrapTable("refresh");
                    },
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
}

$(document).on("click", ".editCategory", function (event) {

    var id = $(this).data('id');
    let formData = new FormData();
    formData.append(csrf_token, csrf_hash);
    formData.append('id', id);

    $.ajax({
        type: "post",
        url: base_url + 'admin/categories/get-category',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {

            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            if (result.error == true) {
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: "Error!",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
            } else {

                let data = result.data[0];

                $("#edit_name").val(data.name);
                $("#edit_category_id").val(data.id);
                $("#edit_parent_id").val(data.parent_id);

                if (data.status == 1) {
                    $('#edit_status').attr("checked", true);
                } else {
                    $('#edit_status').attr("checked", false);
                }
            }
        },
    });
});


document.querySelectorAll('.togglePassword').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
        const input = this.previousElementSibling; // Find the input just before the button
        const icon = this.querySelector('i'); // Get the eye icon
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});

$(".form-submit-event").on("submit", function (e) {
    e.preventDefault();

    tinymce.triggerSave();

    //to get clicked button when there is multiple submit button in one page
    var clickedButton = e.originalEvent?.submitter;
    var currentUrl = window.location.href;
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    var submit_btn = $(clickedButton);
    var btn_html = $(clickedButton).html();
    var btn_val = $(clickedButton).val();
    var button_text = (btn_html != '' || btn_html != 'undefined') ? btn_html : btn_val;
    var check = false;
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            submit_btn.html('Please Wait..');
            submit_btn.attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            submit_btn.html(button_text);
            submit_btn.attr('disabled', false);

            if (result.error == true) {
                Object.keys(result.message).forEach((key) => {
                    showToastMessage(result["message"][key], "error");
                    return;
                });
            } else {

                if (currentUrl.includes("/profile")) {
                    if (result.data.password != undefined && result.data.password_confirm != undefined && result.data.password != "" && result.data.password_confirm != "") {
                        check = true;
                    }
                }

                $('.table').bootstrapTable('refresh');
                $('.form-submit-event')[0].reset();
                $(".modal").modal("hide");
                showToastMessage(result["message"], "success");

                setTimeout(() => {
                    if (check) {
                        window.location.href = base_url + "/auth/logout";
                    } else {
                        window.location.reload();
                    }
                }, 2000);
            }

        },
    });
});
