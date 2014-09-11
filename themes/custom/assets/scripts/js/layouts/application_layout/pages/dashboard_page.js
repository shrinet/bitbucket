var Page = (function(page, $){

	page.constructor = $(function() {
		page.bindJobOfferFilter();
	});

	page.filterJobOffers= function(type) {
		$.phpr.post().action('bluebell:on_dashboard_filter_job_offers')
			.data('filter', type)
			.update('#p_dash_offers', 'dash:offers')
			.afterUpdate(page.bindJobOfferFilter, true)
			.send();
	}

	page.bindJobOfferFilter = function() {
		$('#p_dash_offers .offer-filter a').on('click', function(){
			var type = $(this).data('filter-type');
			page.filterJobOffers(type);
		});		
	}	

	return page;
}(Page || {}, jQuery));