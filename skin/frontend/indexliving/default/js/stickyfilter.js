jQuery(document).ready(function () {
	if(jQuery('.block-layered-nav').length > 0){
		/* var bottomElementPos = jQuery('.block-layered-nav dd.last ol li:last-child').offset().top;
		if(jQuery('.sidebar .block-compare').length > 0){
			bottomElementPos = jQuery('.sidebar .block-compare').offset().top;
		}
		var beforeFooterPos = jQuery('.footer-before-container').offset().top;
		var toolbarBottomPos = jQuery('.toolbar-bottom').offset().top;
		var stickyStartPos = jQuery('.block-layered-nav dd.filter_sticky ol li:last-child').offset().top;
		jQuery(this).scroll(function() {
			var scrollPos = jQuery(this).scrollTop();
			var transformYValue = "translateY("+stickyStartPos+"px)";
			var stickyPosScroll = scrollPos - stickyStartPos
			if(scrollPos >= bottomElementPos && scrollPos <= toolbarBottomPos) { // Vị trí sticky chạy và dừng lại nếu scroll up
				transformYValue = "translateY("+stickyPosScroll+"px)";
				jQuery('.col-left-first').css('transform', transformYValue);
			}
		}); */
		jQuery('.col-left-first').theiaStickySidebar({
			additionalMarginTop: 30
		});
	}
});