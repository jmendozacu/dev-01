amPinbar = Class.create({
    initialize: function (params, indexUrl, deleteUrl, renameUrl, tabUrl)
    {
        this.deleteUrl = deleteUrl;
        this.renameUrl = renameUrl;
        Event.observe(document, 'dom:loaded', function()
        {
            $('anchor-content').down('.content-header').down('h3').appendChild($('ampinbar-pin'));
        });
        Event.observe($('ampinbar-pin'), 'click', function()
        {
            this.addPin(indexUrl, params, tabUrl);
        }.bind(this));

        $('ampinbar-bar').select('.delete').each(function(btnDel) {
            this.deletePin(btnDel, deleteUrl);
        }.bind(this));

        $('ampinbar-bar').select('.rename').each(function(btnName) {
            this.renamePin(btnName);
        }.bind(this));

        $('ampinbar-bar').select('.title-input').each(function(input) {
            this.saveNewTitle(input, renameUrl);
        }.bind(this));
    },

    addPin: function(url, params, tabUrl)
    {
        if (!$('ampinbar-pin').hasClassName('attached')) {
            new Ajax.Request(url, {
                method     : 'post',
                parameters : {common : params, session: $('ampinbar_session_params').value},
                onComplete : function(response) {
                    $('ampinbar-pin').addClassName('attached');
                    $('ampinbar-bar').show();
                    var divTab = $('ampinbar-pin').down('.tab').cloneNode(true);
                    var responseTxt = response.responseText.evalJSON();
                    var titleText = responseTxt.title;
                    divTab.id = responseTxt.pin_id;
                    divTab.down('.title-span').value = titleText;
                    divTab.down('.title-input').value = titleText;
                    divTab.down('a').href = tabUrl + 'pinId/' + responseTxt.pin_id;
                    divTab.down('a').title = titleText;
                    $('ampinbar-bar').appendChild(divTab);

                    this.deletePin(divTab.down('.delete'), this.deleteUrl);
                    this.renamePin(divTab.down('.rename'));
                    this.saveNewTitle(divTab.down('.title-input'), this.renameUrl);

                    divTab.setOpacity(0);
                    divTab.show();

                    new Effect.Opacity(divTab, {from: 0.0,
                        to: 1.0,
                        duration: 1.0
                    });
                }.bind(this)
            });
        }
    },

    deletePin: function(btnDel, url)
    {
        Event.observe(btnDel, 'click', function() {
            var tab = btnDel.up('.tab');
            var pinId = tab.id;
            new Ajax.Request(url, {
                method     : 'post',
                parameters : {pinId : pinId},
                onComplete : function(response) {
                    tab.remove();
                    if ($('ampinbar-bar').select('.tab').length == 0) {$('ampinbar-bar').hide();}
                    var responseTxt = response.responseText.evalJSON();
                    if (window.location.href.indexOf(responseTxt.url) !== false && responseTxt.delete) {
                        $('ampinbar-pin').removeClassName('attached');
                    }
                }
            });
        });
    },

    renamePin: function(btnName)
    {
        Event.observe(btnName, 'click', function() {
            var tab = btnName.up('.tab');
            tab.down('.title-input').show();
            tab.down('.title-span').hide();
            tab.down('a').hide();
            tab.down('.title-input').focus();
        });
    },
    saveNewTitle: function(input, renameUrl) {
        Event.observe(input, 'blur', function() {
            var tab = input.up('.tab');
            var pinId = tab.id;
            new Ajax.Request(renameUrl, {
                method     : 'post',
                parameters : {pinId : pinId, title: input.value},
                onComplete : function(response) {
                    tab.down('.title-input').hide();
                    tab.down('.title-span').value = input.value;
                    tab.down('.title-span').show();
                    tab.down('a').show();
                }
            });
        });
    }
});