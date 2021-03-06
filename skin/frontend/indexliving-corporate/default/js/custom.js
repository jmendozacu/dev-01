function checkItemInWishlist() {
  //debugger;
  jQuery.ajax({
    dataType: 'json',
    url: BASE_URL + '/custom/index/checkItem'
  }).done(function(res){
    //debugger;
    jQuery.ajax({
      url: BASE_URL + 'customer/account/isajaxlogin',
      success: function(html) {
        customerIsLoggedIn = html == 1 ? true : false;
      },
      async:false
    });

    if(!customerIsLoggedIn){
      jQuery('.link-wishlist').each(function(){
        var wl_a = jQuery(this);
        var href = wl_a.attr('href');
        wl_a.attr('ref', href);
        wl_a.removeClass('added-item');
      });
      return;
    }else{
      jQuery('.link-wishlist').each(function(){
        var wl_a = jQuery(this);
        var href = wl_a.attr('href');
        wl_a.attr('ref', href)
        if(href.indexOf('wishlistindex') != -1) { // Remove link
          var removelinkpatt = /item\/\d+/igm;
          var removepattexec = removelinkpatt.exec(href);
          if (typeof removepattexec !== 'undefined' && removepattexec !== null) {
            var productId = removepattexec.shift().slice(5);
            if(res['wlitemadded'].indexOf(productId) != -1){
              return;
            }else{
              if(wl_a.hasClass('added-item')) {
                wl_a.removeClass('added-item')
                    .attr('href', BASE_URL + 'wishlist/index/add/product/' + productId + '/form_key/' + res['form_key'])
                    .attr('ref', '');
              }
            }
          }
        }else{
          var patt = /product\/\d+/igm;
          var pattexec = patt.exec(href);
          if (typeof pattexec !== 'undefined' && pattexec !== null) {
            var productId = pattexec.shift().slice(8);
            if(res['wlitemadded'].indexOf(productId) != -1){
              if(!wl_a.hasClass('added-item')) {
                wl_a.addClass('added-item')
                    .attr('ref', href)
                    .attr('href', BASE_URL + 'custom/wishlistindex/remove/item/' + productId);
              }
            }else {
              if(wl_a.hasClass('added-item')) {
                wl_a.removeClass('added-item')
                    .attr('href', wl_a.attr('ref'))
                    .attr('ref', '');
              }
            }
          }
        }
      });
    }
  });
}

var widthWindow = jQuery( window ).width();
var widthHeight = jQuery( window ).height();

function incQty(inputId){
	var qty = parseInt(jQuery('#' + inputId).val());
	jQuery('#' + inputId).val(qty + 1);
	applyChangeCheckoutCartQty(inputId);
	return false;
}

function decQty(inputId){
	var qty = parseInt(jQuery('#' + inputId).val());
	if(qty > 1){
		jQuery('#' + inputId).val(qty - 1);
	}
	applyChangeCheckoutCartQty(inputId);
	return false;
}
function applyChangeCheckoutCartQty(inputId){
	jQuery('#apply_' + inputId).show();
}


// PAYMENT CONFIRMATION
jQuery(document).ready(function(){
    jQuery("#custom-form-1 .fieldset").addClass("clearfix");
    jQuery("#custom-form-1 .fieldset:nth-child(6)").addClass("bank");
    jQuery("#custom-form-1 .fieldset:nth-child(6)").addClass("bank_radio");
    jQuery("#custom-form-1 .fieldset:nth-child(9)").addClass("bank");
    jQuery("#custom-form-1 .fieldset:nth-child(10)").addClass("bank");
    jQuery("#custom-form-1 .fieldset:nth-child(11)").addClass("hide");
});
//////

/**
	* Magebuzz / Buzzthemes
	* Popup sharing
*/

function ss_plugin_loadpopup_js(em){
	var shareurl=em.href;
	var top = (screen.availHeight - 500) / 2;
	var left = (screen.availWidth - 500) / 2;
	var popup = window.open(
			shareurl,
			'social sharing',
			'width=550,height=420,left='+ left +',top='+ top +',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1'
	);
	return false;
}

function closeWhenClickOutside( target, content, functionName, className, event){
  if(!jQuery(event.target).is(target) && !jQuery(event.target).is(content)) {
    if(!jQuery(event.target).closest(content).length) {
      if(jQuery(content).is(":visible")) {
        (functionName == 'slideUp') ? jQuery(content).slideUp() : jQuery(content).hide();
        jQuery(target).removeClass(className);
      }
    }
  }
}

jQuery(document).ready(function () {

	jQuery(document).click(function(event) {
    //Hide login box
    closeWhenClickOutside('.togglelogin','#header-mini-login', 'hide', 'active', event);
    // Hide mini cart box
    closeWhenClickOutside('.toggle-minicart','#mini-cart-info', 'hide', 'active', event);
    // Hide share
    closeWhenClickOutside('#shareProductLink','#shareproduct-content', 'slideUp', 'active', event);
	})

  //checkItemInWishlist();

  /* language */
  jQuery(".dropdown img.flag").addClass("flagvisibility");
  jQuery(".dropdown dt a").click(function() {
      jQuery(".dropdown dd ul").toggle();
  });

  jQuery(".dropdown dd ul li a").click(function() {
      var text = jQuery(this).html();
      jQuery(".dropdown dt a").html(text);
      jQuery(".dropdown dd ul").hide();
      jQuery("#result").html(getSelectedValue("sample"));
  });

  function getSelectedValue(id) {
      return jQuery("#" + id).find("dt a").html();
  }
  jQuery(document).bind('click', function(e) {
      var jQueryclicked = jQuery(e.target);
      if (! jQueryclicked.parents().hasClass("dropdown"))
          jQuery(".dropdown dd ul").hide();
  });

  jQuery(".dropdown img.flag").toggleClass("flagvisibility");

  /* end language */


  /* tab */
  //When page loads...
	jQuery(".tab_content").hide(); //Hide all content
	jQuery("ul.tabs li.first").addClass("active").show(); //Activate first tab
	jQuery(".tab_content.first").show(); //Show first tab content

	//On Click Event
	jQuery("ul.tabs li").click(function() {
		jQuery("ul.tabs li").removeClass("active"); //Remove any "active" class
		jQuery(this).addClass("active"); //Add "active" class to selected tab
		jQuery(".tab_content").hide(); //Hide all tab content

		var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		jQuery(activeTab).show(); //Fade in the active ID content
		return false;
	});

  // box-list-tab
  jQuery(".tab-title").click(function() {
    if (!jQuery(this).hasClass('active')){
      jQuery(this).next().slideDown();
      jQuery(this).addClass('active');
    }else if (jQuery(this).hasClass('active')) {
      jQuery(this).next().slideUp();
      jQuery(this).removeClass('active');
    }
  });

  if(widthWindow <= 800){
    jQuery("#tabs_history .tabs_history").mCustomScrollbar({
      axis:"x",
      advanced:{
        autoExpandHorizontalScroll:true
      }
    });
  };

  //When page loads...
  jQuery(".tab-history-content").hide(); //Hide all content
  jQuery("ul.tabs_history li:first").addClass("active").show(); //Activate first tab
  jQuery(".tab-history-content:first").show(); //Show first tab content

  //On Click Event
  jQuery("ul.tabs_history li").click(function() {

    jQuery("ul.tabs_history li").removeClass("active"); //Remove any "active" class
    jQuery(this).addClass("active"); //Add "active" class to selected tab
    jQuery(".tab-history-content").hide(); //Hide all tab content

    var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
    jQuery(activeTab).fadeIn(); //Fade in the active ID content
    return false;
  });
  /* change position special-price and old-price in products-grid */
  // (jQuery('.products-grid .special-price')).insertBefore(jQuery('.products-grid .old-price'));
  // jQuery('.special-price + .special-price').remove();

  jQuery(".go-top").click(function() {
    jQuery('html, body').animate({
    scrollTop: 0
  }, 500);
  });
  jQuery('#shareProductLink').click(function() {
		if (!jQuery(this).hasClass('active')){
			jQuery('#shareproduct-content').slideDown();
			jQuery(this).addClass('active');
		}else if (jQuery(this).hasClass('active')) {
			jQuery('#shareproduct-content').slideUp();
      jQuery(this).removeClass('active');
    }

	});

  /* box links footer on mobile */
  jQuery('.footer-links .box-links h3').click(function(){
    jQuery('.footer-links .box-links ul').slideUp(300);
    if (!jQuery(this).hasClass('active')){
      jQuery(this).next().slideToggle(300);
      jQuery('.footer-links .box-links h3').removeClass('active');
      jQuery(this).addClass('active');
    }
    else if (jQuery(this).hasClass('active')) {
      jQuery(this).removeClass('active');
    }
	});
  /* mini login */
  jQuery('.togglelogin').on('click', function(event){
    jQuery('.box-header-content').hide();
    if (!jQuery(this).hasClass('active')){
      jQuery('.toggle-header-content').removeClass('active');
      jQuery(this).addClass('active');
      jQuery('#header-mini-login').show();
      event.stopPropagation();
    }
    else{
      jQuery(this).removeClass('active');
      jQuery('#header-mini-login').hide();
    }
  });

  jQuery('#loginClose').click(function(){
    jQuery('.box-header-content').hide();
    jQuery('#header .top-link-account .toggle-header-content').removeClass('active');
    jQuery('#header-mini-login').hide();
  });
  /* */

  jQuery('.toggle-minicart').click(function(){
		jQuery('.header-cart-box .list-items').css("max-height", widthHeight - 195);
    jQuery('.box-header-content').hide();
    if (!jQuery(this).hasClass('active')){
      jQuery('.toggle-header-content').removeClass('active');
      jQuery(this).addClass('active');
      jQuery('#mini-cart-info').show();
    }
    else{
      jQuery(this).removeClass('active');
      jQuery('#mini-cart-info').hide();
    }
  });

  jQuery('#cartClose').click(function(){
    jQuery('.box-header-content').hide();
    jQuery('#header .header-minicart .toggle-header-content').removeClass('active');
    jQuery('#header-mini-login').hide();
  });

  /*icon search on mobile */
  jQuery('#toggle-search').click(function(){
    jQuery('.box-header-content').hide();
    jQuery('.toggle-header-content').removeClass('active');
    jQuery('#header-search').show();
  });
  jQuery('.close-box-search').click(function(){
      jQuery('#header-search').hide();
  });

  jQuery(".show-categories").click(function() {
    if (!jQuery(this).hasClass('active')){
      jQuery(this).next().slideDown();
      jQuery(this).addClass('active');
    }else if (jQuery(this).hasClass('active')) {
      jQuery(this).next().slideUp();
      jQuery(this).removeClass('active');
    }
  });

  /*icon menu on mobile */
  jQuery('#toggle-menu').click(function(){
    jQuery('.box-top-menu').css("height", widthHeight -44);
		if(jQuery('.box-header-content').hasClass('active')){
			jQuery('.box-header-content').removeClass('active');
			jQuery('#header-nav .nav-primary .arrow').removeClass('active');
		}
    jQuery('.box-header-content').hide();
    if (!jQuery(this).hasClass('active')){
      jQuery('.toggle-header-content').removeClass('active');
      jQuery(this).addClass('active');
      jQuery('.box-top-menu').show();
      jQuery('#header').addClass('fixed');

    }
    else{
      jQuery(this).removeClass('active');
      jQuery('.box-top-menu').hide();
      jQuery('#header').removeClass('fixed');
    }
  });

  /*top menu nav on mobile */
  //jQuery('#header-nav .nav-primary li.level1.parent').append( "<span class='arrow box-mobile'></span>" );
  //jQuery('#header-nav .nav-primary li.level1.parent > .arrow').click(function(){
	// if (!jQuery(this).hasClass('active')){
	//	jQuery(this).prev().addClass('active');
	//	jQuery(this).addClass('active');
  //  jQuery('.box-top-menu').addClass('active');
  //  jQuery('.box-top-menu').css("overflow-y","initial");
	//	jQuery('#header-nav .nav-primary li.level1.parent > .menu-content > ul.level1').css("max-height", widthHeight -44);
	//	jQuery('#header-nav .nav-primary li.level1.parent > .menu-content > ul.level1').css("overflow-y","auto");
	// }
	// else{
	//	jQuery(this).removeClass('active');
  //  jQuery(this).prev().removeClass('active');
  //  jQuery('.box-top-menu').removeClass('active');
  //  jQuery('.box-top-menu').css("overflow-y","auto");
	//	jQuery('#header-nav .nav-primary li.level1.parent > .menu-content > ul.level1').css("max-height", "");
	//	jQuery('#header-nav .nav-primary li.level1.parent > .menu-content > ul.level1').css("overflow-y","");
	// }
  //});
  /*top menu lv2 on mobile */
  //jQuery('#header-nav .nav-primary li.level2.parent').append( "<span class='arrow box-mobile'></span>" );
  //jQuery('#header-nav .nav-primary li.level2.parent .arrow').click(function(){
  //  jQuery('#header-nav .nav-primary li.level2 > ul').slideUp(300);
  //  jQuery('#header-nav .nav-primary li.level2 > a').removeClass('active');
  //  if (!jQuery(this).hasClass('active')){
  //    jQuery(this).prev().slideToggle(300);
  //    jQuery('#header-nav .nav-primary li.level2.parent .arrow').removeClass('active');
  //    jQuery(this).addClass('active');
  //    jQuery(this).prevAll().addClass('active');
  //  }
  //  else if (jQuery(this).hasClass('active')) {
  //    jQuery(this).removeClass('active');
  //  }
	//});
  /*top menu lv2 on mobile */

  jQuery('.box-top-menu .topheader-links li.language a').click(function(){
	 if (!jQuery('.box-top-menu .topheader-links li.language a').hasClass('active')){
		jQuery('.box-menu-mobile .form-language').addClass('active');
		jQuery('.box-top-menu .topheader-links li.language a').addClass('active');
    jQuery('.box-top-menu').addClass('active');
    jQuery('body').scrollTop(0);
		jQuery('.box-top-menu').css("overflow-y","initial");
	 }
	 else{
		jQuery('.box-top-menu .topheader-links li.language a').removeClass('active');
    jQuery('.box-menu-mobile .form-language').removeClass('active');
    jQuery('.box-top-menu').removeClass('active');
		jQuery('.box-top-menu').css("overflow-y","auto");
	 }
  });
  /* cat mobile */
  jQuery('.toolbar-bottom .sort-by-mobile').remove();

	/* product page mobile */
	if(widthWindow <= 640){
		jQuery('.review-list .m-review-list-title').click(function(){
			if (!jQuery(this).hasClass('active')){
				jQuery(this).next().slideToggle(300);
				jQuery(this).addClass('active');
			}
			else{
				jQuery(this).next().slideToggle(300);
				jQuery(this).removeClass('active');
			}
		});

		jQuery('.product-view .box-collateral.box-description h2').click(function(){
			if (!jQuery(this).hasClass('active')){
				jQuery(this).next().slideToggle(300);
				jQuery(this).addClass('active');
			}
			else{
				jQuery(this).next().slideToggle(300);
				jQuery(this).removeClass('active');
			}
		});

		jQuery('.product-view .product-features .box-title h2').click(function(){
			if (!jQuery(this).hasClass('active')){
				jQuery(this).next().slideToggle(300);
				jQuery('.product-view .product-features ul.products-features-grid').slideToggle(300);
				jQuery(this).addClass('active');
			}
			else{
				jQuery(this).next().slideToggle(300);
				jQuery('.product-view .product-features ul.products-features-grid').slideToggle(300);
				jQuery(this).removeClass('active');
			}
		});

		jQuery('.product-view .product-review-summary .review-box .box-title .bl h2').click(function(){
			if (!jQuery(this).hasClass('active')){
				jQuery(this).next().slideToggle(300);
				jQuery('.product-view .product-review-summary .review-box .box-title .br').slideToggle(300);
				jQuery(this).addClass('active');
			}
			else{
				jQuery(this).next().slideToggle(300);
				jQuery('.product-view .product-review-summary .review-box .box-title .br').slideToggle(300);
				jQuery(this).removeClass('active');
			}
		});
  }

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

	jQuery('.sidebar .block-compare .block-title').click(function(){
		if (!jQuery(this).hasClass('active')){
			jQuery(this).next().slideToggle(300);
			jQuery(this).next().addClass('active');
			jQuery(this).addClass('active');
		}
		else{
			jQuery(this).next().slideToggle(300);
			jQuery(this).next().removeClass('active');
			jQuery(this).removeClass('active');
		}
	});
	if(widthWindow < 1199){
		jQuery('.categories-list .category-grid li.category-item .categories-links .link-has-sub').click(function(){
			if (!jQuery(this).hasClass('active')){
				jQuery(this).next().css("max-height", 195);
				jQuery(this).addClass('active');
			}
			else{
				jQuery(this).removeClass('active');
				jQuery(this).next().css("max-height", 0);
			}
		});
	}
	if(widthWindow <769){
		/* box promo */
		jQuery(".category-banners .div-banner").mCustomScrollbar({
			axis:"x",
			advanced:{autoExpandHorizontalScroll:true}
		});
		/* move sort-by-mobile on box filter */
		var sortByHtml = jQuery(".toolbar .sort-by-mobile").html();
		jQuery(".col-left-first .block-layered-nav .block-content").append(sortByHtml);
		//jQuery('.toolbar .sort-by-mobile').remove();
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
		/* max-height box-filter-content*/
		jQuery('.box-filter-content').css("max-height", widthHeight -100);
		/* nav-myaccount mobile*/
		jQuery('.current-myaccountpage').click(function(){
		 if (!jQuery(this).hasClass('active')){
			jQuery(this).next().slideDown(300);
			jQuery(this).addClass('active');
		 }
		 else{
			jQuery(this).removeClass('active');
			jQuery(this).next().slideUp(300);
		 }
		});

	}

   var mytabs = ".career_job .menuheaders";
   jQuery(mytabs).click(function(){
		 if(jQuery(this).hasClass('selected')){
				jQuery(this).removeClass('selected');
			}else{
				jQuery(this).addClass('selected');
			}
	 });
  jQuery('#product-viewed-minimize').html('');
	jQuery('.block-recently-viewed .box-title').click(function(){
		if (!jQuery(this).hasClass('active')){
			jQuery(this).next().slideToggle(300);
			jQuery(this).addClass('active');
		}
		else{
			jQuery(this).removeClass('active');
			jQuery(this).next().slideToggle(300);
		}
	});
    /* menu */
  //jQuery("#header-nav .nav-primary ul.level0").hide();
  //jQuery("#header-nav .nav-primary li.level0.nav-1 > a").addClass("active").show();
	//jQuery("#header-nav .nav-primary li.level0.nav-1 > ul.level0").show();
  //jQuery("#header-nav .nav-primary li.level0 > a").click(function() {
  //  if (!jQuery(this).hasClass('active')){
  //    jQuery("#header-nav .nav-primary ul.level0").hide();
  //    jQuery(this).next().show();
  //    jQuery('#header-nav .nav-primary li.level0 > a').removeClass('active');
  //    jQuery(this).addClass('active');
  //  }
  //});
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

	jQuery(".cateogry > .skip-cateogry" ).hover(function() {
			jQuery(this).addClass('skip-active');
			jQuery(this).parent().addClass('skip-active');
			jQuery(this).next().addClass('skip-active');
		}
	);
	jQuery(".box-top-menu > .page-header > .cateogry").hover(function() {
			if(jQuery(this).hasClass('skip-active')){
				jQuery(this).removeClass('skip-active');
			}
		}, function() {
			jQuery(this).removeClass('skip-active');
			jQuery(this).children(".skip-cateogry").removeClass('skip-active');
			jQuery(this).children(".menu-content").removeClass('skip-active');
		}
	);
});
function backPre(){
  parent.history.back();
  return false;
}