$(function (){
    let order_id = $('#order_id').text();
    window.setInterval(function(){
        $.post(
            '/shop/order/check-status-ajax',
            {order_id: order_id},
            function (data) {
                data = $.parseJSON(data);
                console.log(data.status);
                if (data.status != false
                    && data.status != undefined
                    && data.status != $('#order-status').val()) {
                    $('#order-status').val(data.status);
                    location.reload();
                }
            }
        );
    }, 15000);
});