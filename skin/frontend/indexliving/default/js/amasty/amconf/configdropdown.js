
jQuery(document).ready(function () {
	jQuery('.amconf-images-list-container .amconf-images-list-label').click(function(){
    if (!jQuery(this).hasClass('active')){
      jQuery(this).next().slideToggle(300);
      jQuery(this).addClass('active');
    }
    else{
			jQuery(this).next().slideToggle(300);
      jQuery(this).removeClass('active');
    }
	});
});