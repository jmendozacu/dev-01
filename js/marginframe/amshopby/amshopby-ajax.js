var amshopby_working  = false;
var amshopby_blocks   = {};

function amshopby_ajax_fallback_mode() {
    var myNav = navigator.userAgent.toLowerCase();
    var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
    jQuery(document).ready(function($) {
        jQuery('img.lazy').jail({
            event: 'load+scroll',
            placeholder : SKIN_URL + "images/mgt_lazy_image_loader/loader.gif",
        });
    });
    (function(a,c){var b=c(jQuery),d=typeof define==="function"&&define.amd;if(d){define("jail",["jquery"],b);}else{(this.jQuery||this.$||this)[a]=b;}}("jail",function(f){var b=f(window),d={timeout:1,effect:false,speed:400,triggerElement:null,offset:0,event:"load",callback:null,callbackAfterEachImage:null,placeholder:false,loadHiddenImages:false},k=[],g=false;f.jail=function(o,n){var o=o||{},n=f.extend({},d,n);f.jail.prototype.init(o,n);if(/^(load|scroll)/.test(n.event)){f.jail.prototype.later.call(o,n);}else{f.jail.prototype.onEvent.call(o,n);}};f.jail.prototype.init=function(o,n){o.data("triggerEl",(n.triggerElement)?f(n.triggerElement):b);if(!!n.placeholder){o.each(function(){f(this).attr("src",n.placeholder);});}};f.jail.prototype.onEvent=function(o){var n=this;if(!!o.triggerElement){i(o,n);}else{n.bind(o.event,{options:o,images:n},function(s){var r=f(this),q=s.data.options,p=s.data.images;k=f.extend({},p);a(q,r);f(s.currentTarget).unbind(s.type);});}};f.jail.prototype.later=function(o){var n=this;setTimeout(function(){k=f.extend({},n);n.each(function(){c(o,this,n);});o.event="scroll";i(o,n);},o.timeout);};function i(o,n){if(!!n){var p=n.data("triggerEl");}if(!!p&&typeof p.bind==="function"){p.bind(o.event,{options:o,images:n},m);b.resize({options:o,images:n},m);}}function j(n){var o=0;if(n.length>0){while(true){if(o===n.length){break;}else{if(n[o].getAttribute("data-src")){o++;}else{n.splice(o,1);}}}}}function m(p){var n=p.data.images,o=p.data.options;n.data("poller",setTimeout(function(){k=f.extend({},n);j(k);f(k).each(function(){if(this===window){return;}c(o,this,k);});if(l(k)){f(p.currentTarget).unbind(p.type);return;}else{if(o.event!=="scroll"){var q=(/scroll/i.test(o.event))?n.data("triggerEl"):b;o.event="scroll";n.data("triggerEl",q);i(o,f(k));}}},o.timeout));}function l(n){var o=true;f(n).each(function(){if(!!f(this).attr("data-src")){o=false;}});return o;}function c(q,s,o){var r=f(s),p=(/scroll/i.test(q.event))?o.data("triggerEl"):b,n=true;if(!q.loadHiddenImages){n=h(r,p,q)&&r.is(":visible");}if(n&&e(p,r,q.offset)){a(q,r);}}function e(u,n,s){var q=u[0]===window,y=(q?{top:0,left:0}:u.offset()),r=y.top+(q?u.scrollTop():0),t=y.left+(q?u.scrollLeft():0),p=t+u.width(),v=r+u.height(),x=n.offset(),w=n.width(),o=n.height();return(r-s)<=(x.top+o)&&(v+s)>=x.top&&(t-s)<=(x.left+w)&&(p+s)>=x.left;}function a(n,o){o.hide();o.attr("src",o.attr("data-src"));o.removeAttr("data-src");if(n.effect){if(n.speed){o[n.effect](n.speed);}else{o[n.effect]();}o.css("opacity",1);o.show();}else{o.show();}j(k);if(!!n.callbackAfterEachImage){n.callbackAfterEachImage.call(this,o,n);}if(l(k)&&!!n.callback&&!g){n.callback.call(f.jail,n);g=true;}}function h(q,o,p){var r=q.parent(),n=true;while(r.get(0).nodeName.toUpperCase()!=="BODY"){if(r.css("overflow")==="hidden"){if(!e(r,q,p.offset)){n=false;break;}}else{if(r.css("overflow")==="scroll"){if(!e(r,q,p.offset)){n=false;f(k).data("triggerEl",r);p.event="scroll";i(p,f(k));break;}}}if(r.css("visibility")==="hidden"||q.css("visibility")==="hidden"){n=false;break;}if(o!==b&&r===o){break;}r=r.parent();}return n;}f.fn.jail=function(n){new f.jail(this,n);k=[];return this;};return f.jail;}));

    return isIE == 7 || isIE == 8;
}

function amshopby_ajax_init(){
    if (amshopby_ajax_fallback_mode()) {
        return;
    }

    $$('div.block-layered-nav a', amshopby_toolbar_selector + ' a').
        each(function(e){
            var p = e.up();
            if ((p.hasClassName('amshopby-cat') && !p.hasClassName('amshopby-cat-multi')) || p.hasClassName('amshopby-clearer')){
                return;
            }

            e.onclick = function(){
                if (this.hasClassName('checked')) {
                    this.removeClassName('checked');
                } else {
                    this.addClassName('checked');
                }

                var s = this.href;
                if (s.indexOf('#') > 0){
                    s = s.substring(0, s.indexOf('#'))
                }
                amshopby_ajax_push_state(s);
                amshopby_ajax_request(s);
                return false;
            };
        });

    $$('div.block-layered-nav select.amshopby-ajax-select', amshopby_toolbar_selector + ' select').
        each(function(e){
            e.onchange = 'return false';
            Event.observe(e, 'change', function(e){
                amshopby_ajax_push_state(this.value);
                amshopby_ajax_request(this.value);
                Event.stop(e);
            });
        });
}

function amshopby_get_created_container()
{
    var elements = document.getElementsByClassName('amshopby-page-container');
    return (elements.length > 0) ? elements[0] : null;
}

function amshopby_get_container()
{
    var createdElement = amshopby_get_created_container();
    if (!createdElement) {
        var container_element = null;

        var elements = $$('div.category-products');
        if (elements.length == 0) {
            container_element = amshopby_get_empty_container();
        } else {
            container_element = elements[0];
        }

        if (!container_element) {
            console.debug('Please add the <div class="amshopby-page-container"> to the list template as per installtion guide. Enable template hints to find the right file if needed.');
        }

        container_element.wrap('div', { 'class': 'amshopby-page-container', 'id' : 'amshopby-page-container' });

        createdElement = amshopby_get_created_container();

        $(createdElement).insert({ bottom : '<div style="display:none" class="amshopby-overlay"><div></div></div>'});
    }
    return createdElement;
}

function amshopby_get_empty_container()
{
    var notes = document.getElementsByClassName('note-msg');
    if (notes.length == 1) {
        return notes[0];
    }
}

function amshopby_ajax_push_state(url) {
    if (typeof window.history.pushState === 'function') {
        window.history.pushState({url: url}, '', url);
    }
}

function amshopby_ajax_request(url){
    /*
     * Clean hash param to avoid scrolling page down
     */

    if (typeof amscroll_object != 'undefined') {
        amscroll_object.setHashParam('page', null);
        amscroll_object.setHashParam('top', null);

        amscroll_params.url = url;
        amscroll_object.setUrl(url);
    }

    var block = amshopby_get_container();

    if (block && amshopby_scroll_to_products) {
        block.scrollTo();
    }

    amshopby_working = true;

    $$('div.amshopby-overlay').each(function(e){
        e.show();
    });

    var request = new Ajax.Request(url,{
            method: 'get',
            parameters:{'is_ajax':1},
            onSuccess: function(response){
                try {
                    var data = response.responseText;
                    if(!data.isJSON()){
                        throw new EventException('Cannot convert response data to JSON');
                    }

                    data = data.evalJSON();
                    if (!data.page || !data.blocks){
                        throw new EventException('Invalid data structure in response');
                    }
                    amshopby_ajax_update(data);
                    rerun_some_init_scripts();
                } catch (e) {
                    console.log(e.message);
                    setLocation(url);
                }
                amshopby_working = false;
                amshopby_skip_hash_change = false;
            },
            onFailure: function(){
                amshopby_working = false;
                setLocation(url);
            }
        }
    );
}

function amshopby_get_first_descendant(element) {

    var targetElement = element.firstChild;
    if(typeof element.firstDescendant != "undefined") {
        targetElement = element.firstDescendant();
    }
    return targetElement;
}

function amshopby_ajax_update(data){

    //update category (we need all category as some filters changes description)
    var tmp = document.createElement('div');
    tmp.innerHTML = data.page;

    var title = data.title;
    if (title) {
        $$('title')[0].update(title);
    }

    var block = amshopby_get_container();
    if (block) {
        var targetElement = amshopby_get_first_descendant(tmp);
        /* move top filter before container */
        var amshopbyFiltersTop = block.select('.amshopby-filters-top').first();
        if(amshopbyFiltersTop){
            var parent = block.parentNode;
            parent.insertBefore(amshopbyFiltersTop, block);
        }
        var colLeftFirst = block.select('.col-left-first').first();
        if(colLeftFirst){
            var parent = block.parentNode;
            parent.insertBefore(colLeftFirst, block);
        }

        /*
         * If returned element is not HTML tag
         */
        if (targetElement == null) {
            tmp.innerHTML = '<p class="note-msg">' + data.page + '</p>';
            targetElement = amshopby_get_first_descendant(tmp);
        }
        block.parentNode.replaceChild(targetElement, block);
        if (typeof AmConfigurableData != 'undefined') {
            try{
                targetElement.innerHTML.evalScripts();
            }
            catch(ex){
                console.debug(ex);
            }
        }
    }


    var blocks = data.blocks;
    for (var id in blocks){

        var html   = blocks[id];
        if (html){
            tmp.innerHTML = html;
        }

        block = $$('div.'+id)[0];
        if (html){
            if (!block){
                block = amshopby_blocks[id]; // the block WAS in the structure a few requests ago
                amshopby_blocks[id] = null;
            }
            if (block){
                var targetElement = amshopby_get_first_descendant(tmp);
                block.parentNode.replaceChild(targetElement, block);
            }
        }
        else { // no filters returned, need to remove
            if (block){
                var empty = document.createTextNode('');
                amshopby_blocks[id] = empty; // remember the block in the DOM structure
                block.parentNode.replaceChild(empty, block);
            }
        }
    }

    if (typeof amshopby_jquery_init !== 'undefined') {
        amshopby_jquery_init();
    }
    amshopby_start();
    amshopby_ajax_init();
    amshopby_move_top_filter();

    try {
        var categoryProducts = $$('.category-products, .catblocks').first();
        var colLeftFirst = amshopby_get_container().parentNode.select('.col-left-first').first();
        if(colLeftFirst && categoryProducts){
            var parent = categoryProducts.parentNode;
            parent.insertBefore(colLeftFirst, categoryProducts);
        }
        amshopby_external();
    } catch (e) {
        console.debug(e);
    }
}

document.observe("dom:loaded", function(event) {
    amshopby_ajax_init();

    if (typeof window.history.replaceState === "function") {
        window.history.replaceState({url: document.URL}, document.title);

        setTimeout(function() {
            /*
              Timeout is a workaround for iPhone
              Reproduce scenario is following:
              1. Open category
              2. Use pagination
              3. Click on product
              4. Press "Back"
              Result: Ajax loads the same content right after regular page load
             */
            window.onpopstate = function(e){
                if(e.state){
                    amshopby_ajax_request(e.state.url);
                }
            };
        }, 0)
    }

});

var amshopby_toolbar_selector = 'div.toolbar';
var amshopby_scroll_to_products = false;

function amshopby_external(){
    //add here all external scripts for page reloading
    // like igImgPreviewInit(); 
    if (typeof amscroll_object != 'undefined') {
        amscroll_object.init(amscroll_params);
        amscroll_object.bindClick();
    }

    if (typeof amshopby_demo != 'undefined') {
        amshopby_demo();
    }
    if (typeof AmAjaxObj != 'undefined') {
        AmAjaxShoppCartLoad('button.btn-cart');
    }

    //amfinder
    var amfinderScript = document.getElementById('amfinder_script');
    if (amfinderScript) {
        eval(amfinderScript.innerHTML);
    }

    if (typeof ProductMediaManager != 'undefined') {
        amshopby_external_rwd();
    }

    if (typeof amlabel_init == 'function') {
        amlabel_init();
    }

    /**
     * Third-party themes
     */

    if (typeof jQuery != 'undefined' && typeof calculateMenuItemsInRow == 'function') {
        amshopby_external_megatron();
    }

    //Ultimo fortis themes
    if (typeof setGridItemsEqualHeight != 'undefined') {
        setTimeout('setGridItemsEqualHeight(jQuery)', 100);
        setTimeout('setGridItemsEqualHeight(jQuery)', 300);
        setTimeout('setGridItemsEqualHeight(jQuery)', 800);
        setTimeout('setGridItemsEqualHeight(jQuery)', 2000);
    }

    // venedor/default
    if (typeof products_grid_resize == 'function') {
        products_grid_resize();
    }

    if (typeof jQuery != 'undefined' && typeof jQuery.resize == 'function') {
        jQuery.resize();
    }

    // Magento 1.9.1 Configurable swatches
    if (typeof ConfigurableSwatchesList != 'undefined') {
        ConfigurableSwatchesList.init();
    }
		
		ajaxWishlistInit();
}

function amshopby_external_rwd() {
    if(typeof bp != 'undefined') {
        enquire.register('(max-width: ' + bp.medium + 'px)', {
            setup: function () {
            },
            match: function () {
                $j('.block-subtitle--filter').toggleSingle({destruct: true});
                $j('.block-subtitle--filter').toggleSingle();
            },
            unmatch: function () {
                $j('.block-subtitle--filter').toggleSingle({destruct: true});
            }
        });
    }

    /*align Product Grid Actions */
    if ($j('.products-grid').length) {
        var alignProductGridActions = function () {
            // Loop through each product grid on the page
            $j('.products-grid').each(function(){
                var gridRows = []; // This will store an array per row
                var tempRow = [];
                productGridElements = $j(this).children('li');
                productGridElements.each(function (index) {
                    // The JS ought to be agnostic of the specific CSS breakpoints, so we are dynamically checking to find
                    // each row by grouping all cells (eg, li elements) up until we find an element that is cleared.
                    // We are ignoring the first cell since it will always be cleared.
                    if ($j(this).css('clear') != 'none' && index != 0) {
                        gridRows.push(tempRow); // Add the previous set of rows to the main array
                        tempRow = []; // Reset the array since we're on a new row
                    }
                    tempRow.push(this);

                    // The last row will not contain any cells that clear that row, so we check to see if this is the last cell
                    // in the grid, and if so, we add its row to the array
                    if (productGridElements.length == index + 1) {
                        gridRows.push(tempRow);
                    }
                });

                $j.each(gridRows, function () {
                    var tallestProductInfo = 0;
                    $j.each(this, function () {
                        // Since this function is called every time the page is resized, we need to remove the min-height
                        // and bottom-padding so each cell can return to its natural size before being measured.
                        $j(this).find('.product-info').css({
                            'min-height': '',
                            'padding-bottom': ''
                        });

                        // We are checking the height of .product-info (rather than the entire li), because the images
                        // will not be loaded when this JS is run.
                        var productInfoHeight = $j(this).find('.product-info').height();
                        // Space above .actions element
                        var actionSpacing = 10;
                        // The height of the absolutely positioned .actions element
                        var actionHeight = $j(this).find('.product-info .actions').height();

                        // Add height of two elements. This is necessary since .actions is absolutely positioned and won't
                        // be included in the height of .product-info
                        var totalHeight = productInfoHeight + actionSpacing + actionHeight;
                        if (totalHeight > tallestProductInfo) {
                            tallestProductInfo = totalHeight;
                        }

                        // Set the bottom-padding to accommodate the height of the .actions element. Note: if .actions
                        // elements are of varying heights, they will not be aligned.
                        $j(this).find('.product-info').css('padding-bottom', actionHeight + 'px');
                    });
                    // Set the height of all .product-info elements in a row to the tallest height
                    $j.each(this, function () {
                        $j(this).find('.product-info').css('min-height', tallestProductInfo);
                    });
                });
            });
        }
        alignProductGridActions();
    }


    jQuery('.toggle-content').each(function () {
        var wrapper = jQuery(this);

        var hasTabs = wrapper.hasClass('tabs');
        var startOpen = wrapper.hasClass('open');

        var dl = wrapper.children('dl:first');
        var dts = dl.children('dt');
        var panes = dl.children('dd');
        var groups = new Array(dts, panes);

        //Create a ul for tabs if necessary.
        if (hasTabs) {
            var ul = jQuery('<ul class="toggle-tabs"></ul>');
            dts.each(function () {
                var dt = jQuery(this);
                var li = jQuery('<li></li>');
                li.html(dt.html());
                ul.append(li);
            });
            ul.insertBefore(dl);
            var lis = ul.children();
            groups.push(lis);
        }

        //Add "last" classes.
        var i;
        for (i = 0; i < groups.length; i++) {
            groups[i].filter(':last').addClass('last');
        }

        function toggleClasses(clickedItem, group) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }

        //Toggle on tab (dt) click.
        dts.on('click', function (e) {
            //They clicked the current dt to close it. Restore the wrapper to unclicked state.
            if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                wrapper.removeClass('accordion-open');
            } else {
                //They're clicking something new. Reflect the explicit user interaction.
                wrapper.addClass('accordion-open');
            }
            toggleClasses(jQuery(this), dts);
        });

        //Toggle on tab (li) click.
        if (hasTabs) {
            lis.on('click', function (e) {
                toggleClasses(jQuery(this), lis);
            });
            //Open the first tab.
            lis.eq(0).trigger('click');
        }

        //Open the first accordion if desired.
        if (startOpen) {
            dts.eq(0).trigger('click');
        }
    });
}

function rerun_some_init_scripts() {
    var widthWindow = jQuery( window ).width();
		jQuery('.block-layered-nav #narrow-by-list dt').click(function(){
			if (!jQuery(this).hasClass('current')){
				jQuery(this).next().slideToggle(300);
				jQuery(this).next().addClass('current');
				jQuery(this).addClass('current');
			}
			else{
				jQuery(this).next().slideToggle(300);
				jQuery(this).next().removeClass('current');
				jQuery(this).removeClass('current');
			}
		});
		jQuery(".flip-container > .flipper > .front" ).hover(function() {
				jQuery(this).animate({
					'opacity' : 0
				},150);
			}, function() {
				jQuery(this).animate({
					'opacity' : 1
				},150);
			}
		);
    if(widthWindow <768){ 
      /* box promo */
      jQuery(".category-banners .div-banner").mCustomScrollbar({
        axis:"x",
        advanced:{autoExpandHorizontalScroll:true}
      });
      /* move sort-by-mobile on box filter */
      var sortByHtml = jQuery(".toolbar .sort-by-mobile").html(); 
      jQuery(".col-left-first .block-layered-nav .block-content").append(sortByHtml);
      /* toggle layered-nav */
      jQuery('.toggle-box-filter.toggle-nav').click(function(){
        jQuery('.box-filter-content').hide();
        jQuery('.toggle-box-filter').removeClass('active');
        if(!jQuery(this).hasClass('active')){
          jQuery(this).addClass('active');
          jQuery('#narrow-by-list').show();
        }else{
          jQuery(this).removeClass('active');
          jQuery('#narrow-by-list').hide();
        }
        if(!jQuery('.block-layered-nav .layered-nav-content').hasClass('active')){
          jQuery('.block-layered-nav .layered-nav-content').addClass('active');
        }
      });
      /*  and sortby */
      jQuery('.toggle-box-filter.toggle-sort-by').click(function(){
        jQuery('.box-filter-content').hide();
        jQuery('.toggle-box-filter').removeClass('active');
        if(!jQuery(this).hasClass('active')){
          jQuery(this).addClass('active');
          jQuery('.sort-by-content').show();
        }else{
          jQuery(this).removeClass('active');
          jQuery('.sort-by-content').hide();
        }
        if(!jQuery('.block-layered-nav .layered-nav-content').hasClass('active')){
          jQuery('.block-layered-nav .layered-nav-content').addClass('active');
        }
      });
      /* close-filter */
      jQuery('#close-filter').click(function(){
        jQuery('.block-layered-nav .layered-nav-content, .toggle-box-filter').removeClass('active');
        jQuery('.box-filter-content').hide();
      });
    }
}

function amshopby_external_megatron() {
    var windowWidth = window.innerWidth || document.documentElement.clientWidth;
    var animate = jQuery(".notouch .animate");
    var animateDelay = jQuery(".notouch .animate-delay-outer");
    var animateDelayItem = jQuery(".notouch .animate-delay");
    if (windowWidth > 767) {
        animate.bind("inview", function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) jQuery(this).addClass("animated")
        });
        animateDelay.bind("inview", function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) {
                var j = -1;
                var $this = jQuery(this).find(".animate-delay");
                $this.each(function () {
                    var $this = jQuery(this);
                    j++;
                    setTimeout(function () {
                        $this.addClass("animated")
                    }, 200 * j)
                });
                jQuery(this).addClass("animated")
            }
        })
    } else {
        animate.each(function () {
            jQuery(this).removeClass("animate")
        });
        animateDelayItem.each(function () {
            jQuery(this).removeClass("animate-delay")
        })
    }
}

