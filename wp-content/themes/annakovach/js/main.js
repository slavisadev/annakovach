/**
 * Avoid `console` errors in browsers that lack a console.
 */
(function () {
    var method;
    var noop = function () {
    };
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

(function ($) {

    var $ = jQuery;

    var ajax_url = globalWpJavascriptObject.ajax_url;

    window.connector = {
        getData: function (methodType, route, dataType, dataBlock, callbackFunction, context) {
            if (route === "")
                route = ajax_url,
                    $.ajax({
                        async: true,
                        type: methodType,
                        dataType: dataType,
                        url: route,
                        data: dataBlock
                    }).then(function (data) {
                        if (callbackFunction !== null && context !== null) {
                            callbackFunction(context, data);
                        }
                        return data;
                    });
        }
    };
})(jQuery);