Event.observe(window, 'message', function(e){
    if (e.data.action == 'setHeight')
    {
        var height = e.data.height;
        $('marginframe_store').setStyle({height: height+'px'});
    }
});
