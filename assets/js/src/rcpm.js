var rcpm_selectors;
(function ($, document) {
    'use strict';

    function Selector_Cache() {
        var elementCache = {};

        var get_from_cache = function( selector, $ctxt, reset ) {

            if ( 'boolean' === typeof $ctxt ) {
                reset = $ctxt;
                $ctxt = false;
            }
            var cacheKey = $ctxt ? $ctxt.selector + ' ' + selector : selector;

            if ( undefined === elementCache[ cacheKey ] || reset ) {
                elementCache[ cacheKey ] = $ctxt ? $ctxt.find( selector ) : jQuery( selector );
            }

            return elementCache[ cacheKey ];
        };

        get_from_cache.elementCache = elementCache;
        return get_from_cache;
    }

    rcpm_selectors = new Selector_Cache();

}(jQuery, document));