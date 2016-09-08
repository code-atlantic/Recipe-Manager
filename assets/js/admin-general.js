var rcpm_selectors;
(function ($) {
    'use strict';

    function Selector_Cache() {
        var elementCache = {};

        var get_from_cache = function (selector, $ctxt, reset) {

            if ('boolean' === typeof $ctxt) {
                reset = $ctxt;
                $ctxt = false;
            }
            var cacheKey = $ctxt ? $ctxt.selector + ' ' + selector : selector;

            if (undefined === elementCache[cacheKey] || reset) {
                elementCache[cacheKey] = $ctxt ? $ctxt.find(selector) : jQuery(selector);
            }

            return elementCache[cacheKey];
        };

        get_from_cache.elementCache = elementCache;
        return get_from_cache;
    }

    rcpm_selectors = new Selector_Cache();

    rcpm_selectors(document)
        .ready(function () {
            var color_picker = rcpm_selectors('.rcpm-color-picker');

            if (color_picker.length) {
                color_picker.wpColorPicker({
                    change: function (event, ui) {
                        rcpm_selectors('#' + event.target.id).trigger('colorchange', ui);
                    }
                });
            }
        });

}(jQuery));