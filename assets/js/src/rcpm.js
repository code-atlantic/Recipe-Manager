(function ($, document) {

    $(document).on('click', '.recipe-ingredient, .recipe-step', function () {
        $(this).toggleClass('checked');
    }).css({cursor:"pointer"});

}(jQuery, document));