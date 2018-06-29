(function ($) {

    'use strict';

    // Left menu collapse
    $('.button-menu-mobile').on('click', function (event) {
        event.preventDefault();
        $("body").toggleClass("nav-collapse");
    });

    $(".lang-switch").each(function() {
        var e = $(this),
            a = (e.find("#lang-items"), e.find("#lang-current"));
        a.on("mouseover", function() {
            e.addClass("lang-switch_expand")
        }), e.on("mouseleave", function() {
            e.removeClass("lang-switch_expand")
        }), a.on("touchstart", function() {
            e.toggleClass("lang-switch_expand")
        })
    })
})(jQuery);