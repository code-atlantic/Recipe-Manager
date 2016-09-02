(function ($) {
    $(document).ready(function () {
        var color_picker = $('.rcpm-color-picker');

        if( color_picker.length ) {
            color_picker.wpColorPicker();
        }
    });

}(jQuery));