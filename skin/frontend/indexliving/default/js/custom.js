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

jQuery(document).ready(function () {

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
  
  /* change position special-price and old-price in products-grid */
  (jQuery('.products-grid .special-price')).insertBefore(jQuery('.products-grid .old-price'));
  jQuery('.special-price + .special-price').remove();
  
  jQuery(".go-top").click(function() {
    jQuery('html, body').animate({
    scrollTop: 0
  }, 500);
  });
  jQuery('#shareProductLink').click(function() {
		jQuery('#shareproduct-content').toggle("slide",{ direction: "right" }, 500);
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
  /*top menu on mobile */
  jQuery('.nav-primary li.level0.parent').append( "<span class='arrow box-mobile'></span>" );
  jQuery('.nav-primary li.level0.parent > .arrow').click(function(){
	 if (!jQuery(this).hasClass('active')){
		jQuery(this).prev().addClass('active');
		jQuery(this).addClass('active');
    jQuery('.box-top-menu').addClass('active');
	 }
	 else{
		jQuery(this).removeClass('active');
    jQuery(this).prev().removeClass('active');
    jQuery('.box-top-menu').removeClass('active');
	 }
  });
  /*top menu lv2 on mobile */
  jQuery('.nav-primary li.level1.parent').append( "<span class='arrow box-mobile'></span>" );
  jQuery('.nav-primary li.level1.parent .arrow').click(function(){
    jQuery('.nav-primary li.level1 > ul').slideUp(300);
    jQuery('.nav-primary li.level1 > a').removeClass('active');
    if (!jQuery(this).hasClass('active')){
      jQuery(this).prev().slideToggle(300);
      jQuery('.nav-primary li.level1.parent .arrow').removeClass('active');
      jQuery(this).addClass('active');
      jQuery(this).prevAll().addClass('active');
    }
    else if (jQuery(this).hasClass('active')) {
      jQuery(this).removeClass('active');
    }
	});
  /*top menu lv2 on mobile */
 
  jQuery('.box-top-menu .topheader-links li.language a').click(function(){
	 if (!jQuery('.box-top-menu .topheader-links li.language a').hasClass('active')){
		jQuery('.box-menu-mobile .form-language').addClass('active');
		jQuery('.box-top-menu .topheader-links li.language a').addClass('active');
    jQuery('.box-top-menu').addClass('active');
	 }
	 else{
		jQuery('.box-top-menu .topheader-links li.language a').removeClass('active');
    jQuery('.box-menu-mobile .form-language').removeClass('active');
    jQuery('.box-top-menu').removeClass('active');
	 }
  });
  
});