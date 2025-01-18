var Analytic = (() => {
    return {
        index: index,
    };

    function index() {

        $(document).delegate("#date_from, #date_to", 'change', function () {
            $("#dateFrom").val($('#date_from').val());
            $('#dateTo').val($('#date_to').val())
            $(".calendar form").submit();
        });
    }
})();

Analytic.index();