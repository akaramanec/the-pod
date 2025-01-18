var count_relations = [];

var checked = $('.add-to-relations', this).prop('checked');
$('.send').on('click', function () {
    if (countChecked()) {
        $(".head_relations").css({"color": "red"});
    } else {
        $(".head_relations").css({"color": "#000000"});
    }
});
$('.head_relations').on('click', function () {
    if (countChecked()) {
        $.ajax({
            url: '/customer/ajax/relations',
            data: {relations: JSON.stringify(count_relations)},
            type: 'GET',
            success: function (res) {
                if (res) {
                    document.location.reload(true);
                    // $.pjax.reload({container:"#pjax_customers"});
                }
            }
        });
    }
});

function countChecked() {
    var x = 0;
    count_relations = [];
    $.each($('.add-to-relations:checked'), function () {
        count_relations[x] = $(this).val();
        x++;
    });
    if (count_relations.length >= 2) {
        return true;
    } else {
        return false;
    }
};

$('.del-relations').on('click', function () {
    var id = $(this).data('id');
    $.ajax({
        url: '/customer/ajax/del-relations',
        data: {id: id},
        type: 'GET',
        success: function (res) {
            if (res) {
                document.location.reload(true);
            }
        }
    });
});
$('.view_customer').on('click', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url: '/customer/customer/view',
        data: {id: id},
        type: 'GET',
        success: function (res) {
            $('#view_customer .modal-body').html(res);
            $('#view_customer').modal();
        }
    });
});
