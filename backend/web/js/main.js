$(".video iframe").each(function () {
    var width = $(this).width();
    $(this).css("height", width / 1.7777 + "px");
});

$('#pagination').change(function () {
    var pagination_value = $("#pagination").val();
    var pagination_name = $("#pagination").data('pagination_name');
    $.ajax({
        url: '/ajax/pagination',
        type: 'GET',
        data: {pagination_name: pagination_name, pagination_value: pagination_value},
        success: function (r) {
            if (r) {
                window.location.reload(true);
            }
        }
    });
});


$('.all_tag_customer_newsletter').on('click', function () {
    var checked = $('.all_tag_customer_newsletter').prop('checked');
    switch (checked) {
        case true:
            $('.tag_customer_newsletter').prop('checked', true);
            break;
        case false:
            $('.tag_customer_newsletter').prop('checked', false);
            break;
    }
});

//order
var order_id = $('.order_id').text();
$('.edit_mod').on('click', function (e) {
    var mod_id = $(this).data('mod_id');
    $.ajax({
        url: '/shop/order/edit-mod',
        data: {mod_id: mod_id, order_id: order_id},
        type: 'GET',
        success: function (res) {
            $('#edit_mod .modal-body').html(res);
            $('#edit_mod').modal();
        }
    });
});
//blogger
$('.checked-pay-blogger-all').on('click', function () {
    var checked = $('.checked-pay-blogger-all').prop('checked');
    switch (checked) {
        case true:
            $('.pay-blogger-checkbox').prop('checked', true);
            break;
        case false:
            $('.pay-blogger-checkbox').prop('checked', false);
            break;
    }
});
$('.btn-pay-blogger').on('click', function () {
    var x = 0;
    order_id = [];
    $.each($('.pay-blogger-checkbox:checked'), function () {
        order_id[x] = $(this).data('order_id');
        x++;
    });
    $('.input-pay-blogger').val(JSON.stringify(order_id));
});
$('.checked-pay-blogger-all-wholesale').on('click', function () {
    var checked = $('.checked-pay-blogger-all-wholesale').prop('checked');
    switch (checked) {
        case true:
            $('.pay-blogger-checkbox-wholesale').prop('checked', true);
            payBlogger();
            break;
        case false:
            $('.pay-blogger-checkbox-wholesale').prop('checked', false);
            $('#sumDebt').text(0);
            $('#sumTotalPayed').text(0);
            break;
    }
});
$('.btn-pay-blogger-wholesale').on('click', function () {
    let x = 0;
    blogger_id = [];
    $.each($('.pay-blogger-checkbox-wholesale:checked'), function () {
        blogger_id[x] = $(this).data('blogger_id');
        x++;
    });
    $('#input-pay-blogger-wholesale').val(JSON.stringify(blogger_id));
});

function payBlogger() {
    let sumDebt = 0;
    let sumTotalPayed = 0;
    $.each($('.pay-blogger-checkbox-wholesale:checked'), function () {
        sumDebt += $(this).data('sum_debt');
        sumTotalPayed += $(this).data('sum_total_payed');
    });
    $('#sumDebt').text(sumDebt);
    $('#sumTotalPayed').text(sumTotalPayed);
}

/*Search*/
function search(q) {
    if (q) {
        $.ajax({
            url: '/shop/order/search-ajax',
            type: 'GET',
            data: {q: q},
            success: function (res) {
                console.log(res);
                $('#search_results').html(res);
                $("#search_results").css({"display": "block"});
            }
        });
    }
}

function searchFocusClick() {
    $('.search-item').on('click', function () {
        $('#search_input').val($(this).data('id'))
        focus_click($('#search_id'), $(this).find('span').text());
    });
}

function focus_click($input, $text) {
    $input.val($text).focus();
    $('#btn_search').click();
}

$(document).ready(function () {
    searchFocusClick();
});
$(document).ajaxSuccess(function () {
    searchFocusClick();
});

$(window).click(function () {
    $("#search_results").css({"display": "none"});
});
//setting
$('.add_setting_item').on('click', function (e) {
    var setting_id = $('.setting_id').text();
    $.ajax({
        url: '/system/setting/create-item',
        data: {setting_id: setting_id},
        type: 'GET',
        success: function (res) {
            $('#add_setting_item .modal-body').html(res);
            $('#add_setting_item').modal();
        }
    });
});
$('.edit_setting_item').on('click', function (e) {
    var setting_id = $(this).data('setting_id');
    var slug = $(this).data('slug');
    $.ajax({
        url: '/system/setting/update-item',
        data: {setting_id: setting_id, slug: slug},
        type: 'GET',
        success: function (res) {
            $('#edit_setting_item .modal-body').html(res);
            $('#edit_setting_item').modal();
        }
    });
});
// left_side
var left_side_attribute = [
    'content_left_side',
    'seo_left_side',
    'setting_left_side',
    'logger_left_side',
    'poll_left_side'
];

$.each(left_side_attribute, function (index, element) {
    attribute_state(element);
});

function attribute_state(attribute) {
    var storage = localStorage.getItem(attribute);
    collapse(attribute, storage);
}

function openClose(attribute) {
    var storage = localStorage.getItem(attribute);
    localStorage.setItem(attribute, flag(storage));
    $('#' + attribute).on('hidden.bs.collapse', function () {
        $('#heading_' + attribute + ' .open-close').html('<i class="fas fa-angle-right"></i>');
    });
    $('#' + attribute).on('show.bs.collapse', function () {
        $('#heading_' + attribute + ' .open-close').html('<i class="fas fa-angle-down"></i>');
    });
}

function flag(flag) {
    if (!flag || flag === 'undefined') {
        return 'open';
    }
    if (flag === 'close') {
        return 'open';
    }
    if (flag === 'open') {
        return 'close';
    }
}

function collapse(attribute, storage) {
    if (storage == 'open') {
        $('#' + attribute).addClass('collapse show');
        $('#heading_' + attribute + ' .open-close').html('<i class="fas fa-angle-down"></i>');
    } else {
        $('#' + attribute).addClass('collapse');
        $('#heading_' + attribute + ' .open-close').html('<i class="fas fa-angle-right"></i>');
    }
}

$('#sendNow').on('click', function (e) {
    if ($('#sendNow').prop('checked')) {
        var date = new Date();
        var dateFormat = date.toISOString().split('T')[0] + ' ' + date.toTimeString().split(' ')[0];
        $('#newsletter-date_departure').val(dateFormat);
    } else {
        $('#newsletter-date_departure').val('');
    }
});
$('#textarea-message').change(function () {
    saveNewsletter();
});
$('#tagsId').change(function () {
    saveNewsletter();
});
$('#sendTelegram').on('click', function (e) {
    saveNewsletter();
});
$('#sendViber').on('click', function (e) {
    saveNewsletter();
});
$('#sendEmail').on('click', function (e) {
    saveNewsletter();
});
$('#sendNow').on('click', function (e) {
    saveNewsletter();
});
$('#customerBlogger').on('click', function (e) {
    saveNewsletter();
});
$('#activeCustomer').on('click', function (e) {
    saveNewsletter();
});
$('#subscribedCustomer').on('click', function (e) {
    saveNewsletter();
});
$('#notCustomerBlogger').on('click', function (e) {
    saveNewsletter();
});

function saveNewsletter() {
    let params = {
        newsletter_id: $('#newsletter_id').text().trim(),
        text: $('#textarea-message').val(),
        tagsId: $("#tagsId").select2('val'),
        sendTelegram: $('#sendTelegram').prop('checked'),
        sendViber: $('#sendViber').prop('checked'),
        sendEmail: $('#sendEmail').prop('checked'),
        sendNow: $('#sendNow').prop('checked'),
        customerBlogger: $('#customerBlogger').prop('checked'),
        activeCustomer: $('#activeCustomer').prop('checked'),
        subscribedCustomer: $('#subscribedCustomer').prop('checked'),
        notCustomerBlogger: $('#notCustomerBlogger').prop('checked'),
        date_departure: $('#newsletter-date_departure').val(),
    }
    $.ajax({
        url: '/customer/newsletter/save-ajax',
        data: {params: JSON.stringify(params)},
        type: 'POST',
        success: function (r) {
            $('#qty-customer').text(r)
        }
    });
}
