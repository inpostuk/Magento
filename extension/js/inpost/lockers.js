(function ($) {

    $(document).ready(function () {
        $('input[name=shipping_method]').on('change', function () {
            if ($(this).val().indexOf('inpost_lockers') >= 0) {
                $.ajax({
                    url: "/inpost/lockers/getwidget/",
                    success: function (response) {
                        response = JSON.parse(response);
                        $('.lockers').remove();
                        $('.wrapper').append(response['html']);
                    }
                });
            }
        });
    });

})(jQuery);