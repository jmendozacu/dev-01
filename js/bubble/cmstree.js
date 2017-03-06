function previewAction(formId, formObj, url) {
    var formElem = $(formId);
    formElem.writeAttribute('target', '_blank');
    formObj.submit(url);
    formElem.writeAttribute('target', '');
}

function showPopupDuplicate() {
    $('popup-duplicate').show().setStyle({
        'marginTop': -$('popup-duplicate').getDimensions().height / 2 + 'px'
    });
    $('popup-window-mask').setStyle({
        height: $('html-body').getHeight() + 'px'
    }).show();
}

function hidePopupDuplicate() {
    $('popup-window-mask').hide();
    $('popup-duplicate').hide();
}