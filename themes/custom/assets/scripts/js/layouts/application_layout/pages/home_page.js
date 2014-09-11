var Page = (function(page, $){

	page.constructor = $(function() {

		$('#banners .sub_banner').click(function(){
			window.location = $(this).data('link');
		});

	});

	return page;
}(Page || {}, jQuery));

