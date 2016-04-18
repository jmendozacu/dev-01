jQuery(document).ready(function () {
	var layerNavElementHeight = jQuery('.block-layered-nav').outerHeight();
	var layerNavPos = jQuery('.block-layered-nav').offset().top;
	var layerNavLastElementPos = jQuery('.block-layered-nav dt.filter_sticky').offset().top;
	jQuery(this).scroll(function() {
		var scrollTopPos = jQuery(this).scrollTop();
		var screenBottomPos = screen.availHeight + scrollTopPos;
		var layerNavBottomPos = layerNavElementHeight + layerNavPos;
		var winScrollTopTranform = scrollTopPos - layerNavLastElementPos;
		var transformYValue = "translateY("+scrollTopPos+"px)";
		var footerBeforePos = jQuery('.footer-before-container').offset().top - layerNavPos;
		var toolbarBottomPos = jQuery('.toolbar-bottom').offset().top;
		if(screenBottomPos >= layerNavBottomPos && scrollTopPos <= footerBeforePos) {
			transformYValue = "translateY("+winScrollTopTranform+"px)";
			jQuery('.block-layered-nav').css('transform', transformYValue);
		}else {
			transformYValue = "translateY(0px)";
			jQuery('.block-layered-nav').css('transform', transformYValue);
		}
	});
});