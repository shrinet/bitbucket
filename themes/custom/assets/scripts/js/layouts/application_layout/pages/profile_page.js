var Page = (function(page, $){

	page.constructor = $(function() {
		// Init others
		page.bind_request_popup();
		page.bind_other_providers();
		page.bind_rating_popup();
		page.bind_rating_full_popup();

	});


	page.bind_request_popup = function() {

		// Popup
		$('#popup_contact_provider').popup({ trigger: '#button_contact_provider', size:'xlarge' });
		$('#popup_contact_provider_success').popup({ size:'xlarge' });

		page.requestQuickFormFields.validate('#form_request')
			.action('bluebell:on_directory_create_request')
			.success(function() {
				$('#popup_contact_provider_success').popup('openPopup');
			});
	}
	
	page.bind_rating_popup = function() {

		// Popup
		//$('#popup_rating_provider').popup({ trigger: '#button_rating_provider', size:'xlarge' });
		$('#popup_rating_provider_success').popup({ size:'xlarge' });

		page.requestQuickFormFields.validate('#form_rating')
			.action('bluebell:on_profile_create_rating')
			.success(function() {
				//$('#popup_rating_provider_success').popup('openPopup');
			});
	}
		page.bind_rating_full_popup = function() {

		// Popup
		$('#popup_full_ratings').popup({ trigger: '#button_rating_full', size:'xlarge' });
		$('#popup_full_ratings').popup({ size:'xlarge' });

		
	}
	page.bind_other_providers = function() {
		$('#other_providers > ul').addClass('jcarousel-skin-ahoy').jcarousel({scroll:1});
		$('#other_providers li').click(function() {
			var provider_id = $(this).data('id');            
			if (provider_id)
				$('#map_other_providers').gmap_locator('focusLocation', provider_id);
			
		});

		$('#map_other_providers').gmap_locator({
			container: '#other_providers ul',
			bubble_id_prefix: '#location_',
			allow_scrollwheel: false,
			bubble_on_hover: false,
			zoom_ui: true            
		})
	}

	return page;
}(Page || {}, jQuery));
