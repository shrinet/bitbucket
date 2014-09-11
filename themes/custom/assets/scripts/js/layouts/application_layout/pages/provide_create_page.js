var Page = (function(page, $){

	page.constructor = $(function() {

		var logo_id = null;
		var photo_id = null;
		
		// Profile field editing
		$('#p_profile_form').fieldEditor()
			.fieldEditor('apply_validation', $('#form_provide_create'), Page.provideProfileDetailsFormFields, 
				function() { 
					// Success
					$('#form_provide_create').phpr().post().action('bluebell:on_create_provider')
						.data('provider_logo', self.logo_id)
						.data('provider_photo', self.photo_id)
						.data('redirect', root_url('provide/manage/%s'))
						.send();
				});
		
		// Provider work hours
		$('#p_work_hours_form').provideHours();

		// Provider work radius
		$('#p_work_radius_form').provideRadius({
			onComplete: function(obj) { $('#provider_service_codes').val('|' + obj.get_nearby_postcodes().join('|') + '|'); },
			radiusMax: $('#work_radius_max').val(),
			radiusUnit: $('#work_radius_unit').val()
		});

		// Role auto complete
		page.bindRoleSelect('#provide_role_name', {
			onSelect: function(obj, value){ 
				$('#provide_role_name').prev('label').text(value);
			} 
		});

		// Select country, populate state
		$('#provide_country_id').countrySelect({ 
			stateElementId:'#provide_state_id', 
			afterUpdate: function(obj) { 
				obj.stateElement = $('#provide_country_id').parent().next().children().first();
			} 
		});

		// Marry provide_radius and field_editor functionality
		$('#profile_details_address').bind('field_editor.field_set', function(e, value) { 
			$('#work_radius_address').val(value).trigger('change'); 
		});

		// Add image uploads
		$('#input_provide_photo').uploader({ 
			linkId: '#link_provide_photo', 
			removeId: '#link_provide_photo_remove',
			imageId: '#provide_photo', 
			paramName:'provider_photo',
			onSuccess: function(obj, data) { $('#link_provide_photo').hide(); self.photo_id = data.result.id; },	
			onRemove: function() { $('#link_provide_photo').fadeIn(1000); }
		});

		$('#input_provide_logo').uploader({ 
			linkId: '#link_provide_logo',
			removeId: '#link_provide_logo_remove', 
			imageId: '#provide_logo', 
			paramName:'provider_logo', 
			onSuccess: function(obj, data) { $('#link_provide_logo').hide(); self.logo_id = data.result.id; },
			onRemove: function() { $('#link_provide_logo').fadeIn(1000); $('#provide_logo').hide(); return false; }
		});

		$(window).load(function(){
			// Prepopulate map
			if ($('#profile_details_address label').hasClass('populated'))
				$('#work_radius_address').val($('#profile_details_address label').text()).trigger('change');
		});

	})

	return page;
}(Page || {}, jQuery));
