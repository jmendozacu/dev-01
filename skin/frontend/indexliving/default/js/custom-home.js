
jQuery(document).ready(function () {
  /* box menu on homepage mobile */
  if(jQuery("#nav-home").html()){
    var navHtml = jQuery("#nav-home").html(); 
    jQuery("#department-menu-mobile").append(navHtml);
    jQuery('.main-home #nav-home').remove();
    jQuery('.main-home #nav-home').remove();
    jQuery(".box-menu-home .menu-content").remove();
  }
  
});