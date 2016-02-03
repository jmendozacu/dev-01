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
  
});