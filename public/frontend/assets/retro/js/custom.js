"use strict";

$(function () {
    set_active_link();
});

function set_active_link() {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
    path = decodeURIComponent(path);
    path = document.location.href;

    $(".navbar li a").each(function () {
        var href = $(this).attr("href");

        if (href === path) {
            $('.navbar li a').removeClass('active')
            $(this).closest("a").addClass("active");
        }
    });
}

function showToastMessage(message, type) {
    switch (type) {
        case "error":
            $().ready(
                iziToast.error({
                    title: "Error",
                    message: message,
                    position: "topRight",
                })
            );
            break;

        case "success":
            $().ready(
                iziToast.success({
                    title: "Success",
                    message: message,
                    position: "topRight",
                })
            );
            break;
    }
}


$('.tenures').on('change', function () {
    var id = $(this).attr("data-package_id");
    var price = $(this).find(":selected").attr("data-price");
    var discount_value = $(this).find(":selected").attr("data-discount");
    if (discount_value == '0') {
        var value = price;
    } else {
        var value = discount_value;
    };
    var myvar = '<small class="discount-font">(<del>' + value + '</del>)</small></div>'

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

$("#get_maareeye").on('click', function () {
    window.location.href = base_url + "/login";
});

function set_admin() {
    $("#identity").val("9876543210");
    $("#password").val("12345678");
}

function set_vendor() {
    $("#identity").val("9999999999");
    $("#password").val("12345678");
}

function set_delivery_boy() {
    $("#identity").val("4445555");
    $("#password").val("12345678");
}
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
                Object.keys(result.message).map((key) => {
                    showToastMessage(result.message[key], "error");
                });
            } else {
                if (result.vendor == true) {
                    setTimeout(function () {
                        location.href = base_url + "/vendor/home";
                    }, 500);
                    showToastMessage(result.message, "success")

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

$('#register_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#register_btn').html('Please Wait..');
            $('#register_btn').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                showToastMessage(result.message, "error");

            } else {
                showToastMessage(result.message, "success");
                window.location = base_url + '/login';

            }
        }
    });
})
$('#contact_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);
    $.ajax({
        type: "post",
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#contact_submit').html('Please Wait..');
            $('#contact_submit').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result.error == true) {
                showToastMessage(result.message, "error");

            } else {
                showToastMessage(result.message, "success");
            }
        }
    });
})
$('#forgot_password').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrf_token, csrf_hash);

    $.ajax({
        type: 'POST',
        url: this.action,
        data: formData,
        beforeSend: function () {
            $('#contact_submit').html('Please Wait..');
            $('#contact_submit').attr('disabled', true);
        },
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'json',

        success: function (result) {
            csrf_token = result['csrf_token'];
            csrf_hash = result['csrf_hash'];
            if (result['error'] == false) {

                $('#forgot_password_form').hide();
                $('#success-result').html(result['message']);
                $('#success-result').removeClass("d-none");
            } else {
                $('#forgot_password_form').show();
                $('#login-result').html(result['message']);
                $('#login-result').show().delay(6000).fadeOut();
                $('#login-result').removeClass("d-none").delay(6000).queue(function () {
                    $(this).addClass("d-none").dequeue();
                });
                $('#loginbtn').val('Forgot Password');
                $('#loginbtn').attr('disabled', false);
            }

        }
    });
});

window.addEventListener('load', () => {
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    })
});

$(document).on('click', '.mobile-nav-toggle', function (e) {
    document.querySelector('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
});

$(document).on('click', '.navbar .dropdown > a', function (e) {
    if (document.querySelector('#navbar').classList.contains('navbar-mobile')) {
        e.preventDefault()
        this.nextElementSibling.classList.toggle('dropdown-active')
    }
})

$("#sign_in").show();
$("#sign_up").hide();

$("#register_btn_of_login").on('click', function () {
    $("#sign_in").hide();
    $("#sign_up").show();
})

$("#login_btn_of_register").on('click', function () {
    $("#sign_in").show();
    $("#sign_up").hide();
})

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