function numberOfViews(book) {
    return book.turn('pages') / 2 + 1;
}
function getViewNumber(book, page) {
    return parseInt((page || book.turn('page')) / 2 + 1, 10);
}
function setPreview(a) {
    var d = 49 < jQuery("#slider").slider("option", "max") ? 55 : 45;
    b = 25;
    c = jQuery(_thumbPreview.children(":first"));

    _thumbPreview.addClass("no-transition").css({width: d + 15, height: b + 15, top: -b - 30, left: (jQuery(jQuery("#slider").children(":first")).width() - d - 15) / 2});
    c.css({width: d, height: b});

    echopage = 1 == a ? a : a == jQuery("#slider").slider("option", "max") && 0 == jQuery(".flipbook").turn("pages") % 2 ? 2 * a - 1 : 2 * a - 2 + "-" + (2 * a - 1), c.html("<b>" + echopage + "</b>")
}
jQuery(document).ready(function () {
    clickElement(jQuery(".tbicon"), function (a) {
        navigation(jQuery.trim(jQuery(this).attr("class").replace(/\b([a-z-]*hover|tbicon)\b/g, "")))
    });

    jQuery('.next-button').bind('click', function () {
        jQuery('.flipbook').turn('next');
    });
    jQuery('.previous-button').bind('click', function () {
        jQuery('.flipbook').turn('previous');
    });
});
function clickElement(a, b) {
    jQuery.isTouch ? a.bind(jQuery.mouseEvents.up, b) : a.click(b)
}
function navigation(a) {
    switch (a) {
        case "share-facebook":
            window.open("https://www.facebook.com/sharer.php?u=" + encodeURIComponent(rawPublicationLink) + "&t=" + encodeURIComponent(rawPublicationTitle));
            break;
        case "share-twitter":
            window.open("https://twitter.com/intent/tweet?original_referer=" + encodeURIComponent(rawPublicationLink) + "&url=" + encodeURIComponent(rawPublicationLink) + "&text=" + encodeURIComponent(rawPublicationTitle));
            break;
        case "share-plus":
            window.open("https://plusone.google.com/_/+1/confirm?url=" + encodeURIComponent(rawPublicationLink))
    }
}


