jQuery(document).ready(function () {
	if(jQuery('.block-layered-nav').length > 0){
		/* var layerNavElementHeight = jQuery('.block-layered-nav').outerHeight();
		var layerNavPos = jQuery('.block-layered-nav').offset().top;
		if(jQuery('.block-layered-nav dt.filter_sticky')){
			var layerNavStartStickyElementPos = jQuery('.block-layered-nav dt.filter_sticky').offset().top;
		}else{
			var layerNavStartStickyElementPos = jQuery('.block-layered-nav dt.last').offset().top;
		}
		
		jQuery(this).scroll(function() {
			var scrollTopPos = jQuery(this).scrollTop();
			var screenBottomPos = screen.availHeight + scrollTopPos;
			var layerNavBottomPos = layerNavElementHeight + layerNavPos;
			var winScrollTopTranform = scrollTopPos - layerNavStartStickyElementPos;
			var transformYValue = "translateY("+scrollTopPos+"px)";
			var footerBeforePos = jQuery('.footer-before-container').offset().top - layerNavPos;
			var layerNavLastElementHeight = jQuery('.block-layered-nav dd.last').outerHeight();
			if(jQuery('.sidebar .block-compare')){
				var sidebarCompareHeight = jQuery('.sidebar .block-compare').outerHeight();
				var stopStickyPos = footerBeforePos - sidebarCompareHeight - layerNavLastElementHeight;
			}else{
				var stopStickyPos = footerBeforePos - layerNavLastElementHeight;
			}
			
			if(screenBottomPos >= layerNavBottomPos && scrollTopPos <= stopStickyPos) {
				transformYValue = "translateY("+winScrollTopTranform+"px)";
				jQuery('.col-left-first').css('transform', transformYValue);
			}else {
				transformYValue = "translateY(0px)";
				jQuery('.col-left-first').css('transform', transformYValue);
			}
		}); */
	}
});