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
/*Search*/
function search(q) {
    if (q) {
        $.ajax({
            url: '/home/search-ajax',
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
    'customer_left_side',
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

