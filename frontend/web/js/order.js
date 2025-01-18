$('#branch_ref').prop('disabled', true);
function searchCity(q) {
    let search = q.trim();
    if (q) {
        $.ajax({
            url: '/search-np/search-city',
            data: {q: q},
            type: 'GET',
            success: function (res) {
                if (res) {
                    $('#branch_ref').prop('disabled', true);
                    $('#search_result_additionally_list').html(res);
                    $("#search_result_additionally_list").css({"display": "block"});
                }
            },
        });
    }
}

function searchCityClickData() {
    $('.addresses-item').on('click', function () {
        let item_city = $(this).data('item_city');
        let text = $(this).text().trim();
        $('#city').val(text);
        $('#branch_ref').val('');
        $('#branch_ref').empty();
        $(".search_result_additionally_list").css({"display": "none"});
        $.ajax({
            url: '/search-np/warehouses-data',
            data: {item_city: JSON.stringify(item_city)},
            type: 'POST',
            success: function (data) {
                $('#branch_ref').select2({
                    data: data
                });
                $('#branch_ref').prop('disabled', false);
            },
        });
    });
}
function closeListSearch() {
    $(window).click(function () {
        $("#search_result_additionally_list").css({"display": "none"});
    });
};
$(document).ready(function () {
    searchCityClickData();
    $(document).on('change', '#branch_ref', function () {
        let branch_text = $('#select2-branch_ref-container').text();
        let branch_ref = $('#branch_ref').val();
        $('#branch').val(branch_text);
        $.ajax({
            url: '/search-np/branch-save',
            data: {branch_ref: branch_ref},
            type: 'POST'
        });
    });
});
$(document).ajaxSuccess(function () {
    searchCityClickData();
    closeListSearch();
});

$('.delivery-item:first').prop('checked', true);
$('.delivery-np').css({"display": "block"});
let deliveryAllSlug = JSON.parse($('#deliveryAllSlug').text());
$('.delivery-item').on('click', function (e) {
    $('.delivery-item-form').css({"display": "none"});
    let val = $(this).val();
    $.each(deliveryAllSlug, function (index, value) {
        if (value == val) {
            return $('.' + val).css({"display": "block"});
        }
    });
});
$('#order_form').on('beforeSubmit', function (event) {
    if (!$(this).find('.has-error').length) {
        $(this).find('button[type=submit]').prop('disabled', true);
    }
});

