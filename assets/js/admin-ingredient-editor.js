(function ($) {
    "use strict";
    function allergen_check() {
        if ($('#_is_allergen').is(':checked')) {
            $('#_allergen_warning').prop('disabled', false).parents(':first').show();
        } else {
            $('#_allergen_warning').prop('disabled', true).parents(':first').hide();
        }
    }

    $(document)
        .on('click', '#_is_allergen', function () {
            allergen_check();
        })
        .ready(function () {
            allergen_check();
            $(".tips").tooltip({
                track: true,
                items: 'span[data-tip]',
                content: function () {
                    return $(this).attr('data-tip');
                },
                position: { my: "left+10 bottom-15", at: "left bottom", collision: "flipfit" },
                tooltipClass: 'rcpm-tooltip'
            });
        });

}(jQuery));