// $.readyFn.execute();

/**
 * Replace jQuery's $.fn.ready() function with a mod exec
 *
 * Sites that make heavy use of the $(document).ready function
 * are generally incompatable with asynchrounous content. The
 * the $.fn.ready function only runs once. This script replaces
 * the ready function with a module execution controller that
 * let's us register functions and execute all of the functions
 * as we need them. This is useful after HTML gets injected on the
 * page and we want to rebind functionally to the new content.
 *
 * @author  Miguel Ángel Pérez   reachme@miguel-perez.com
 * @note    Should be placed directly after jQuery on the page
 *
 */
;(function($){
    var  $doc = $(document);

    /** create mod exec controller */
    $.readyFn = {
        list: [],
        register: function(fn) {
            $.readyFn.list.push(fn);
        },
        execute: function() {
            for (var i = 0; i < $.readyFn.list.length; i++) {
                try {
                    $.readyFn.list[i].apply(document, [$]);
                }
                catch (e) {
                    throw e;
                }
            };
        }
    };

    /** run all functions */
    $doc.ready(function(){
        $.readyFn.execute();
    });

    /** register function */
    $.fn.ready = function(fn) {
        $.readyFn.register(fn);
    };

})(jQuery);


// To re-run the ready functions just use `$.readyFn.execute();`
// after the new HTML has been injected into the page.
