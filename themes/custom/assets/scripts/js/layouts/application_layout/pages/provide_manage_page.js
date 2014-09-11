var Page = (function(page, $){

	page.constructor = $(function() {

		// Success popup
		$('#popup_profile_success').popup({ trigger: '#button_done' });
		
		// Image uploads
		$('#input_provide_photo').uploader({
			linkId: '#link_provide_photo',
			removeId: '#link_provide_photo_remove',
			imageId: '#provide_photo',
			paramName:'provider_photo',
			onSuccess: function(obj, data) { $('#link_provide_photo').hide(); },
			onRemove: function() {
				$('#link_provide_photo').fadeIn(1000);
				$('#input_provide_photo').phpr().post().action('service:on_update_provider_photo').data('delete', true).send();
			}
		});

		$('#input_provide_logo').uploader({
			linkId: '#link_provide_logo',
			removeId: '#link_provide_logo_remove',
			imageId: '#provide_logo',
			paramName:'provider_logo',
			onSuccess: function(obj, data) { $('#link_provide_logo').hide(); },
			onRemove: function() {
				$('#link_provide_logo').fadeIn(1000);
				$('#provide_logo').hide();
				$('#input_provide_logo').phpr().post().action('service:on_update_provider_logo').data('delete', true).send();
				return false;
			}
		});

		$('#popup_profile_membership').on('click','.membershipSinglePanel',function () {
             $('.membershipSinglePanel').removeClass('selected');
             $(this).addClass('selected')
        });

		// Init others
		page.bindProfileFields();
		page.bindProfileDescription();
		page.bindProfileTestimonials();
		page.bindWorkRadius();
		page.bindWorkHours();
		page.bindPortfolio();
		page.bindProfileDelete();
		page.bindMembership();
	});

	// Profile fields
	page.bindProfileFields = function() {

		var self = this;

		// Save event
		var saveProfileField = function(object, element, value) {
			// Check form is valid
			var form = element.closest('form');
			if (form.valid()) {
				// Save profile field
				element.phpr().post().action('bluebell:on_update_provider').send();
			}
		};

		// Editing
		$('#p_profile_form').fieldEditor({ on_set_field: saveProfileField })
			.fieldEditor('apply_validation', $('#form_provide_details'), Page.provideProfileDetailsFormFields);

		// Role auto complete
		page.bindRoleSelect('#provide_role_name', {
			onSelect: function(obj, value){
				$('#provide_role_name').prev('label').text(value);
			}
		});

		// Select country, populate state
		$('#provide_country_id').countrySelect({ 
			stateElementId: '#provide_state_id',
			afterUpdate: function(obj) {
				obj.stateElement = $('#provide_country_id').parent().next().children().first();
			}
		});

		// Marry provide_radius and field_editor functionality
		$('#profile_details_address').bind('field_editor.field_set', function(e, value) {
			$('#work_radius_address').val(value).trigger('change');
		});

	}

	// Profile description
	page.bindProfileDescription = function() {

		// Popup
		$('#popup_profile_description').popup({ trigger: '#button_profile_description' });

		// Save
		$('#form_provide_decription').submit(function() {
			$('#form_provide_decription').phpr().post()
				.action('bluebell:on_update_provider_description')
				.update('#p_provide_description', 'provide:description')
				.send();
			$('#popup_profile_description').popup('closePopup');
			return false;
		});
	}

	// Testimonials
	page.bindProfileTestimonials = function() {

		// Popup
		$('#popup_profile_testimonials').popup({ 
			trigger: '#button_profile_testimonials', 
			onOpen: function() { 
				$('#form_provide_testimonials').show();
				$('#profile_testimonials_success').hide();
				$('#testimonial_email').val('');
			}
		});


		// Save
		Page.provideTestimonialsFormFields.validate('#form_provide_testimonials')
			.action('service:on_ask_testimonial')
			.success(function(){
				$('#form_provide_testimonials').hide();
				$('#profile_testimonials_success').show();
			});

		// Delete
		$('#p_provide_testimonials .button_delete_testimonial').live('click', function() {
			var testimonial_id = $(this).data('testimonial-id');
			var provider_id = $(this).data('provider-id');
			var confirm = $(this).data('confirm');
			$.phpr.post().action('service:on_delete_testimonial')
				.data('testimonial_id', testimonial_id)
				.data('provider_id', provider_id)
				.update('#p_provide_testimonials', 'provide:testimonials')
				.confirm(confirm)
				.send();

		});

	}

	// Portfolio
	page.bindPortfolio = function() {

		// Popup
		$('#popup_profile_portfolio').popup({
			size: 'expand',
			trigger: '.button_profile_portfolio',
			onClose: function() {
				$('#form_provide_portfolio').phpr().post()
					.action('service:on_refresh_provider_portfolio')
					.data('is_manage', true)
					.update('#p_provide_portfolio', 'provide:portfolio')
					.beforeSend(function(){
						$('#provider_portfolio').portfolio('destroy');
					})
					.send();
			}
		});

		// Uploader
		$('#input_add_photos').uploader({
			mode: 'multi_image', 
			linkId:'#link_add_photos',
			paramName:'provider_portfolio[]',
			onRemove: function(obj, file_id) {
				$('#input_add_photos').phpr().post()
					.action('service:on_update_provider_portfolio')
					.data('file_id', file_id)
					.data('delete', true)
					.send();
			}
		});

	}

	// Work radius
	page.bindWorkRadius = function() {

		// Popup
		$('#popup_work_radius').popup({ trigger: '#link_work_area', size:'expand', onOpen: page.bindWorkRadiusForm });

		// Save
		$('#form_work_radius').submit(function() {
			$('#form_work_radius').phpr().post().action('bluebell:on_update_provider').send();
			$('#popup_work_radius').popup('closePopup');
			return false;
		});

	}

	page.bindWorkRadiusForm = function() {
		// Map
		$('#p_work_radius_form').not('.work-radius-loaded').addClass('work-radius-loaded')
			.provideRadius({
				onComplete: function(obj) { $('#provider_service_codes').val('|' + obj.get_nearby_postcodes().join('|') + '|'); },
				radiusMax: $('#work_radius_max').val(),
				radiusUnit: $('#work_radius_unit').val()
			});

		// Prepopulate map
		$('#work_radius_address').val($('#profile_details_address label').text()).trigger('change');

	}

	// Work hours
	page.bindWorkHours = function() {

		// Behavior
		$('#p_work_hours_form').provideHours({ use_general: false });

		// Popup
		$('#popup_work_hours').popup({ trigger: '#link_work_hours', size:'large' });

		// Save
		$('#form_work_hours').submit(function() {
			$('#form_work_hours').phpr().post().action('bluebell:on_update_provider').send();
			$('#popup_work_hours').popup('closePopup');
			return false;
		});

	}
	
	//Membership
	page.bindMembership = function(){
		$('#popup_profile_membership').popup({ trigger: '#link_update_membership'});
		
		$('#form_provide_membership').submit(function(){
//			var provider_id = $(this).data('provider-id');
			
			$('#form_provide_membership').phpr().post().action('service:on_create_membership').send();
			$('#popup_profile_membership').popup('closePopup');
			return false;
		});
		
	}

	// Profile delete
	page.bindProfileDelete = function() {
		
		$('#popup_profile_delete').popup({ trigger: '#link_delete_profile' });

		$('#button_profile_delete').click(function() {
			var provider_id = $(this).data('provider-id');
			$.phpr.post().action('service:on_delete_provider').data('provider_id', provider_id).data('redirect', root_url('provide/profiles')).send();
		});

	}

	return page;
}(Page || {}, jQuery));
