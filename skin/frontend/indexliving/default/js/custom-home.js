
jQuery(document).ready(function () {
  /* box menu on homepage mobile */
  if(jQuery("#nav-home").html()){
    var navHtml = jQuery("#nav-home").html(); 
    jQuery("#department-menu-mobile").append(navHtml);
    jQuery('.main-home #nav-home').remove();
    jQuery('.main-home #nav-home').remove();
    jQuery(".box-menu-home .menu-content").remove();
  }
  
  jQuery(".box-list-products .category-products .block-content, .box-home-list-products .nav-primary li.level0 ul.level0").mCustomScrollbar({
    axis:"x",
    advanced:{autoExpandHorizontalScroll:true},
    callbacks:{onScroll: function() {jQuery(this).find('img.lazy').jail();}}
  });
  
});