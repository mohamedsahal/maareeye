/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(function () {
    // Add manual close handler for media modal
    $(document).on('click', '#media-upload-modal .btn-close', function () {
        $('#media-upload-modal').modal('hide');
    });

    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);
    path = document.location.href;

    $(".sidebar-menu li a").each(function () {
        var href = $(this).attr("href");
        if (href === path) {
            $(".navbar li a").removeClass("active");
            $(this).closest("li").addClass("active");
            if ($(this).parents().hasClass("dropdown-menu")) {
                $(this).parents().addClass("active");
                $(this).parents().show();
            }
        }
    });
});

function resetForm(form) {
    // clearing inputs
    var inputs = form.getElementsByTagName("input");
    for (var i = 0; i < inputs.length; i++) {
        switch (inputs[i].type) {
            // case 'hidden':
            case "text":
                inputs[i].value = "";
                break;
            case "radio":
            case "checkbox":
                inputs[i].checked = false;
            case "email":
                inputs[i].value = "";
                break;
            case "number":
                inputs[i].value = "";
                break;
        }
    }

    // clearing selects
    var selects = form.getElementsByTagName("select");
    for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;

    // clearing textarea
    var text = form.getElementsByTagName("textarea");
    for (var i = 0; i < text.length; i++) text[i].innerHTML = "";

    return false;
}

//  businesses form/table
function edit_business(e) {
    $("#image_edit").html("");
    var business_id = $(e).data("business_id");

    $.ajax({

        url: site_url + "vendor/businesses/edit_business/" + business_id,
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                var img =
                    '<div class="img-fluid"><img class="icon-box" src="' +
                    base_url +
                    "/" +
                    result.business.icon +
                    '" alt=""></div>';
                $('input[name="name"]').val(result.business.name);
                $('input[name="business_id"]').val(result.business.id);
                $('input[name="old_icon"]').val(result.business.icon);
                $('textarea[name="description"]').val(result.business.description);
                $('textarea[name="address"]').val(result.business.address);
                $('input[name="contact"]').val(result.business.contact);
                $('input[name="tax_name"]').val(result.business.tax_name);
                $('input[type="hidden"][name="edit_business_input_image"]').val(result.business.icon);
                $('input[name="tax_value"]').val(result.business.tax_value);
                $('textarea[name="bank_details"]').val(result.business.bank_details);
                $('input[name="email"]').val(result.business.email);
                $('input[name="website"]').val(result.business.website);
                $('input[name="status"]').val(result.business.status);
                $("#image_edit").attr('src', base_url + "/" + result.business.icon);
                $(".image_edit_box").removeClass('d-none');
                $(document).scrollTop(0, 0, 500);
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
}

// vendor units form........................
function select_parent_id() {
    var unit = $("#unit").val();
}
$(document).ready(function () {
    $("#unit").on("change", function () {
        select_parent_id();
    });

});

//  Add product button check if business of vendor added first
$(document).ready(function () {
    $("#add_product_btn").on("click", function (e) {
        e.preventDefault();
        var business_id = $("#business_id").val();
        if (business_id == "0" || business_id == "") {
            if (!confirm("Please Add/Select your BUSINESS first!")) {
                return false;
            }
        } else {
            window.location = base_url + "/vendor/products/add_products";
        }
    });
});
// variants_modal
$(document).on("show.bs.modal", "#variants_Modal", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var id = $(current_selected_variant).data("id");

    var existing_url = $(this).find("#variants_table").data("url");
    if (existing_url.indexOf("?") > -1) {
        var temp = $(existing_url).text().split("?");
        var new_url = temp[0] + "?product_id=" + id;
    } else {
        var new_url = existing_url + "?product_id=" + id;
    }
    $("#variants_table").bootstrapTable("refreshOptions", {
        url: new_url,
    });
});
// Product form
var product_type;

function toggle_stock_management() {
    var stock_management = $("#stock_management").is(":checked");
    if (stock_management) {
        $("#stock_management_type_div").show();

        /* 1 - product_level | 2 - varaint_level  */
        var stock_management_type = $("#stock_management_type").val();
        if (stock_management_type == 1) {
            $(".stock_product_level").show();
            $(".stock_variant_level").hide();
        } else if (stock_management_type == 2) {
            $(".stock_product_level").hide();
            $(".stock_variant_level").show();
        } else {
            $(".stock_product_level").hide();
            $(".stock_variant_level").hide();
        }
    } else {
        $("#stock_management_type_div").hide();
        $(".stock_variant_level").hide();
        $(".stock_product_level").hide();
    }
}

function toggle_product_type() {
    var product_type = $("#product_type").val();
    if (product_type == "simple") {
        $(".add_btn_action").hide();
        $("#variant").empty();
    } else {
        $(".add_btn_action").show();
    }
}

$(document).ready(function () {
    toggle_stock_management();
    $(".add_btn_action").hide();

    $("#product_type").on("change", function () {
        toggle_product_type();
    });

    $("#stock_management_type").on("change", function () {
        toggle_stock_management();
    });

    $("#stock_management").on("change", function () {
        toggle_stock_management();
    });
    toggle_product_type();

    var i = 0;
    var j = 0;
    var k = 0;
    $("#add_variant").on("click", function (e) {
        e.preventDefault();
        var units = $("#units").val();
        if (units) {
            units = JSON.parse(units);
            var options = "<option value=''>Select Unit</option>";
            $.each(units, function (i, units) {
                options +=
                    '<option value = "' +
                    units["id"] +
                    '" > ' +
                    units["name"] +
                    "</option>";
            });

            var all_warehouses = $("#all_warehouses").val();
            if (all_warehouses) {
                all_warehouses = JSON.parse(all_warehouses);
                var warehouse_options = "<option value=''>Select Warehouse</option>";
                $.each(all_warehouses, function (i, warehouse) {
                    warehouse_options +=
                        '<option value = "' +
                        warehouse["id"] +
                        '" > ' +
                        warehouse["name"] +
                        "</option>";
                });

                var html = `
            <div class="variant-item py-1 mb-3 border-top border-2">
                <div class="d-flex justify-content-between my-1">
                    <div>
                        <p class="text-black font-weight-bolder">Variant ${$(".variant-item").length + 1}</p>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <button class="btn btn-icon btn-danger remove-variant-item remove_variant" 
                                    data-variant_id=""
                                    name="remove_variant"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Remove variant">
                                <i class="fas fa-trash"></i>
                                <span class="d-none d-md-inline">Remove variant</span>
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-primary addWarehouseBtn" 
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Add warehouse"
                                    data-variant_index="${$(".variant-item").length}"
                                    type="button">
                                <i class="fas fa-plus"></i>
                                <span class="d-none d-md-inline">Add warehouse</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2 custom-col">
                        <label>Variant Name<span class="asterisk text-danger"> *</span></label>
                        <input type="text" class="form-control" id="variant_name" name="variant_name[]" placeholder="Ex. 1 kg..">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label id=""> Variant Barcode </label>
                        <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]"  placeholder="Enter Barcode , Ex : 9875855">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label>Sale Price (₹)<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="sale_price" name="sale_price[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col">
                        <label>Purchase Price (₹)<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="purchase_price" name="purchase_price[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Unit<span class="asterisk text-danger"> *</span></label>
                        <select class="form-control" id="unit_id" name="unit_id[]">
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Stock<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="stock" name="stock[]" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-2 custom-col stock_variant_level">
                        <label>Minimum Stock<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="qty_alert" name="qty_alert[]" min="0.00" placeholder="0.00">
                    </div>
                </div>
                
                <div class="warehouses">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                            <select class="form-control" id="warehouse_id" name="warehouses[${$(".variant-item").length}][warehouse_ids][]">
                                ${warehouse_options}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_stock" name="warehouses[${$(".variant-item").length}][warehouse_stock][]">
                        </div>
                        <div class="col-md-3">
                            <label for="warehouse_qty_alert">Warehouse Minimum Stock Level</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_qty_alert" name="warehouses[${$(".variant-item").length}][warehouse_qty_alert][]">
                        </div>
                    </div>
                </div>
            </div>`;


                $("#variant").append(html);
                toggle_stock_management();
            }
        }
    });
    $(document).on("click", ".remove_variant", function (e) {
        e.preventDefault();
        $(this).parent().parent().parent().parent().remove();
    });
    // remove-variant
    $(document).on("click", ".remove_variant", function (e) {
        e.preventDefault();
        if (!confirm("Are you sure want to delete?")) {
            return false;
        }
        e.stopPropagation();
        e.stopImmediatePropagation();
        var variant_id = $(this).attr("data-variant_id");
        $.ajax({
            type: "get",
            url: site_url + "/vendor/products/remove_variant/" + variant_id,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (result) {
                if (result.error == false) {
                    iziToast.success({
                        title: "Success!",
                        message: result.message,
                        position: "topRight",
                    });
                } else {
                    iziToast.error({
                        title: "Error!",
                        message: result.message,
                        position: "topRight",
                    });
                }
            },
        });
        $(this).parent().parent().parent().remove();
    });

    $("#reset").on("click", function (e) {
        e.preventDefault();
    });
    $("#product_form").on("submit", function (e) {
        e.preventDefault();
        let isValid = true;

        var stock_management_type = $("#stock_management_type").val();

        if (stock_management_type == 1) {

            // Get all the stock input elements
            const stockInput = document.querySelector('input[name="simple_product_stock"]');
            let index = 0;

            const stockValue = parseFloat(stockInput.value) || 0;

            // Get corresponding warehouse stock elements for this stock

            const warehouseStocks = document.querySelectorAll('.warehouse_stock');
            // const warehouseStocks = document.querySelectorAll(`input[name="warehouses[${index}][warehouse_stock][]"]`);

            let totalWarehouseStock = 0;

            // Sum all warehouse stock values
            warehouseStocks.forEach(function (warehouseStockInput) {
                totalWarehouseStock += parseFloat(warehouseStockInput.value) || 0;
            });

            // Compare the total warehouse stock to the stock value
            if (totalWarehouseStock !== stockValue) {
                isValid = false;

                iziToast.error({
                    title: "Error! Mismatch Stock",
                    message: `Total of all warehouse stocks must be equal to variant stock (for variant ${index + 1})`,
                    position: "topRight",
                });
            }
            index++;

        } else if (stock_management_type == 2) {
            // Get all the stock input elements
            const stockInputs = document.querySelectorAll('input[name="stock[]"]');
            let index = 0;
            // Loop through each stock input
            stockInputs.forEach(function (stockInput) {
                const stockValue = parseFloat(stockInput.value) || 0;

                // Get corresponding warehouse stock elements for this stock
                const warehouseStocks = document.querySelectorAll(`input[name="warehouses[${index}][warehouse_stock][]"]`);
                // const warehouseStocks = document.querySelectorAll('.warehouse_stock');
                let totalWarehouseStock = 0;

                // Sum all warehouse stock values
                warehouseStocks.forEach(function (warehouseStockInput) {
                    totalWarehouseStock += parseFloat(warehouseStockInput.value) || 0;
                });

                // Compare the total warehouse stock to the stock value
                if (totalWarehouseStock !== stockValue) {
                    isValid = false;
                    // alert(`Total of all warehouse stocks must be equal to variant stock (for variant ${index + 1})`);
                    iziToast.error({
                        title: "Error! Mismatch Stock",
                        message: `Total of all warehouse stocks must be equal to variant stock (for variant ${index + 1})`,
                        position: "topRight",
                    });
                }
                index++;
            });
        }

        if (isValid) {

            var formData = new FormData(this);
            formData.append(csrf_token, csrf_hash);
            $.ajax({
                type: "post",
                url: this.action,
                data: formData,
                beforeSend: function () {
                    $('.submit_btn').html('Please Wait..');
                    $('.submit_btn').attr('disabled', true);
                },
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (result) {
                    csrf_token = result["csrf_token"];
                    csrf_hash = result["csrf_hash"];

                    $('.submit_btn').html('Save');
                    $('.submit_btn').attr('disabled', false);

                    if (result.error == true) {
                        var message = "";
                        Object.keys(result.message).map((key) => {
                            iziToast.error({
                                title: "Error!",
                                message: result.message[key],
                                position: "topRight",
                            });
                        });
                    } else {
                        window.location = base_url + "/vendor/products/add_products";
                        showToastMessage(result.message, "success");
                    }
                },
            });
        }
    });
});
//  variant table update status
function update_status(element) {
    if (!confirm("Are you sure want to update status?")) {
        return false;
    }
    var status;
    var id;
    if (!$(element).is(":checked")) {
        id = $(element).attr("data-id");
        status = "0";
    } else {
        var id = $(element).attr("data-id");
        status = "1";
    }
    $.ajax({
        type: "get",
        url:
            site_url +
            "/vendor/products/update_variant_status?id=" +
            id +
            "&status=" +
            status,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                $("table").bootstrapTable("refresh");
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
}
// tooltip
var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
// update default business
function update_default_business(element) {
    if (!confirm("Are you sure want to change default business?")) {
        return false;
    }
    var default_business;
    var id;
    if (!$(element).is(":checked")) {
        id = $(element).attr("data-id");
        default_business = "0";
    } else {
        var id = $(element).attr("data-id");
        default_business = "1";
    }
    $.ajax({
        type: "get",
        url:
            site_url +
            "/vendor/businesses/update_default_business? id=" +
            id +
            "&default_business=" +
            default_business,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                $("table").bootstrapTable("refresh");
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
}

function update_default_warehouse(element) {
    if (!confirm("Are you sure want to change default warehouse?")) {
        return false;
    }
    var default_warehouse;
    var id;
    if (!$(element).is(":checked")) {
        id = $(element).attr("data-id");
        default_warehouse = "0";
    } else {
        var id = $(element).attr("data-id");
        default_warehouse = "1";
    }
    $.ajax({
        type: "get",
        url:
            site_url +
            "/vendor/warehouse/update_default_warehouse? id=" +
            id +
            "&default_warehouse=" +
            default_warehouse,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                $("table").bootstrapTable("refresh");
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
}
$(document).ready(function () {
    $("#add_service_btn").on("click", function (e) {
        e.preventDefault();
        var business_id = $("#business_id").val();
        if (business_id == "0" || business_id == "") {
            if (!confirm("Please Add/Select your BUSINESS first!")) {
                return false;
            }
        } else {
            window.location = base_url + "/vendor/services/add_service";
        }
    });
});
// Service form submit
$(".recursive").hide();
if ($("input[name='is_recursive']").is(":checked")) {
    $(".recursive").show();
}
$("#is_recursive").on("click", function () {
    $(".recursive").hide();
    if ($("input[name='is_recursive']").is(":checked")) {
        $(".recursive").show();
    }
});

// orders-Pos system page

if ($("#products_div").length > 0) {
    $(fetch_products());
    $(display_cart());
}
$(".payment_method_name").hide();

function wordLimit(string, length = 50, dots = "...") {
    const words = string.split(" ");
    let newLength = 0;
    const resultWords = [];

    for (let i = 0; i < words.length; i++) {
        newLength += words[i].length;
        resultWords.push(words[i]);
        if (newLength >= length) {
            break;
        }
    }

    let newStr = resultWords.join(" ");
    if (newStr.length < string.length) {
        newStr += dots;
    }

    return newStr;
}

function display_products(products, currency) {
    var html = "";
    $.each(products, function (i, products) {
        var product_variants;
        $.each(products["variants"], function (j, variants) {
            // calculate here
            product_variants +=
                '<option value="' +
                variants.id +
                '" data-price="' +
                variants.sale_price +
                '" data-variant_name ="' +
                variants.variant_name +
                '">' +
                variants.variant_name +
                " -" +
                variants.sale_price +
                currency +
                "</option>";
        });
        html =
            '<div class="col-md-4">' +
            '<div class="owl-carousel owl-theme" id="products-carousel">' +
            '<div class="product-item pb-3">' +
            '<div class="item-image">' +
            '<img alt="image" src="' +
            products["image"] +
            '" class="order-image ">  ' +
            "</div>" +
            '<div class="product-details"><div class="product-name" title="' + products["name"] + '">' +
            wordLimit(products["name"], 25) +
            '</div><div class="d-flex justify-content-center">' +
            '<div class="col-md form-group"><label for="product_variant_id">Variant</label><span class="asterisk text-danger"> *</span>' +
            '<select class="form-control product_variants" name="product_variant_id"  id="product_variant_id">' +
            product_variants +
            '</select></div></div><button class="btn btn-xs btn-primary shop-item-button" id ="shop-item-button" data-business_id="' +
            products["business_id"] +
            '" data-tax_id= ' + products["tax_ids"] + ' data-is_tax_included="' +
            products["is_tax_included"] +
            '" data-product_id = "' +
            products["id"] +
            '" onclick="add_to_cart(event)" type="button">Add to Cart</button>' +
            '<input type="hidden" class="product_full_name" value="' + products["name"] + '">'
        "</div></div></div></div>";
        $("#products_div").append(html);
    });
}
// add to cart

function add_to_cart(e) {
    var cartRow = document.createElement("div");
    cartRow.classList.add("cart-row");
    var button = e.target;
    var product_item = button.parentElement.parentElement;
    var variant_dropdown =
        product_item.children[1].children[1].children[0].children
            .product_variant_id;
    var product_variant_id = variant_dropdown.value;
    var product_id = $(product_item.children[1].children[2]).data("product_id");
    var tax_id = $(product_item.children[1].children[2]).data("tax_id");

    var business_id = $(product_item.children[1].children[2]).data("business_id");
    var is_tax_included = $(product_item.children[1].children[2]).data(
        "is_tax_included"
    );
    var product_name =
        product_item.children[1].getElementsByClassName("product_full_name")[0].value

    // var product_name =
    //     product_item.getElementsByClassName("product-name")[0].innerText;
    var price = $(variant_dropdown.options[variant_dropdown.selectedIndex]).data(
        "price"
    );
    var variant_name = $(
        variant_dropdown.options[variant_dropdown.selectedIndex]
    ).data("variant_name");
    var image = product_item.getElementsByClassName("order-image")[0].src;
    var session_business_id = $("#business_id").val();
    var cart_item = {
        product_id: product_id,
        tax_id: tax_id,
        business_id: business_id,
        is_tax_included: is_tax_included,
        product_variant_id: product_variant_id,
        product_name: product_name,
        variant_name: variant_name,
        image: image,
        price: price,
        quantity: 1,
    };
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null
            ? JSON.parse(cart)
            : null;
    if (cart !== null && cart !== undefined) {
        if (cart.find((item) => item.product_variant_id === product_variant_id)) {
            var message = "This item is already present in your cart";
            show_message("Oops!", message, "error");
            return;
        }
        message = "Adding item to cart";
        button.innerText = "adding";
        setTimeout(function () {
            button.innerText = "Add to Cart";
        }, 600);
        cart.push(cart_item);
    } else {
        cart = [cart_item];
    }
    localStorage.setItem("cart" + business_id, JSON.stringify(cart));

    let last_order_id = $("#pos_quick_invoice").data('id');
    if (last_order_id != "") {
        $("#pos_quick_invoice").data('id', "")
        $("#pos_quick_invoice").addClass('d-none');
    }
    display_cart();
    final_total();
}

$(document).on("change", ".cart-quantity-input-new", function (e) {

    var variant_id = $(this).siblings().val();
    var quantity = $(this).val();
    var data = quantity;

    update_quantity(data, variant_id);
});

function display_cart() {
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null
            ? JSON.parse(cart)
            : null;
    var currency = $(".cart-value").attr("data-currency");
    var cartRowContents = "";
    if (cart !== null && cart.length > 0) {
        cart.forEach((item) => {

            cartRowContents += `
            <div class="container-order">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="cart-image">
                        <a href="${item.image}" data-lightbox="image-1" class="image-box-70">
                            <img class="mr-4" src="${item.image}">
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="cart-item-title " title="${item.product_name}">${wordLimit(item.product_name, 30) + ' (' + item.variant_name + ')'}</p>
                </div>

                <div class="col-md-3">
                    <div>
                        <div class="input-group-prepend input-group">
                            <input type="hidden" class="product-variant" name="variant_ids[]" type="number"
                                value=${item.product_variant_id}>
                            <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="minus"><i
                                    class="fas fa-minus"></i></button>
                            <input class="form-control cart-input  cart-quantity-input-new text-center p-0" name="quantity[]"
                                id="quantity${item.product_variant_id
                }" data-qty="${item.quantity}" value="${item.quantity
                }">
                            <button type="button" class="cart-quantity-input btn btn-sm btn-secondary" data-operation="plus"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <span class="cart-price fw-bolder">${currency + parseFloat(item.price).toLocaleString()
                }</span>
                        </div>
                        <div>
                            <button class="btn btn-sm remove-cart-item" data-business_id=${item.business_id}
                                data-variant_id=${item.product_variant_id}><i class="fa-trash-alt fas fs-6 text-danger"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        });
    } else {
        cartRowContents = `
            <div class="container">
                <div class="row">
                    <div class="col mt-4 d-flex justify-content-center text-primary h5">No items in cart</div>
                </div>
            </div>`;
    }
    $(".cart-items").html(cartRowContents);
    update_cart_total();
}

function cart_total() {

    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart = cart != null && cart != undefined ? JSON.parse(cart) : null;
    var total = 0;
    if (cart != null && cart != undefined) {
        cart.forEach((item) => {
            var quantity = item.quantity;
            var price = item.price;
            total += quantity * price;
        });
    }
    var currency = $("#cart-total-price").attr("data-currency");
    var total_amont = {
        currency: currency,
        total: total,
        cart_total_formated: parseFloat(total).toLocaleString(),
    };
    return total_amont;
}

function update_cart_total() {

    var total = cart_total();
    var final = final_total();
    var discount = $('#discount').val() != "" ? $('#discount').val() : 0;
    var shipping_charge = $('#delivery_charge').val() != "" ? $('#delivery_charge').val() : 0;
    $("#cart-total-price").html(total.currency + "" + total.cart_total_formated);
    $("#cart_discount").html(total.currency + "" + discount);
    $("#cart_shipping_charge").html(total.currency + "" + shipping_charge);
    $("#final_total").html(final.currency + "" + final.cart_total_formated);
    return;
}

$(".final_total").on("keyup", function () {
    final_total();
    update_cart_total();
});
$(".final_total").on("change", function () {
    final_total();
    update_cart_total();
});

function final_total() {
    var cart = cart_total();
    var sub_total = cart.total;
    var discount = $("#discount").val();
    var delivery_charges = $("#delivery_charge").val();
    var final_total = sub_total;
    if (discount != 0 && discount != null) {
        final_total = parseFloat(sub_total) - parseFloat(discount);
    }
    if (delivery_charges != 0 && delivery_charges != null) {
        final_total = parseFloat(final_total) + parseFloat(delivery_charges);
    }
    var currency = $("#final_total").attr("data-currency");
    var res = {
        currency: currency,
        total: final_total,
        cart_total_formated: parseFloat(final_total).toLocaleString(),
    };
    return res;
}

$(document).on("click", ".remove-cart-item", function (e) {
    e.preventDefault();
    var variant_id = $(this).data("variant_id");
    var business_id = $(this).data("business_id");
    $(this).parent().parent().remove();
    var session_business_id = $("#business_id").val();

    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null
            ? JSON.parse(cart)
            : null;
    if (cart) {
        var new_cart = cart.filter(function (item) {
            return item.product_variant_id != variant_id;
        });
        localStorage.setItem("cart" + business_id, JSON.stringify(new_cart));
        display_cart();
    }
});


function set_quantity(e) {
    var operation = $(e).data("operation");
    var variant_id = $(e).siblings().val();
    var input = $(e).parent()[0].children[2];
    var qty = parseInt($(input).data("qty"));
    if (operation == "plus") {
        qty = qty + 1;
        $(input).val(qty);
    } else {
        qty = qty - 1;
        $(input).val(qty);
    }
    update_quantity(qty, variant_id);
}

function update_quantity(qty, product_variant_id) {
    if (isNaN(qty) || qty <= 0) {
        qty = 1;
    }
    var session_business_id = $("#business_id").val();
    var cart = localStorage.getItem("cart" + session_business_id);
    cart =
        localStorage.getItem("cart" + session_business_id) !== null
            ? JSON.parse(cart)
            : null;
    if (cart) {
        var i = cart.map((i) => i.product_variant_id).indexOf(product_variant_id);
        cart[i].quantity = qty;
        var business_id = cart[i].business_id;
        localStorage.setItem("cart" + business_id, JSON.stringify(cart));
        display_cart();
    }
}
$(document).on("click", ".cart-quantity-input", function (e) {
    set_quantity(this);
});

$(document).on("click", ".btn-clear_cart", function (e) {
    e.preventDefault();
    delete_cart_items();
});

function delete_cart_items() {
    var session_business_id = $("#business_id").val();
    localStorage.removeItem("cart" + session_business_id);
    display_cart();
}

$(document).on("change", "#product_warehouse", function (e) {
    e.preventDefault();
    delete_cart_items();
});


function fetch_products() {

    var category_id = $("#product_category").find("option:selected").val();
    var brand_id = $("#product_brand").find("option:selected").val();
    var warehouse_id = $("#product_warehouse").find("option:selected").val();
    var limit = $("input[name=limit]").val();
    var offset = $("input[name=offset]").val();
    var search = $("#search_product").val();
    var flag = null;

    $.ajax({
        type: "GET",
        url: site_url + "/vendor/products/get_products",
        cache: false,
        data: {
            category_id: category_id,
            brand_id: brand_id,
            warehouse_id: warehouse_id,
            search: search,
            limit: limit,
            offset: offset,
        },
        beforeSend: function () {
            $("#products_div").html(
                `<div class="text-center" style='min-height:450px;' ><h4>Please wait.. . loading products..</h4></div>`
            );
        },
        dataType: "json",
        success: function (result) {
            if (result.error == true) {

                $("#products_div").html(
                    `<div class="text-center" style='min-height:450px;' ><h4>No products found..</h4></div>`
                );
            } else {
                var products = result.data;
                if (products) {
                    var html = "";
                    $("#total_products").val(result.total);
                    $("#products_div").empty(html);
                    var currency = result.currency;
                    display_products(products, currency);
                    var total = $("#total_products").val();
                    var current_page = $("#current_page").val();
                    var limit = $("#limit").val();
                    paginate(total, current_page, limit);
                }
            }
        },
    });
}

// paginantion
function paginate(total, current_page, limit) {
    var number_of_pages = total / limit;
    var i = 0;
    var pagination = `<div class="row p-2">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            <ul class="pagination mb-0">`;
    pagination += `<li class="page-item disabled"><a class="page-link" href="javascript:prev_page()" tabindex="-1" ><i class="fas fa-chevron-left"></i></a></li>`;
    var active = "";
    while (i < number_of_pages) {
        active = current_page == i ? "active" : "";
        pagination += `<li class="page-item ${active}"><a class="page-link" href="javascript:go_to_page(${limit},${i})">${++i}<span class="sr-only">(current)</span></a></li>`;
    }
    pagination += `<li class="page-item"><a class="page-link" href="javascript:next_page()"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            </div>
        </div>
    </div>`;

    $(".product_pagination").html(pagination);
}

function go_to_page(limit, page_number) {
    var total = $("#total_products").val();
    var category_id = $("#product_category").find("option:selected").val();
    var offset = page_number * limit;
    paginate(total, page_number, limit);

    $("#limit").val(limit);
    $("#offset").val(offset);
    $("#current_page").val(page_number);
    fetch_products(category_id, limit, offset);
}

function prev_page() {
    var current_page = $("#current_page").val();
    var limit = $("#limit").val();
    var prev_page = parseFloat(current_page) - 1;

    if (prev_page >= 0) {
        go_to_page(limit, prev_page);
    }
}

function next_page() {
    var current_page = $("#current_page").val();
    var total = $("#total_products").val();
    var limit = $("#limit").val();

    var number_of_pages = total / limit;
    var next_page = parseFloat(current_page) + 1;

    if (next_page < number_of_pages) {
        go_to_page(limit, next_page);
    }
}

$("#product_categories").on("change", function () {
    var category_id = $("#product_categories").val();
    var limit = $("#limit").val();
    $("#current_page").val("0");
    fetch_products(category_id, limit, 0);
});

$("#clear_user_search").on("click", function () {
    $(".select_user").empty();
});

var customer_id = 0;
$(".select_user").on("change", function () {
    customer_id = $(this).val();
});

$(".payment_status").on("change", function () {
    var status = $(this).find("option:selected").val();
    if (status != "partially_paid") {
        $(".amount_paid").hide();
    } else {
        $(".amount_paid").show();
        $(".amount_paid").removeClass("d-none");
    }
});
$(".payment_method").on("click", function () {
    var payment_method = $(this).val();
    if (payment_method == "wallet") {
        $(".amount_paid").hide();
        $(".payment_status").hide();
        $(".payment_status_label").hide();
    } else {
        $(".payment_status_label").show();
        $(".payment_status").show();
        $(".payment_status").trigger("change");
    }
});

// place order form
function show_message(prefix = "Great!", message, type = "success") {
    Swal.fire(prefix, message, type);
}

$(document).on("ready", function () {
    $(".transaction_id").hide();
    $(".payment_method_name").hide();
});

/* payment method selected event  */
$(".payment_method").on("click", function () {
    var payment_method = $(this).val();
    var exclude_txn_id = ["cash"];
    var include_payment_method_name = ["other"];

    if (exclude_txn_id.includes(payment_method)) {
        $(".transaction_id").hide();
    } else {
        $(".transaction_id").show();
    }

    if (include_payment_method_name.includes(payment_method)) {
        $(".payment_method_name").show();
    } else {
        $(".payment_method_name").hide();
    }
});

$("#place_order_form").on("submit", function (e) {
    e.preventDefault();
    if (confirm("Are you sure? you want to check out.")) {
        var session_business_id = $("#business_id").val();

        var cart = localStorage.getItem("cart" + session_business_id);
        if (cart == null || !cart) {
            var message = "Please add items to cart";
            show_message("Oops!", message, "error");
            return;
        }

        var cartTotal = cart_total();
        var total = cartTotal["total"];
        var discount = $("#discount").val();
        var status = $("#status").val();
        var delivery_charges = $("#delivery_charge").val();
        var order_type = $("#order_type").val();
        var message = $("#message").val();
        var finalTotal = final_total();
        var final = finalTotal["total"];
        var payment_status = $("#payment_status_item").find(":selected").val();
        var amount_paid = $("#amount_paid_item").val();
        var payment_method = $(".payment_method:checked").val();
        var transaction_id = $("#transaction_id").val();
        var warehouse_id = $("#product_warehouse").find("option:selected").val();

        if (payment_status != "unpaid" && payment_status != "cancelled") {

            if (!payment_method) {
                var message = "Please choose a payment method";
                show_message("Oops!", message, "error");
                return;
            }
        }
        var payment_method_name = $("#payment_method_name").val();
        if (!payment_method_name) {
            payment_method_name = "";
        }
        const request_body = {
            [csrf_token]: csrf_hash,
            data: cart,
            payment_method: payment_method,
            customer_id: customer_id,
            payment_method_name: payment_method_name,
            total: total,
            discount: discount,
            delivery_charges: delivery_charges,
            final_total: final,
            status: status,
            payment_status: payment_status,
            amount_paid: amount_paid,
            transaction_id: transaction_id,
            order_type: order_type,
            message: message,
            warehouse_id: warehouse_id,
        };
        $.ajax({
            type: "post",
            url: this.action,
            data: request_body,
            dataType: "json",
            success: function (result) {
                let order_id = result.data.order_id;

                csrf_token = result["csrf_token"];
                csrf_hash = result["csrf_hash"];

                var messages = [
                    "Please add order item",
                    "Amount is more than order total please check!",
                    "You dont have sufficient wallet balance,Please recharge wallet!",
                    "Please select the customer!"
                ];
                if (result.error === true) {
                    if (messages.includes(result.message)) {
                        iziToast.error({
                            title: "Error!",
                            message: result.message,
                            position: "topRight",
                        });
                    } else if (typeof result.message === "object") {
                        Object.values(result.message).forEach((msg) => {
                            iziToast.error({
                                title: "Error!",
                                message: msg,
                                position: "topRight",
                            });
                        });
                    }
                } else {
                    iziToast.success({
                        title: "Success!",
                        message: result.message,
                        position: "topRight",
                    });

                    delete_cart_items();
                    $("#pos_quick_invoice").data("id", order_id);
                    $("#pos_quick_invoice").removeClass("d-none");

                    // Uncomment if needed:
                    // setTimeout(() => location.reload(), 600);
                }


            },
        });
    }
});

// set delivery boy for order item
function set_delivery_boy(e) {
    var deliveryboy = $(e).find("option:selected").val();
    var order_id = $(e).find(":selected").attr("data-order_id");
    var type = $(e).find(":selected").attr("data-type");

    $.ajax({
        type: "get",
        url: site_url + "/vendor/orders/set_delivery_boy/",
        data: {
            deliveryboy: deliveryboy,
            order_id: order_id,
            type: type,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
}
$(".delivery_boy").on("change", function () {
    set_delivery_boy(this);
});
// order-details update status of ordered item

function update_order_status(e) {
    var status = $(e).find("option:selected").val();
    var order_id = $(e).find(":selected").attr("data-order_id");
    var type = $(e).find(":selected").attr("data-type");

    $.ajax({
        type: "get",
        url: site_url + "/vendor/orders/update_order_status/",
        data: {
            status: status,
            order_id: order_id,
            type: type,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
}
$(".status_update").on("change", function () {
    update_order_status(this);
});
// bulk update status

function show_message(prefix = "Great!", message, type = "success") {
    Swal.fire(prefix, message, type);
}
// bulk status update of order items
var item_id = [];

function update_bulk_status(item_id) {
    var bulk_status;
    var type;
    var order_id;
    bulk_status = $(".status_bulk").find("option:selected").val();
    order_id = $(".status_bulk").find("option:selected").attr("data-order_id");
    type = $(".status_bulk").attr("data-type");
    if (bulk_status == "" || bulk_status == 0) {
        var message = "Please select status for bulk update!";
        show_message("Oops!", message, "error");
        return;
    }
    if (item_id == "" || item_id == undefined) {
        var message = "Please check item for bulk update!";
        show_message("Oops!", message, "error");
        return;
    }
    var response = [item_id, bulk_status, type, order_id];
    return response;
}
$(".update_status_bulk ").on("click", function (e) {
    e.preventDefault();
    var item = update_bulk_status(item_id);
    if (!item) {
        return;
    } else {
        var item_ids = item[0];
        var status = item[1];
        var type = item[2];
        const request_body = {
            [csrf_token]: csrf_hash,
            item_ids: item_ids,
            status: status,
            type: type,
        };
        $.ajax({
            type: "post",
            url: base_url + "/delivery_boy/orders/update_status_bulk",
            data: request_body,
            cache: false,
            dataType: "json",
            success: function (result) {
                csrf_token = result["csrf_token"];
                csrf_hash = result["csrf_hash"];
                if (result.error == true) {
                    showToastMessage(result.message, result.type);
                } else {
                    showToastMessage(result.message, result.type);
                    location.reload();
                }
            },
        });
    }
});

$(function () {
    $(".status_order_bulk").on("click", function () {
        if (this.checked) {
            var checked = $(".status_order").prop("checked", this.checked);
            $.each(checked, function (i, checked) {
                var id = checked.value;
                item_id.push(id);
            });
        } else {
            var checked = $(".status_order").prop("checked", false);
            item_id = [];
        }
    });
    $(".status_order").on("click", function () {
        if (this.checked) {
            var id = $(this).val();
            item_id.push(id);
            $(".status_order_bulk").prop("checked", false);
        } else {
            var id = $(this).val();
            item_id.pop(id);
        }
    });
});

// create payment modal event
$(document).on("show.bs.modal", "#create_payment", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var order_id = $(current_selected_variant).data("order_id");
    var customer_id = $(current_selected_variant).data("customer_id");
    var supplier_id = $(current_selected_variant).data("supplier_id");
    $('input[name="order_id"]').val(order_id);
    $('input[name="customer_id"]').val(customer_id);
    $('input[name="supplier_id"]').val(supplier_id);
    $('input[name="order_type"]').val();
});

$(".transaction_id").hide();
$("#payment_mode").on("change", function () {
    var type = $(this).find("option:selected").val();
    if (type == "other") {
        var html =
            ' <label for="payment_method_name">Enter Payment Method Name</label><span class="asterisk text-danger"> *</span>' +
            '<input type="text" class="form-control" id="payment_method_name" name="payment_method_name" placeholder="">';
        $("#type").append(html);
        $(".transaction_id").show();
    } else if (type == "cash") {
        $(".transaction_id").hide();
    } else {
        $("#type").html("");
        $(".transaction_id").show();
    }
});

$("#product_wallet").on("change", function () {
    var user_id = $(this).val();
    $.ajax({
        type: "get",
        url: site_url + "/vendor/orders/customer_balance",
        data: {
            user_id: user_id,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                var balance = result.balance;
                $("#wallet_balance").html("");
                $("#wallet_balance").append("wallet balance:" + balance);
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
});
// subscription packages js

$(".free_package").on("click", function () {
    var user_id = $(this).data("user_id");
    var package_id = $(this).data("package_id");
    var tenure = $(this).data("tenure");
    var months = $(this).data("months");
    var price = $(this).data("price");
    var transaction_id = "0";
    const request_body = {
        [csrf_token]: csrf_hash,
        user_id: user_id,
        txn_id: transaction_id,
        package_id: package_id,
        months: months,
        tenure: tenure,
        price: price,
    };
    $.ajax({
        type: "post",
        url: site_url + "/vendor/subscription/free_subscription",
        data: request_body,
        dataType: "json",
        success: function (result) {
            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];

            if (result.error == true) {
                location.href = base_url + "/vendor/payments/payment_failed";
                showToastMessage(result.message, "error");
            } else {
                location.href = base_url + "/vendor/payments/payment_success";
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
});

$(get_tenure_id);
var tenure_id;

function get_tenure_id() {
    tenure_id = $(this).find(":selected").attr("data-tenure_id");
}
$(".tenures").on("change", function () {
    var id = $(this).attr("data-package_id");
    tenure_id = $(this).find(":selected").attr("data-tenure_id");
    var discount_value = $(this).find(":selected").attr("data-discount");
    var price = $(this).find(":selected").attr("data-price");
    var tenure_name = $(this).find(":selected").text();

    var status;
    var icon;
    if (discount_value == "0") {
        status = "bg-danger";
        icon = " fa-times";
    } else {
        status = "bg-success";
        icon = " fa-check";
    }
    var myvar =
        '<div class="pricing-item  ">' +
        '<div class="pricing-item-icon ' +
        status +
        '"><i class="fa ' +
        icon +
        '"></i></div>' +
        '<div class="pricing-item-label">Discounted price' +
        '<span class="discount_price"> ' +
        discount_value +
        "</span>" +
        "</div>" +
        "</div>";
    $("#price" + id).empty(this);
    $("#price" + id).append(this.value);
    $("#discount_price" + id)
        .children()
        .last()
        .remove();
    $("#discount_price" + id).append(myvar);
    if (discount_value == 0) {
        var price = $(this).find(":selected").attr("data-price");
        $("#price" + id).empty(price);
        $("#price" + id).append(price);
    } else {
        var discount =
            discount_value +
            ' <small class="discount-font">(<del>₹ ' +
            price +
            "</del>)</small>";
        $("#price" + id).empty(discount);
        $("#price" + id).append(discount);
    }
});
$(document).on("show.bs.modal", "#customer_status", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var customer_id = $(current_selected_variant).data("id");
    $('input[name="customer_id"]').val(customer_id);
});

$(document).on("show.bs.modal", "#customers_services", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var customer_id = $(current_selected_variant).data("customer_id");
    var existing_url = $(this).find("#customers_services_table").data("url");
    if (existing_url.indexOf("?") > -1) {
        var temp = $(existing_url).text().split("?");
        var new_url = temp[0] + "?customer_id=" + customer_id;
    } else {
        var new_url = existing_url + "?customer_id=" + customer_id;
    }
    $("#customers_services_table").bootstrapTable("refreshOptions", {
        url: new_url,
    });
});


$(document).on("show.bs.modal", "#deliveryboy_register", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var deliveryboy_id = $(current_selected_variant).data("id");
    var name = $(current_selected_variant).data("name");
    var identity = $(current_selected_variant).data("identity");
    var email = $(current_selected_variant).data("email");
    if (deliveryboy_id == undefined) {
        return;
    }
    $.ajax({
        type: "get",
        url: site_url + "/vendor/delivery_boys/count",
        data: {
            id: deliveryboy_id,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                $('input[name="business_id[]"]').val(result.business_id);
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
    $('input[name="first_name"]').val(name);
    $('input[name="id"]').val(deliveryboy_id);
    $('input[name="identity"]').val(identity);
    $('input[name="email"]').val(email);
});

// $("#customers_table").on("check.bs.table", function (e, row) {
$("#customers_table").on("check.bs.table", function (e, row) {
    e.preventDefault();
    console.log(row);
    var name = row.name != undefined ? row.name : row.customer_name

    $("#name").val(name);
    $("#identity").val(row.mobile);

    $("#address").val(row.address);
    $("#email").val(row.email);
    $("#user_id").val(row.id);
    $("#customer_id").val(row.id);

    if (row.active == 1) {
        $("#status").attr("checked", true);
    } else {
        $("#status").attr("checked", false);
    }
});
// filter transactions
var type = "";
$("#transaction_type").on("change", function () {
    type = $(this).find("option:selected").val();
});

function t_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        type: type,
    };
}
function queryParams(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
    };
}
$("#transaction_filter").on("click", function (e) {
    $("#transactions_table").bootstrapTable("refresh");
});

// filter orders list
var start_date = "";
var end_date = "";
var payment_status_filter = "";
var order_type_filter = "";
$("#payment_status_filter").on("change", function () {
    payment_status_filter = $(this).find("option:selected").val();
});

$("#order_type_filter").on("change", function () {
    order_type_filter = $(this).find("option:selected").val();
});
$(function () {
    $('input[name="date_range"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});
$("#date_range").on("change", function () { });

function orders_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        payment_status_filter: payment_status_filter,
        order_type_filter: order_type_filter,
    };
}

$("#filter").on("click", function (e) {
    $("#orders_items_table").bootstrapTable("refresh");
});

$("#delivery_boys_table").on("check.bs.table", function (e, row) {
    e.preventDefault();

    $("input[name='business_id[]']:checkbox").attr("checked", false);
    $("input[name='status']:checkbox").attr("checked", true);
    $("#name").val(row.name);
    $("#identity").val(row.mobile);
    $("#email").val(row.email);
    $("#delivery_boy_id").val(row.id);

    if (row.permissions.customer_permission == "1") {
        $("#customer_permission").attr("checked", true);
    } else {
        $("#customer_permission").attr("checked", false);
    }
    if (row.permissions.transaction_permission == "1") {
        $("#transaction_permission").attr("checked", true);
    } else {
        $("#transaction_permission").attr("checked", false);
    }
    if (row.permissions.orders_permission == "1") {
        $("#orders_permission").attr("checked", true);
    } else {
        $("#orders_permission").attr("checked", false);
    }

    var assigned_b_id = row.assigned_b_id;
    if (assigned_b_id.length > 1) {
        var b_id = assigned_b_id.split(",");
        $.each(b_id, function (i, b_id) {
            if ($("#" + b_id.trim()).val() == b_id) {
                $("#" + b_id.trim()).attr("checked", true);
            }
        });
    } else {
        if ($("#" + assigned_b_id).val() == assigned_b_id) {
            $("#" + assigned_b_id).attr("checked", true);
        }
    }

    if (row.active == 1) {
        $("#status").attr("checked", true);
    } else {
        $("#status").attr("checked", false);
    }
});

/* Search AJAX Users in POS */
$(document).ready(function () {
    $(".select_user").select2({
        ajax: {
            url: site_url + "vendor/orders/get_users",
            dataType: "json",
            data: function (params) {
                var query = {
                    search: params.term,
                };
                return query;
            },
            processResults: function (response) {
                return {
                    results: response.data,
                };
            },
            cache: true,
        },
        placeholder: "Search for a User",

        templateResult: formatPost,
        templateSelection: formatPostSelection,
    });
});

function formatPost(post) {
    if (post.loading) {
        return post.text;
    }

    var $container = $(
        "<div class='select2-result-postsitory clearfix'>" +
        "<div class='select2-result-postsitory__meta'>" +
        "<strong>" +
        post.text +
        "</strong><span> | </span>" +
        "<strong>" +
        post.number +
        "</strong><span> | </span>" +
        "<strong>" +
        post.email +
        "</strong>" +
        "</div>" +
        "</div>" +
        "</div>"
    );

    return $container;
}

function formatPostSelection(post) {
    return post.text;
}
let userId = $("#user_id").val();

// subscription transaction filter
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
        start_date: txn_start_date,
        end_date: txn_end_date,
        txn_provider: txn_provider,
        transaction_status: transaction_status,
    };
}
$("#payment_method").on("change", function () {
    txn_provider = $(this).val();
});
$("#transaction_status").on("change", function () {
    transaction_status = $(this).val();
});
$("#transaction_filter_btn").on("click", function (e) {
    $("#vendors_transactions_table").bootstrapTable("refresh");
});

function refresh_table(id) {
    $("#" + id).bootstrapTable("refresh");
}

// recursive_services table for subscription
$(document).on("show.bs.modal", "#recursive_services", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var id = $(current_selected_variant).data("service_id");

    var existing_url = $(this)
        .find("#customers_list_of_services_table")
        .data("url");
    if (existing_url.indexOf("?") > -1) {
        var temp = $(existing_url).text().split("?");
        var new_url = temp[0] + "?service_id=" + id;
    } else {
        var new_url = existing_url + "?service_id=" + id;
    }
    $("#customers_list_of_services_table").bootstrapTable("refreshOptions", {
        url: new_url,
    });
});

function remove_subscription(e) {
    if (!confirm("Are you sure want to delete?")) {
        return false;
    }
    var subscription_id = $(e).attr("data-sub_id");
    $.ajax({
        type: "get",
        url:
            site_url +
            "/vendor/customers_subscription/remove_subscription/" +
            subscription_id,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
                $("#customers_services").bootstrapTable("refresh");
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
            }
        },
    });
}

//  dashboard chart
if ($("#myChart").length > 0) {
    var total_sale = [];
    var month_name;
    var data = [];

    $.ajax({
        type: "get",
        url: site_url + "/vendor/home/fetch_sales",
        cache: false,
        dataType: "json",
        success: function (result) {
            total_sale = result.total_sale;
            month_name = result.month_name;
            var data = {
                labels: month_name,
                datasets: [
                    {
                        label: "sale",
                        backgroundColor: [
                            "rgba(255, 99, 132, 0.2)",
                            "rgba(255, 159, 64, 0.2)",
                            "rgba(255, 205, 86, 0.2)",
                            "rgba(75, 192, 192, 0.2)",
                            "rgba(54, 162, 235, 0.2)",
                            "rgba(153, 102, 255, 0.2)",
                            "rgba(201, 203, 207, 0.2)",
                        ],
                        borderColor: [
                            "rgb(255, 99, 132)",
                            "rgb(255, 159, 64)",
                            "rgb(255, 205, 86)",
                            "rgb(75, 192, 192)",
                            "rgb(54, 162, 235)",
                            "rgb(153, 102, 255)",
                            "rgb(201, 203, 207)",
                        ],
                        borderWidth: 1,
                        data: total_sale,
                    },
                ],
            };

            var config = {
                type: "bar",
                data: data,
                options: {},
            };
            var myChart = new Chart(document.getElementById("myChart"), config);
        },
    });
}
if ($("#sales-per-warehouse-chart").length > 0) {

    $.ajax({
        type: "get",
        url: site_url + "/vendor/home/fetch_warehouse_sales",
        cache: false,
        dataType: "json",
        success: function (result) {
            var datasets = [];
            var labels = []; // Month labels
            var backgroundColors = [
                "rgba(255, 99, 132, 0.2)",
                "rgba(255, 159, 64, 0.2)",
                "rgba(255, 205, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(201, 203, 207, 0.2)"
            ];
            var borderColors = [
                "rgb(255, 99, 132)",
                "rgb(255, 159, 64)",
                "rgb(255, 205, 86)",
                "rgb(75, 192, 192)",
                "rgb(54, 162, 235)",
                "rgb(153, 102, 255)",
                "rgb(201, 203, 207)"
            ];

            // Iterate through each warehouse in the result
            var colorIndex = 0;
            $.each(result, function (warehouse_id, warehouse_data) {
                if (labels.length === 0) {
                    // Set the month labels only once, as all warehouses will share the same months
                    labels = warehouse_data.month_name;
                }

                datasets.push({
                    label: warehouse_data.warehouse_name + " Sales",
                    backgroundColor: backgroundColors[colorIndex % backgroundColors.length],
                    borderColor: borderColors[colorIndex % borderColors.length],
                    borderWidth: 1,
                    data: warehouse_data.total_sales,
                });

                colorIndex++;
            });

            // Create chart data
            var chartData = {
                labels: labels,
                datasets: datasets,
            };

            // Create chart config
            var config = {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    }
                },
            };

            // Render the chart
            var sales_per_warehouse_chart = new Chart(
                document.getElementById("sales-per-warehouse-chart"),
                config
            );
        },
    });
}

// doughnut chart
if ($("#pieChart").length > 0) {
    $.ajax({
        type: "get",
        url: site_url + "/vendor/home/fetch_data",
        cache: false,
        dataType: "json",
        success: function (result) {
            const data = {
                labels: ["sale", "orders", "customers"],
                datasets: [
                    {
                        label: "sale",
                        data: [result.sales, result.orders, result.customer],
                        backgroundColor: [
                            "rgb(255, 99, 132)",
                            "rgb(54, 162, 235)",
                            "rgb(255, 205, 86)",
                        ],
                        hoverOffset: 4,
                    },
                ],
            };

            const config = {
                type: "doughnut",
                data: data,
            };
            const myChart = new Chart(document.getElementById("pieChart"), config);
        },
    });
}

function set_locale(language_code) {
    $.ajax({
        url: base_url + "/admin/languages/change/" + language_code,
        type: "GET",
        success: function (result) { },
    }).then(() => {
        location.reload();
    });
}

$("#send_invoice").on("click", function () {
    var email = $(this).attr("data-email");
    var order_id = $(this).attr("data-order_id");
    const request = {
        [csrf_token]: csrf_hash,
        email_id: email,
        order_id: order_id,
    };
    $.ajax({
        type: "post",
        url: site_url + "/vendor/invoices/send",
        data: request,
        dataType: "json",
        success: function (result) {
            csrf_token = result["csrf_token"];
            csrf_hash = result["csrf_hash"];
            if (result.error == false) {
                showToastMessage(result.message, "success");
            } else {
                showToastMessage(result.message, "error");
            }
        },
    });
});
let stock;
$(".stock_btn").on("click", function (e) {
    e.preventDefault();
    stock = $(this).attr("data-flag");
    window.location.href = base_url + "/vendor/products/stock/" + stock;
});

function stock_params(params) {
    return {
        stock: params.stock,
        search: params.search,
        limit: params.limit,
        sort: params.sort,
        order: params.order,
        offset: params.offset,
    };
}

// search suppliers using ajax
$(document).ready(function () {
    $(".select_supplier").select2({
        ajax: {
            url: site_url + "vendor/purchases/get_suppliers",
            dataType: "json",
            data: function (params) {
                var query = {
                    search: params.term,
                };
                return query;
            },
            processResults: function (response) {
                return {
                    results: response.data,
                };
            },
            cache: true,
        },
        placeholder: "Search for a Supplier",
        templateResult: formatPostSuppliers,
        templateSelection: SuppliersSelection,

    });
});

function formatPostSuppliers(p) {
    if (p.loading) {
        return p.text;
    }
    var $supplier = $(
        "<div class='select2-result-postsitory clearfix'>" +
        "<div class='select2-result-postsitory__meta'>" +
        "<strong>" +
        p.text +
        "</strong><span> | </span>" +
        "<strong>" +
        p.balance +
        "</strong>" +
        "</div>" +
        "</div>"
    );
    return $supplier;
}

function SuppliersSelection(p) {
    return p.text;
}

// search products
$(document).ready(function () {

    $(".search_products").select2({

        ajax: {
            url: site_url + "vendor/products/get_products",
            dataType: "json",
            data: function (params) {

                var query = {
                    search: params.term,

                };
                return query;
            },
            processResults: function (response) {
                return {
                    results: response.variants,
                };
            },
            cache: true,
        },
        placeholder: "Search for a Product",
        templateResult: formatPostProducts,
        templateSelection: ProductsSelection,
    });
});

function formatPostProducts(p) {
    if (p.loading) {
        return p.text;
    }
    var str = p.name + '- ' + p.variant_name;
    str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        return letter.toUpperCase();
    });
    var $products = $(
        "<div class='select2-result-postsitory clearfix'>" +
        "<div class='select2-result-postsitory__meta'>" +
        '<span><img src="' +
        p.image +
        '" width="28px" class="img-fluid drop-down-img"/> <strong>' +
        str +
        "</strong></span><br>" +
        "<div class='select2-result-repository__stargazers'><i class='fa fa-flag'></i> In " +
        p.category +
        "</div>" +
        "</div>" +
        "</div>"
    );

    return $products;
}

function formatState(p) {
    if (!p.id) {
        return p.text;
    }

    var optimage = $(p.element).attr("data-image");
    if (!optimage) {
        return p.text;
    } else {
        var $opt = $(
            '<span><img src="' + optimage + '" width="28px" /> ' + p.text + "</span>"
        );
        return $opt;
    }
}

function ProductsSelection(p) {
    return p.name != undefined ? p.name + ' (' + p.variant_name + ')' : 'Search for a Product';
}

// form-submit-event

$(document).on("click", ".edit_btn", function (e) {
    e.preventDefault();
    var url = $(this).data("url");

    $(".edit-modal-lg")
        .modal("show")
        .find(".modal-body")
        .load(base_url + "/" + url + " .form-submit-event", function () {
            if ($("input[data-bootstrap-switch]").length) {
                $("input[data-bootstrap-switch]").each(function () {
                    $("input[data-bootstrap-switch]").bootstrapSwitch();
                });
            }
        });
});
var variant_data = [];
var qty;
var discount;
var price;
var count = 1;
$(document).on("change", ".search_products", function (e) {
    e.preventDefault();

    data = $(".search_products").select2("data")[0];

    var table_data = new Object();
    // table_data.sr = count;
    table_data.id = data.id;
    price = data.purchase_price;
    table_data.name = data.name + ' (' + data.variant_name + ')';
    // table_data.id = count;
    table_data.quantity =
        '<input type="number" class="form-control qty" value="1" min="1" data-price ="' +
        price +
        '" name="qty[]" placeholder="Ex.1">';
    table_data.price =
        '<input type="number" class="form-control price" value="' +
        data.purchase_price +
        '" min="1" name="price[]" placeholder="Ex.1">';
    table_data.discount =
        '<input type="number" class="form-control discount" min="1" data-price ="' +
        price +
        '" name="discount[]" placeholder="Ex.1" step = "0.01">';
    table_data.total = '<strong class="table_price">' + price + "</strong>";

    var is_exist = false;

    $.each(variant_data, function (i, e) {

        if (e.id == data.id) {

            iziToast.error({
                message: `<span style="text-transform:capitalize">${data.name + ' (' + data.variant_name + ')'} is already in list!</span> `,
            });
            is_exist = true;
            return false;
        }
    });

    if (is_exist === false) {
        product_details(this);
        $("#purchase_order").bootstrapTable("insertRow", {
            index: 0,
            row: table_data,
        });
        count++;
    }
});
if ($.fn.editable != undefined) {
    $.fn.editable.defaults.mode = "inline";
    $(document).ready(function () {
        $("#username").editable();
    });
}


function product_details(e) {
    variant_data.push({
        id: data.id,
        name: data.variant_name,
        price: data.purchase_price,
    });

    $('input[name="products"]').val(JSON.stringify(variant_data));
}

$(document).on("keyup", ".qty", function (e) {
    subTotal(this);
    purchase_total();
});
$(document).on("change", ".qty", function (e) {
    subTotal(this);
    purchase_total();
});
$(document).on("change", ".price", function (e) {
    settlePrice(this);
    purchase_total();
});
$(document).on("keyup", ".price", function (e) {
    settlePrice(this);
    purchase_total();
});
$(document).on("keyup", ".discount", function (e) {
    settleDisount(this);
    purchase_total();
});
$(document).on("change", ".discount", function (e) {
    settleDisount(this);
    purchase_total();
});

function subTotal(e) {
    $("#sub_total").html("");

    var qty = $(e).val();
    var table_subtotal =
        e.parentElement.parentElement.getElementsByClassName("table_price");
    var price = $(
        e.parentElement.parentElement.getElementsByClassName("price")
    ).val();
    var discount = $(
        e.parentElement.parentElement.getElementsByClassName("discount")
    ).val();
    $(table_subtotal).html("0");
    if (qty != 0 && qty != null) {
        var sub_total = parseFloat(price) * parseFloat(qty);
        $(table_subtotal).html(sub_total);
    }
    if (discount != 0 && discount != null) {
        var sub_total = parseFloat(price) * parseFloat(qty) - parseFloat(discount);
        $(table_subtotal).html(sub_total);
    }
}

function settlePrice(e) {
    var price = $(e).val();
    $("#sub_total").html("");
    var table_subtotal =
        e.parentElement.parentElement.getElementsByClassName("table_price");
    var price_class = $(e.parentElement.getElementsByClassName("price"));
    var qty = $(
        e.parentElement.parentElement.getElementsByClassName("qty")
    ).val();
    var discount = $(
        e.parentElement.parentElement.getElementsByClassName("discount")
    ).val();
    var price = $(price_class).val();
    var sub_total = parseFloat(price) * parseFloat(qty);
    $(table_subtotal).html(sub_total);
    if (price != 0 && price != null) {
        $(table_subtotal).html(sub_total);
        if (qty != 0 && qty != null) {
            sub_total = parseFloat(price) * parseFloat(qty);
            $(table_subtotal).html(sub_total);
        }
        if (discount != 0 && discount != null) {
            var sub_total =
                parseFloat(price) * parseFloat(qty) - parseFloat(discount);
            $(table_subtotal).html(sub_total);
        }
    }
}
var $table = $("#purchase_order");
var $remove = $("#remove");
$(function (e) {
    $table.on(
        "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
        function () {

            $remove.prop("disabled", !$table.bootstrapTable("getSelections").length);
        }
    );
    $remove.click(function () {
        var ids = $.map($table.bootstrapTable("getSelections"), function (row) {
            return row.id;
        });
        let products = JSON.parse($("#products").val());
        let new_products_list = products.filter(function (product) {
            return !ids.includes(product.id);
        });
        variant_data = new_products_list

        $table.bootstrapTable("remove", {
            field: "id",
            values: ids,
        });
        purchase_total();
        $('input[name="products"]').val(JSON.stringify(new_products_list));
        $remove.prop("disabled", true);
    });
});

function settleDisount(e) {
    var discount = $(e).val();
    $("#sub_total").html("");
    var table_subtotal =
        e.parentElement.parentElement.getElementsByClassName("table_price");
    var qty = $(
        e.parentElement.parentElement.getElementsByClassName("qty")
    ).val();
    var price = $(
        e.parentElement.parentElement.getElementsByClassName("price")
    ).val();
    var sub_total = parseFloat(price) * parseFloat(qty);
    $(table_subtotal).html(sub_total);
    if (discount != 0 && discount != null) {
        sub_total = parseFloat(price) * parseFloat(qty) - parseFloat(discount);
        $(table_subtotal).html(sub_total);
    }
}
$(document).on("change", "#tax_id,#order_taxes", function (e) {
    purchase_total();
});
$(document).on("keyup", "#order_discount", function (e) {
    purchase_total();
});
$(document).on("keyup", "#shipping", function (e) {
    purchase_total();
});

function purchase_total() {

    var total = 0;
    var final_total;
    var tax;
    var length = $(".table_price").length;
    for (var i = 0; i < length; i++) {
        var price = $(".table_price")[i];
        total += parseFloat(price.innerHTML);
    }
    var order_tax = $("#tax_id").find(":selected").attr("data-percentage");

    var taxVal = $('#order_taxes').val();



    var discount = $("#order_discount").val();
    var shipping = $("#shipping").val();
    var currency = $("#sub_total").attr("data-currency");
    $("#sub_total").html(currency + total);
    $('input[name="total"]').val(total);


    if (order_tax != 0 && order_tax != null) {
        order_tax = parseFloat(order_tax) / 100;

        tax = total * order_tax;
        final_total = total + tax;
        $("#sub_total").html(currency + final_total);
        $('input[name="total"]').val(final_total);
    }

    if (typeof taxVal !== 'undefined' && taxVal !== '') {
        let taxes = typeof taxVal === 'string' ? JSON.parse(taxVal) : taxVal;

        let totalPercentage = 0;

        taxes.forEach(function (tax) {
            totalPercentage += parseFloat(tax.percentage);
        });

        totalPercentage = parseFloat(totalPercentage) / 100;

        var total_tax = total * totalPercentage;
        final_total = total + total_tax;

        $("#sub_total").html(currency + final_total);
        $('input[name="total"]').val(final_total);
    }

    if (discount != 0 && discount != null) {
        final_total = final_total - parseFloat(discount);
        $("#sub_total").html(currency + final_total);
        $('input[name="total"]').val(final_total);
    }
    if (shipping != 0 && shipping != null) {
        final_total = final_total + parseFloat(shipping);
        $("#sub_total").html(currency + final_total);
        $('input[name="total"]').val(final_total);
    }
    if (order_tax == 0 || order_tax == undefined) {
        discount = (discount != undefined && discount > 0) ? discount : 0;
        shipping = (shipping != undefined && shipping > 0) ? shipping : 0;
        total_tax = (total_tax != undefined && total_tax > 0) ? total_tax : 0;
        final_total = (total + parseFloat(shipping) + parseFloat(total_tax)) - parseFloat(discount);
        $("#sub_total").html(currency + final_total);
        $('input[name="total"]').val(final_total);
    }
}

// purchase order status update bulk
$(".purchase_update_status_bulk ").on("click", function (e) {
    e.preventDefault();
    var item = update_bulk_status(item_id);
    if (!item) {
        return;
    } else {
        var item_ids = item[0];
        var status = item[1];
        var type = item[2];
        var order_id = item[3];
        const request_body = {
            [csrf_token]: csrf_hash,
            item_ids: item_ids,
            status: status,
            type: type,
            order_id: order_id,
        };
        $.ajax({
            type: "post",
            url: base_url + "/vendor/purchases/update_status_bulk",
            data: request_body,
            cache: false,
            dataType: "json",
            success: function (result) {
                csrf_token = result["csrf_token"];
                csrf_hash = result["csrf_hash"];
                if (result.error == true) {
                    showToastMessage(result.message, result.type);
                } else {
                    showToastMessage(result.message, result.type);
                    location.reload();
                }
            },
        });
    }
});

// purchase order status update individual

function update_order_status(e) {
    var status = $(e).find("option:selected").val();
    var order_id = $(e).find(":selected").attr("data-order_id");
    var type = $(e).find(":selected").attr("data-type");

    $.ajax({
        type: "get",
        url: site_url + "/vendor/purchases/update_order_status/",
        data: {
            status: status,
            order_id: order_id,
            type: type,
        },
        cache: false,
        dataType: "json",
        success: function (result) {
            if (result.error == false) {
                iziToast.success({
                    title: "Success!",
                    message: result.message,
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Error!",
                    message: result.message,
                    position: "topRight",
                });
                location.reload();
            }
        },
    });
}
$(".purchase_status_update").on("change", function () {
    update_order_status(this);
});

// bulk uplaod submit button
$(document).on("submit", "#bulk_uploads_form", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
        type: "POST",
        url: this.action,
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,

        success: function (result) {
            if (result.error == false) {
                setTimeout(() => {
                    showToastMessage(result.message, "success");
                }, 1000);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showToastMessage(result.message, "error");
                return;
            }
        },
    });
});

if ($("#charttest").length > 0) {
    var ctx = document.getElementById("charttest").getContext("2d");
    var total_sale = [];
    var month_name;
    var data = [];
    var myChart = [];
    var total_purchase = [];
    var month_name_purchase;
    var data_p = [];

    $.ajax({
        type: "get",
        url: site_url + "/vendor/home/fetch_purchases",
        cache: false,
        dataType: "json",
        success: function (result) {

            total_sale = result.total_sale;
            month_name = result.month_name;
            total_purchase = result.total_purchases;
            month_name_purchase = result.month_name;
            var myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: month_name,
                    datasets: [{
                        label: "sales",
                        data: total_sale,
                        borderWidth: 2,
                        backgroundColor: "rgba(63,82,227,.8)",
                        borderWidth: 0,
                        borderColor: "transparent",
                        pointBorderWidth: 0,
                        pointRadius: 2.5,
                        pointBackgroundColor: "transparent",
                        pointHoverBackgroundColor: "rgba(63,82,227,.8)",
                    },
                    {
                        label: "Purchase",
                        data: total_purchase,
                        borderWidth: 2,
                        backgroundColor: "rgba(254,86,83,.7)",
                        borderWidth: 0,
                        borderColor: "transparent",
                        pointBorderWidth: 0,
                        pointRadius: 2.5,
                        pointBackgroundColor: "transparent",
                        pointHoverBackgroundColor: "rgba(254,86,83,.8)",
                    },
                    ],
                },
                options: {
                    legend: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                // display: false,
                                drawBorder: false,
                                color: "#f2f2f2",
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1500,
                                callback: function (value, index, values) {
                                    return "$" + value;
                                },
                            },
                        },],
                        xAxes: [{
                            gridLines: {
                                display: false,
                                tickMarkLength: 15,
                            },
                        },],
                    },
                },
            });
        },
    });
}


// fetch stock on select 2

$(document).ready(function () {
    $(".fetch_stock").select2({
        ajax: {
            url: site_url + "vendor/products/fetch_stock",
            dataType: "json",
            data: function (params) {
                var query = {
                    search: params.term,
                };
                return query;
            },
            processResults: function (response) {
                return {
                    results: response.data,
                };
            },
            cache: true,
        },
        placeholder: "Search for a Products",
        templateResult: format,
        templateSelection: StockSelection,
    });
});

function format(p) {
    if (p.loading) {
        return p.text;
    }
    var str = p.name;
    str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        return letter.toUpperCase();
    });
    var $products = $(
        "<div class='select2-result-postsitory clearfix'>" +
        "<div class='select2-result-postsitory__meta'>" +
        '<span><img src="' +
        site_url +
        p.image +
        '" width="28px" class="img-fluid"/> <strong>' +
        str +
        "</strong></span><br>" +
        "</div>" +
        "</div>"
    );

    return $products;
}

function StockSelection(p) {
    return p.name;
}

$(document).on("show.bs.modal", "#new_stock", function (event) {
    $(this).hide().show();
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var id = $(current_selected_variant).data("product_id");
    var stock_management = $(current_selected_variant).data("stock_management");
    var stock = $(current_selected_variant).data("stock");
    var name = $(current_selected_variant).data("name");
    let variant_id = $(current_selected_variant).data("variant_id");
    var options;
    $('input[name="product"]').val(id);
    $('input[name="variant_id"]').val(variant_id);
    $('input[name="stock_management"]').val(stock_management);
    $('input[name="current_stock"]').val(stock);
    $('input[name="name"]').val(name);
    $("#fetch_stock_1").val(id).trigger("change");
});
$(document).on("show.bs.modal", "#transfer_stock", function (event) {
    $(this).hide().show();
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;

    let name = $(current_selected_variant).data("name");
    let variant_id = $(current_selected_variant).data("variant_id");

    $('input[name="ts_variant_id"]').val(variant_id);

    $('input[name="ts_name"]').val(name);

});

$(document).on("show.bs.modal", "#expenses_modal", function (event) {
    var triggerElement = $(event.relatedTarget);
    var current_selected_variant = triggerElement;
    var expenses_id = $(current_selected_variant).data("id");
    $('input[name="expenses_id"]').val(id);
    var note = $(current_selected_variant).data("note");
    $('input[name="note"]').val(note);
    var amount = $(current_selected_variant).data("amount");
    $('input[name="amount"]').val(amount);
    var expenses_type = $(current_selected_variant).data("expenses_type");
    $('input[name="expenses_type"]').val(expenses_type);
    var expenses_date = $(current_selected_variant).data("expenses_date");
    $('input[name="expenses_date"]').val(expenses_date);
});

// report date filter

var start_date = "";
var end_date = "";
var payment_type_filter = "";

$("#payment_type_filter").on("change", function () {
    payment_type_filter = $(this).find("option:selected").val();
});

$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});

$("#clear").on("click", function () {
    start_date = "";
    end_date = "";
    $('input[name="daterange"]').val("Date Range Picker");
    $('input[name="date_range"]').val("Date Range Picker");
    $('#payment_status_filter').val("");
    $('#payment_method').val("");
    $('#order_type_filter').val("");
    $('#transaction_status').val("");

    $(".table").bootstrapTable("refresh");

});

function reports_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        payment_type_filter: payment_type_filter,
    };
}
$("#apply").on("click", function (e) {
    $("#payment_reports_table").bootstrapTable("refresh");
});

// sales date filter

var start_date = "";
var end_date = "";
$("#payment_type_filter").on("change", function () {
    payment_type_filter = $(this).find("option:selected").val();
});
$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});

$("#date").on("change", function () { });

function reports_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        payment_type_filter: payment_type_filter,
    };
}
$("#apply").on("click", function (e) {
    $("#sales_summary_table").bootstrapTable("refresh");
});

// profit loss filter
var start_date = "";
var end_date = "";

$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});

$("#date_profit_loss").on("change", function () { });

function pl_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
    };
}
$("#apply").on("click", function (e) {
    $("#profit_loss_table").bootstrapTable("refresh");
});


// best_customers
$("#date_best_customers").on("change", function () { });

function best_customers_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
    };
}
$("#apply").on("click", function (e) {
    $("#best_customers_table").bootstrapTable("refresh");
});



// top selling products
var start_date = "";
var end_date = "";

$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});

$("#date_top_selling_products").on("change", function () { });

function top_selling_products_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
    };
}
$("#apply").on("click", function (e) {
    $("#top_selling_products_table").bootstrapTable("refresh");
});



var start_date = "";
var end_date = "";

$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});

var category_id = "";
$("#product_category").on("change", function () {
    category_id = $(this).find("option:selected").val();
    $('#products_table').bootstrapTable('refresh');
});
let filter_brand_id = "";
$(document).on('change', '#product_brand', function () {
    filter_brand_id = $(this).val();

    $('#products_table').bootstrapTable('refresh');
});
function cat_query(params) {
    var search = $('#products_table').bootstrapTable('getOptions').searchText;
    params.search = search || '';
    params.category_id = category_id;
    params.brand_id = filter_brand_id;

    return params;
}
$(document).ready(function () {
    $('.select_product').select2();
});

// purchases report

var start_date = "";
var end_date = "";
var payment_status_filter = "";
var supplier_id = "";

$("#payment_status_filter").on("change", function () {
    payment_status_filter = $(this).find("option:selected").val();
});

$("#supplier_filter").on("change", function () {
    supplier_id = $(this).find("option:selected").val();
});

$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        function (start, end) {
            start_date = start.format("YYYY-MM-DD");
            end_date = end.format("YYYY-MM-DD");
        }
    );
});




function purchase_report_query(p) {
    return {
        search: p.search,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        start_date: start_date,
        end_date: end_date,
        payment_status_filter: payment_status_filter,
        supplier_id: supplier_id,
    };
}
$("#apply").on("click", function (e) {
    $("#purchase_report_table").bootstrapTable("refresh");
});

function scanned_barcode(e) {
    var barcode_value = "";
    barcode_value = e;
    var limit = $("input[name=limit]").val();
    var offset = $("input[name=offset]").val();
    var search = $("#search_product").val();
    var flag = null;

    $.ajax({
        type: "GET",
        url: site_url + "/vendor/products/scanned_barcode_items",
        cache: false,
        data: {
            variant_id: barcode_value,
            search: search,
            limit: limit,
            offset: offset,
        },

        dataType: "json",
        success: function (result) {
            var data = (result);

            if (result.error == true) {

                iziToast.error({
                    title: 'Error Occurred',
                    message: result.message,
                    position: "topRight",
                });

            } else {
                var products = result.data;
                if (products) {
                    var product_id = products['id'];
                    var tax_id = products['tax_id'];
                    var business_id = products['business_id'];
                    var is_tax_included = products['is_tax_included'];
                    var product_variants = products['variants'][0];

                    var product_variant_id = product_variants['id'];
                    var variant_name = product_variants['variant_name'];
                    var product_name = products['name'];
                    var image = site_url + products['image'];
                    var price = product_variants['sale_price'];
                    var cart_item = {
                        product_id: product_id,
                        tax_id: tax_id,
                        business_id: business_id,
                        is_tax_included: is_tax_included,
                        product_variant_id: product_variant_id,
                        product_name: product_name,
                        variant_name: variant_name,
                        image: image,
                        price: price,
                        quantity: 1,
                    };

                    var session_business_id = $("#business_id").val();

                    var cart = localStorage.getItem("cart" + session_business_id);
                    cart =
                        localStorage.getItem("cart" + session_business_id) !== null ?
                            JSON.parse(cart) :
                            null;
                    if (cart !== null && cart !== undefined) {
                        if (cart.find((item) => item.product_variant_id === product_variant_id)) {
                            var message = "This item is already present in your cart";
                            show_message("Oops!", message, "error");
                            return;
                        }
                        message = "Adding item to cart";
                        cart.push(cart_item);
                    } else {
                        cart = [cart_item];
                    }
                    localStorage.setItem("cart" + business_id, JSON.stringify(cart));
                    display_cart();
                    final_total();
                }
            }
        },
    });


};



//sales order 
var product = [];
var qty;
var discount;
var price;
var count = 1;
$(document).on("change", ".search_products", function (e) {

    e.preventDefault();
    data = $(".search_products").select2("data")[0];
    var table_data = new Object();
    price = data.sale_price;
    table_data.name = data.name + "-" + data.variant_name;
    table_data.image = '<img src="' +
        data.image +
        '" width="60px"  class="img-fluid"/>';

    table_data.sr = count;
    table_data.id = data.id;

    table_data.quantity =
        '<input type="number" class="form-control qty" value="1" min="1" data-price ="' +
        price +
        '" name="qty[]" placeholder="Ex.1">';
    table_data.price =
        '<input type="number" class="form-control price" value="' +
        data.sale_price +
        '" min="1" name="price[]" step="0.01"  placeholder="Ex.1">';
    table_data.discount =
        '<input type="number" class="form-control discount" min="1" data-price ="' +
        price +
        '" name="discount[]" placeholder="Ex.1" step = "0.01">';
    table_data.total = '<strong class="table_price">' + price + "</strong>";

    var is_exist = false;

    $.each(product, function (i, e) {
        if (e.name === data.variant_name) {
            iziToast.error({
                message: `<span style="text-transform:capitalize">${data.variant_name} is already in list!</span> `,
            });
            is_exist = true;
            return false;
        }
    });

    if (is_exist === false) {
        product.push({
            variant_id: data.id,
            name: data.name,
            price: data.sale_price,
            product_id: data.product_id,

            variant_name: data.variant_name,


        });

        $('#sale_product_id').val(JSON.stringify(product));
    }
    $("#sales_order").bootstrapTable("insertRow", {
        index: 0,
        row: table_data,
    });
    count++;
    purchase_total();
});


$(function (e) {
    $("#sales_order").on(
        "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
        function () {

            $("#remove_sales_order").prop("disabled", !$("#sales_order").bootstrapTable("getSelections").length);
        }
    );
    $("#remove_sales_order").click(function () {
        var ids = $.map($("#sales_order").bootstrapTable("getSelections"), function (row) {
            return row.id;
        });
        let products = JSON.parse($("#products").val());
        let new_products_list = products.filter(function (product) {
            return !ids.includes(product.id);
        });
        variant_data = new_products_list

        $("#sales_order").bootstrapTable("remove", {
            field: "id",
            values: ids,
        });
        purchase_total();
        $('input[name="products"]').val(JSON.stringify(new_products_list));
        $("#remove_sales_order").prop("disabled", true);
    });
});


let code = "";
let reading = false;
// var scanned_barcode = "";
document.addEventListener('keypress', e => {
    //usually scanners throw an 'Enter' key at the end of read
    if (e.keyCode == 13) {
        if (code.length >= 1) {
            scanned_barcode(code);
        }
    } else {
        code += e.key; //while this is not an 'enter' it stores the every key            
    }

    //run a timeout of 200ms at the first read and clear everything
    if (!reading) {
        reading = true;
        setTimeout(() => {
            code = "";
            reading = false;
        }, 200); //200 works fine for me but you can adjust it
    }

    // alert(scanned_barcode);
});

// payment remainder 
$("#filter").on("click", function (e) {
    $("#payment_reminder_table").bootstrapTable("refresh");
});

function payment_reminder(order_id) {
    var order_id = order_id;
    Swal.fire({
        title: "Send Reminder Message",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "GET",
                    url: site_url + "/vendor/orders/send_reminder",
                    data: {
                        "order_id": order_id,
                    },
                    dataType: "json",
                    success: function (result) {
                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];
                        if (result.error == false) {

                            Swal.fire('Success', result.message, 'success');
                        } else {

                            Swal.fire('Error!', result.message, 'error');
                        }

                    }
                });
            });
        },
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Reminder was not sent.', 'error');
        }
    })
}

// Quantity Alert Message


function sendEmailNotification(product, variant, image) {

    $.ajax({
        url: site_url + 'vendor/products/send_email_stock_alert',
        type: 'POST',
        data: {
            product: product,
            variant: variant,
            image: image
        },
        dataType: 'json',
        success: function (response) {

        },
        error: function (xhr, status, error) {
            console.error('Error sending email notification:', error);
        }
    });
}

$(window).on('load', function () {
    $.ajax({
        url: site_url + 'vendor/products/stock_alert',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var products = response.rows;
            $.each(products, function (index, value) {
                if (value.qty_alert > value.stock) {
                    var showNotification = true;
                    var sendNotification = true;
                    var emailSent = sessionStorage.getItem('emailSent_' + value.product_id);
                    if (sessionStorage.getItem('dontShowAgain_' + value.product_id) === 'true') {
                        showNotification = false;
                    }
                    if (showNotification) {
                        if (window.location.href !== site_url + 'admin/purchases/purchase_orders/order') {
                            iziToast.warning({
                                position: 'topRight',
                                css: {
                                    'font-weight': '800'
                                },
                                icon: 'fa fa-box-open',
                                message: value.product + '(' + value.variant_name + ') is going out of stock',
                                buttons: [
                                    ['<button>Redirect to purchases</button>', function (instance, toast) {
                                        window.location.href = site_url + 'admin/purchases/purchase_orders/order';
                                    }],
                                    ['<button>Don\'t show again</button>', function (instance, toast) {
                                        sessionStorage.setItem('dontShowAgain_' + value.product_id, 'true');
                                        instance.hide({
                                            transitionOut: 'fadeOutRight',
                                            onClosing: function (instance, toast, closedBy) { }
                                        }, toast);
                                    }]
                                ],
                            });
                            // Send email notification
                            if (emailSent) {
                                sendNotification = false;

                            }
                            if (sendNotification) {
                                sendEmailNotification(value.product, value.variant_name, value.image);
                                sessionStorage.setItem('emailSent_' + value.product_id, 'true');

                            }
                        }
                    }
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
});

// generate barcode

$("#generate-barcode").on("click", function (e) {

    //   $("#bar-gn").empty();
    $(".barcode").text("");
    $(".barcode").val("");
    e.preventDefault();

    var quantity = $("#quantity").val();
    var barcode_value = "";
    var barcode_name = "";

    if ($("#products_name option:selected").text() == "Select") {
        iziToast.error({
            title: 'Error',
            message: 'Please Select the Product',
            position: "topRight",
        });
        return false;
    } else {
        barcode_value = $("#products_name").val();
        barcode_name = $("#products_name option:selected").text();
    }

    var i = 0;
    var div = "";
    for (i = 0; i < quantity; i++) {
        div =
            '<svg id = "barcode" class="barcode border border-dark m-2 selection-to-print "  jsbarcode-format="auto"jsbarcode-value = "' +
            barcode_value +
            '" jsbarcode-textmargin="5"  jsbarcode-text = "' +
            barcode_value +
            '"jsbarcode-fontoptions="bold"id="barcode"></svg>';
        $("#bar-gn").append(div);
        document
            .getElementById("barcode-print")
            .addEventListener("click", function () {
                var printContents = document.getElementById("printDiv").innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                window.location.reload();
            });
    }
    if (i < 1) {
        iziToast.error({
            title: 'Error',
            message: 'please select the quantity',
            position: "topRight",
        });
    }
    JsBarcode(".barcode").init();
});


$("#barcode-reset").on("click", function (e) {
    Swal.fire({
        title: "Are you sure? ",
        text: "Want to clear Barcode!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, clear it!",
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {

                $("#bar-gn").empty();
                $("#quantity").val("");
                $("#products_name").val('').trigger("change");

                Swal.fire('Success', 'Data Reset Sucessfully.', 'success');

            });
        },
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Barcode is not reset.', 'error');
        }
    });
});

// barcode_modal
// close barcode modal

// $("#barcode_Modal").on("hidden.bs.modal", function (e) {
//   $("#variant-barcode").empty();
// });

function generate_barcode(product_id) {
    var error_msg = "Cannot Display Barcode";

    var data = {
        id: product_id,
    };

    $.ajax({
        type: "GET",
        url: site_url + "/vendor/products/get_products",
        cache: false,
        data: data,
        dataType: "json",
        success: function (result) {
            if (result.error == true) {
                console.log(error_msg);
            } else {
                display_barcode(result.data);
            }
        },
    });
}

function display_barcode(data) {
    if (data == undefined) {
        return;
    }
    var product_name = data[0]["name"];
    var variants = data[0]["variants"];
    var total_variants = variants.length;
    var i = 0;
    var div2 = "";

    for (i = 0; i < total_variants; i++) {
        var variant_id = variants[i]["id"];
        var variant_name = variants[i]["variant_name"];

        div2 =
            '<div class = "col-md-3 text-center"> ' +
            "<h6>" +
            product_name +
            " - " +
            variant_name +
            "  </h6>" +
            '<svg id = "barcode" class="barcode  border border-dark m-2 selection-to-print "  jsbarcode-format="auto"jsbarcode-value = "' +
            variant_id +
            '" jsbarcode-textmargin="5"  jsbarcode-text = "' +
            variant_name +
            '"jsbarcode-fontoptions="bold"id="barcode"></svg></div>';

        $("#variant-barcode").append(div2);
    }
    document
        .getElementById("download-barcode")
        .addEventListener("click", function () {
            var printContents = document.getElementById("printDiv").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });
    JsBarcode(".barcode").init();
    $("#barcode_Modal").on("hidden.bs.modal", function (e) {
        $("#variant-barcode").empty();
    });
}
$(document).on('ready', function () {

    $(document).on("show.bs.modal", "#barcode_Modal", display_barcode());



})

document.getElementById('calculatorIcon').addEventListener('click', function () {
    const dropdown = document.getElementById('dropdownCalculator');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Enable editing on double-click and restrict input to numbers and operators
document.getElementById('display').addEventListener('dblclick', function () {
    this.removeAttribute('readonly');
    this.focus();
});

document.getElementById('display').addEventListener('blur', function () {
    this.setAttribute('readonly', true);
});

// Restrict input to numbers, operators, and control keys (backspace, enter)
document.getElementById('display').addEventListener('keydown', function (event) {
    const allowedKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '-', '*', '/', '.', '(', ')', 'Backspace', 'Enter'];
    if (!allowedKeys.includes(event.key) && !event.ctrlKey && event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
        event.preventDefault();
    }

    // Calculate result on Enter key press
    if (event.key === 'Enter') {
        event.preventDefault();
        calculateResult();
        this.setAttribute('readonly', true);
    }
    if (event.key === 'Delete') {
        this.value = ''; // Clear the display
    }
});

// Calculator functions
function appendValue(value) {
    document.getElementById('display').value += value;
}

function clearDisplay() {
    document.getElementById('display').value = '';
}

function calculateResult() {
    const display = document.getElementById('display');
    try {
        display.value = eval(display.value); // Evaluate the expression
    } catch (error) {
        display.value = 'Error'; // If there's an error in the expression
    }
}

// Backspace function to remove the last character
function backspace() {
    const display = document.getElementById('display');
    display.value = display.value.slice(0, -1); // Remove the last character
}

// Prevent the calculator from closing when clicking inside the calculator area
document.getElementById('dropdownCalculator').addEventListener('click', function (event) {
    event.stopPropagation(); // Prevent event bubbling
});

// Close the dropdown when clicking outside the calculator or the icon
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('dropdownCalculator');
    const calculatorIcon = document.getElementById('calculatorIcon');
    if (!calculatorIcon.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});



$(document).ready(function () {

    if ($("#is_tax_inlcuded").length > 0) {
        if ($("#is_tax_inlcuded").prop('checked')) {
            // if tax is included in price 
            $("#tax_rows").addClass('d-none');
        } else {
            // if tax is excluded in price 
            $("#tax_rows").removeClass('d-none');
        }


        $("#is_tax_inlcuded").on('change', function () {
            let ischaked = $(this).prop('checked');

            if (ischaked) {
                // if tax is included in price 
                $("#tax_rows").addClass('d-none');
            } else {
                // if tax is excluded in price 
                $("#tax_rows").removeClass('d-none');
            }

        });
    }


});

$(document).ready(function () {
    if (document.getElementById('tax_ids')) {

        $.ajax({
            type: "POST",
            url: site_url + "vendor/tax/get_taxs",
            success: function (response) {

                let taxArray = response.taxs;
                taxArray = taxArray.map((tax) => {
                    return { value: tax.name, id: tax.id };
                });

                let inputElm = document.getElementById('tax_ids');

                if (document.getElementById('product_id').value.length > 0) {

                    let products_tax_value = document.getElementById('products_tax_value').value;
                    inputElm.value = products_tax_value;
                    products_tax_value = JSON.parse(products_tax_value);
                    let tagify = new Tagify(inputElm, {
                        enforceWhitelist: true,
                        whitelist: taxArray,
                        tagTextProp: 'name',
                        userInput: false,
                        dropdown: {
                            closeOnSelect: false,
                            enabled: 0,
                        },
                        value: taxArray
                    });

                } else {

                    let tagify = new Tagify(inputElm, {
                        enforceWhitelist: true,
                        whitelist: taxArray,
                        tagTextProp: 'name',
                        userInput: false,
                        dropdown: {
                            closeOnSelect: false,
                            enabled: 0,
                        },
                    });

                }
            }
        });
    }

    if (window.location.href.includes("/purchases")) {
        $.ajax({
            type: "POST",
            url: site_url + "vendor/tax/get_taxs",
            success: function (response) {
                let taxArray = response.taxs;
                taxArray = taxArray.map((tax) => {
                    return { value: tax.name, id: tax.id, percentage: tax.percentage };
                });

                let inputElm = document.getElementById('order_taxes');

                let tagify = new Tagify(inputElm, {
                    enforceWhitelist: true,
                    whitelist: taxArray,
                    tagTextProp: 'name',
                    userInput: false,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
                    },
                });
            }
        });
    }

    if (window.location.href.includes("/services")) {
        $.ajax({
            type: "POST",
            url: site_url + "vendor/tax/get_taxs",
            success: function (response) {
                let taxArray = response.taxs;
                taxArray = taxArray.map((tax) => {
                    return { value: tax.name, id: tax.id, percentage: tax.percentage };
                });

                let inputElm = document.getElementById('service_taxes');

                if (document.getElementById('service_id').value.length > 0) {
                    let service_taxes_values = document.getElementById('service_taxes_values').value;
                    inputElm.value = service_taxes_values;
                    service_taxes_values = JSON.parse(service_taxes_values);
                    let tagify = new Tagify(inputElm, {
                        enforceWhitelist: true,
                        whitelist: taxArray,
                        tagTextProp: 'name',
                        userInput: false,
                        dropdown: {
                            closeOnSelect: false,
                            enabled: 0,
                        },
                        value: taxArray
                    });
                } else {

                    let tagify = new Tagify(inputElm, {
                        enforceWhitelist: true,
                        whitelist: taxArray,
                        tagTextProp: 'name',
                        userInput: false,
                        dropdown: {
                            closeOnSelect: false,
                            enabled: 0,
                        },
                    });
                }
            }
        });
    }
});


$(document).ready(function () {

    $(".No-negative").on('input', function (e) {
        const value = parseInt($(this).val());

        if (value < 0) {
            iziToast.error({
                title: "Error",
                message: "Negative values are not allowed",
                position: "topRight",
            });

            $(this).val('');
        };

    });

});

function editWarehouse(id, url) {

    let formData = new FormData();
    formData.append(csrf_token, csrf_hash);
    formData.append('id', id);

    $.ajax({
        type: "post",
        url: `${url}${id}`,
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
                let data = result.data;
                $("#editWarehouseId").val(id);
                $("#editWarehouseName").val(data.name);
                $("#editWarehouseCountry").val(data.country);
                $("#editWarehouseCity").val(data.city);
                $("#editWarehouseZip_code").val(data.zip_code);
                $("#editWarehouseAddress").val(data.address);

                $("#editWarehouseModel").modal('show');
            }
        },
    });
}

$(document).on('click', ".addWarehouseBtn", function (e) {
    let mutli_lang_remove_warehouse = $("#mutli_lang_remove_warehouse").val();

    let variant_index = $(this).data("variant_index");

    let all_warehouses = $("#all_warehouses").val();
    if (all_warehouses) {
        all_warehouses = JSON.parse(all_warehouses);
        var warehouse_options = "<option value=''>Select Warehouse</option>";
        $.each(all_warehouses, function (i, warehouse) {
            warehouse_options +=
                '<option value = "' +
                warehouse["id"] +
                '" > ' +
                warehouse["name"] +
                "</option>";
        });
    }

    let warehouseRowHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                            <select class="form-control" id="warehouse_id" name="warehouses[${variant_index}][warehouse_ids][]">
                                ${warehouse_options}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_stock" name="warehouses[${variant_index}][warehouse_stock][]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <label for="warehouse_qty_alert">Warehouse Minimum stock level</label><span class="asterisk text-danger">*</span>
                            <input type="number" class="form-control No-negative" id="warehouse_qty_alert" name="warehouses[${variant_index}][warehouse_qty_alert][]">
                        </div>
                    </div>
                    <div class="col-md-2 custom-col">
                        <label for="" class="d-block">${mutli_lang_remove_warehouse}</label>
                        <button class="btn btn-icon btn-danger remove-warehouse" type="button" data-variant_id="<?= $variant['id'] ?>" name="remove_warehouse" data-toggle="tooltip" data-placement="bottom" title="Remove warehouse"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;

    // $(".warehouses").append(warehouseRowHTML);
    $(this).parent().parent().parent().siblings(".warehouses").append(warehouseRowHTML);
});

$(document).on("click", ".remove-warehouse", function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
});

function editBrand(id, route) {
    let formData = new FormData();
    formData.append(csrf_token, csrf_hash);
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
                Object.keys(result.message).map((key) => {
                    iziToast.error({
                        title: "Error!",
                        message: result.message[key],
                        position: "topRight",
                    });
                });
            } else {
                let data = result.data[0];

                $("#brand_id").val(id);
                $("#edit_brand_name").val(data.name);
                $("#edit_brand_description").val(data.description);
                $("#editBrandModal").modal('show');
            }
        },
    });
}

function deleteBrand(id, route) {
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
                            Object.keys(result.message).map((key) => {
                                Swal.fire('Error!', result.message[key], 'error');
                            });
                        } else {
                            Object.keys(result.message).map((key) => {
                                Swal.fire('Success', result.message[key], 'success');
                            });

                        }
                        $("#brand_table").bootstrapTable("refresh");
                    },
                });

            });
        },
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }

    });
}
$(document).on('click', '#chat-scrn', function () {
    var data = $(this).data("value");
    $("nav.navbar.navbar-expand-lg.main-navbar").toggleClass('d-none')
    $("#sidebar-wrapper").parent().toggleClass('d-none');
    if (data == 'max') {
        $('.main-footer').removeClass('chat-hide-show');
        $('.chat-full-screen').addClass('main-content').removeClass('chat-full-screen');
        $("#navebar-bg").addClass('navbar-bg');
        $(this).data("value", "min");
        $(this).children().removeClass('fas chat-scrn fa-compress').addClass('fas chat-scrn fa-expand');
    } else {
        $("#navebar-bg").removeClass('navbar-bg');
        $('.main-footer').addClass('chat-hide-show');
        $(this).children().removeClass('fas chat-scrn fa-expand').addClass('fas chat-scrn fa-compress');
        $('.main-content').addClass('chat-full-screen').removeClass('main-content');
        $(this).data("value", "max");
    }
});
function get_todays_stats() {
    let total_sale;
    let total_purchase;
    let total_expens;

    function fetchAndSetValue(elementId, url, dataKey, callback = null) {
        let el = document.getElementById(elementId);

        if (el) {
            $.ajax({
                type: "get",
                url: base_url + url,
                success: function (response) {
                    let value = 0.00;
                    let res = JSON.parse(response);
                    if (!res.is_error) {
                        value = res.data[dataKey];
                    }
                    let currency = $("#" + elementId).data("currency");
                    $("#" + elementId).text(currency + " " + value);

                    if (callback) callback(value);
                }
            });
        }
    }

    fetchAndSetValue("today_sales", "/vendor/todays_total_sales", "total_amount", (val) => total_sale = val);
    fetchAndSetValue("today_purchase", "/vendor/todays_total_purchase", "total_amount", (val) => total_purchase = val);
    fetchAndSetValue("today_expanse", "/vendor/get_todays_expense", "total_amount", (val) => total_expens = val);
    fetchAndSetValue("today_payments", "/vendor/todays_total_payment_resived", "total_amount");
    fetchAndSetValue("today_payments_remaining", "/vendor/todays_total_payment_remaining", "diffrence");
    fetchAndSetValue("today_paid", "/vendor/todays_total_paids", "total_amount");
    fetchAndSetValue("today_amount_to_pay", "/vendor/todays_total_remaining", "diffrence");
    fetchAndSetValue("today_profit", "/vendor/totdays_profit", "profit");
}
$(document).ready(function () {


    get_todays_stats()
});
function printInvoice() {
    let id = $("#pos_quick_invoice").data('id');
    var printWindow = window.open(base_url + '/vendor/invoices/thermal_print/' + id, '_blank');
    printWindow.onload = function () {
        printWindow.print();
    };
}

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

$(document).ready(function () {
    function updateToWarehouseOptions() {
        const selectedFromId = $("#ts_from_warehouse_id").val();

        $("#ts_to_warehouse_id option").each(function () {
            if ($(this).val() === selectedFromId && selectedFromId !== "") {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }

    $("#ts_from_warehouse_id").on("change", function () {
        updateToWarehouseOptions();
    });

    // Optional: Reset filtering if 'To Warehouse' is changed too
    $("#ts_to_warehouse_id").on("change", function () {
        const selectedToId = $(this).val();

        $("#ts_from_warehouse_id option").each(function () {
            if ($(this).val() === selectedToId && selectedToId !== "") {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});

// First register any plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType
);

// Turn input element into a pond

$(".filepond").filepond({
    credits: null,
    allowFileSizeValidation: "true",
    maxFileSize: "25MB",
    labelMaxFileSizeExceeded: "File is too large",
    labelMaxFileSize: "Maximum file size is {filesize}",
    allowFileTypeValidation: true,

    labelFileTypeNotAllowed: "File of invalid type",
    fileValidateTypeLabelExpectedTypes:
        "Expects {allButLastType} or {lastType}",
    storeAsFile: true,
    allowPdfPreview: true,
    pdfPreviewHeight: 320,
    pdfComponentExtraParams: "toolbar=0&navpanes=0&scrollbar=0&view=fitH",
    allowVideoPreview: true,
    allowAudioPreview: true,
    onprocessfile: function (error, file) {
        if (!error) {
            // Clear the image view area
            const pond = FilePond.create(
                document.querySelector(".filepond-input")
            );
            pond.removeFiles();
            $(".filepond--root .filepond--image-preview").html("");
        }
    },
});
var current_selected_image;
$('#upload-media').on('click', function () {
    $('.image-upload-section').removeClass('d-none');
    var $result = $('#media-upload-table').bootstrapTable('getSelections');

    var path = base_url + $result[0].sub_directory + $result[0].name;
    var sub_directory = $result[0].sub_directory + $result[0].name;
    var media_type = $('#media-upload-modal').find('input[name="media_type"]').val();
    var input = $('#media-upload-modal').find('input[name="current_input"]').val();
    var is_removable = $('#media-upload-modal').find('input[name="remove_state"]').val();
    var ismultipleAllowed = $('#media-upload-modal').find('input[name="multiple_images_allowed_state"]').val();
    var removable_btn = (is_removable == '1') ? '<button class="remove-image btn btn-danger btn-xs mt-3">Remove</button>' : '';

    $(current_selected_image).closest('.form-group').find('.image').removeClass('d-none');
    if (ismultipleAllowed == '1') {

        for (let index = 0; index < $result.length; index++) {
            $(current_selected_image).closest('.form-group').find('.image-upload-section').append('<div class="image-box-200 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image"><div class="image-upload-div"><img class="img-fluid" alt="' + $result[index].name + '" title="' + $result[index].name + '" src=' + base_url + $result[index].sub_directory + $result[index].name + ' ><input type="hidden" name=' + input + ' value=' + $result[index].sub_directory + $result[index].name + '></div>' + removable_btn + '</div>');
        }
    } else {
        path = (media_type != 'image') ? base_url + 'assets/admin/images/' + media_type + '-file.png' : path;

        $(current_selected_image).closest('.form-group').find('.image-upload-section').html('<div class="image-box-200 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image"><div class="image-upload-div"><img class="img-fluid" alt="' + $result[0].name + '" title="' + $result[0].name + '" src=' + path + ' ><input type="hidden" name=' + input + ' value=' + sub_directory + '></div>' + removable_btn + '</div>');
    }


    current_selected_image = '';

    $('#media-upload-modal').modal('hide');
});

$(document).on('show.bs.modal', '#media-upload-modal', function (event) {
    var triggerElement = $(event.relatedTarget);

    current_selected_image = triggerElement;

    var input = $(current_selected_image).data('input');
    var isremovable = $(current_selected_image).data('isremovable');
    var ismultipleAllowed = $(current_selected_image).data('is-multiple-uploads-allowed');
    var media_type = ($(current_selected_image).is('[data-media_type]')) ? $(current_selected_image).data('media_type') : 'image';
    $('#media_type').val(media_type);
    if (ismultipleAllowed == 1) {
        $('#media-upload-table').bootstrapTable('refreshOptions', {
            singleSelect: false,
        });
    } else {
        $('#media-upload-table').bootstrapTable('refreshOptions', {
            singleSelect: true,
        });
    }
    $(this).find('input[name="current_input"]').val(input);
    $(this).find('input[name="remove_state"]').val(isremovable);
    $(this).find('input[name="multiple_images_allowed_state"]').val(ismultipleAllowed);
});



function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}
$(document).on('click', '.copy-to-clipboard', function () {

    var $element = $(this).closest('tr').find('.path');


    copyToClipboard($element);
    iziToast.success({
        message: 'Image path copied to clipboard',
    });
});
$(document).on('click', '.copy-relative-path', function () {
    var $element = $(this).closest('tr').find('.relative-path');
    copyToClipboard($element);
    iziToast.success({
        message: 'Image path copied to clipboard',
    });
});

$(document).on('click', '.delete-media', function () {

    var id = $(this).data('id');
    var t = this;
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'vendor/media/delete/' + id,
                    dataType: 'json',
                    success: function (result) {


                        csrf_token = result["csrf_token"];
                        csrf_hash = result["csrf_hash"];
                        if (result.error == true) {
                            Object.keys(result.message).map((key) => {
                                Swal.fire('Error!', result.message[key], 'error');
                            });
                        } else {
                            Object.keys(result.message).map((key) => {
                                Swal.fire('Success', result.message[key], 'success');
                            });

                        }
                        $('.table').bootstrapTable('refresh');

                    }
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
});

$(".form-submit-event").on("submit", function (e) {
    e.preventDefault();

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
    var isValid = 1;

    //check this only for team_members form
    if (currentUrl.includes("/team_members")) {
        if ($("#password_confirm").val() != $("#password").val()) {
            isValid = 0;
        }

        if (!isValid) {
            iziToast.error({
                title: "Error!",
                message: "Confirm password is not same as password",
                position: "topRight",
            });
            return
        }
    }

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
                    if (result.data.old != undefined && result.data.new != undefined && result.data.old != "" && result.data.new != "") {
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
