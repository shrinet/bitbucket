var Page = (function(page, $){

	page.constructor = $(function() {

		page.requestSimpleFormFields.validate('#request_form')
			.action('bluebell:on_review_request');

		// Role selection
		page.bindRoleSelect('#request_title', {
			onChange: function(obj, category_name) { 
				page.roleSelectionChanged(category_name);
			}
		});

		page.bindRequiredBy();

		// Alternative time
		$('#link_request_alt_time').click(page.clickAltTime);

		// Remote location
		$('#location_remote').change(function() { page.clickLocationRemote(this); });
		page.clickLocationRemote('#location_remote');

		page.bindAddPhotos();
	});	

	// Update the custom description area when role is changed
	page.roleSelectionChanged = function(category_name) {
		$('#request_form').phpr().post().action('bluebell:on_update_request_category')
			.update('#p_request_custom_description', 'request:custom_description')
			.afterUpdate(function(){
				page.bindAddPhotos();
			}, true)
			.send();
	}

	page.bindAddPhotos = function() {
		// Add photos
		$('#input_add_photos').uploader({
			mode: 'multi_image', 
			linkId:'#link_add_photos', 
			paramName:'request_images[]'
		});
	}

	// Required by logic
	page.bindRequiredBy = function() {
		var required_by = $('#required_by_group');
		var checked = required_by.find('label input:checked');

		if (checked.length == 0)
			checked = required_by.find('label input:first').attr('checked', true);

		page.clickRequiredBy(checked);
		
		required_by.find('label input').change(function() { 
			page.clickRequiredBy(this);
		});			
	}

	page.clickRequiredBy = function(element) {
		var label = $(element).closest('li');
		label.siblings().removeClass('selected').find('.radio-expand').hide();
		label.addClass('selected').find('.radio-expand').show();
	}

	page.clickAltTime = function() {
		$(this).toggleText();
		$(this).prev().toggleText();
		var container = $('.firm-date-secondary').toggle();
	}

	page.clickLocationRemote = function(obj) {
		var checked = $(obj).is(':checked');	
		var location = $('#request_location');
		location.attr('disabled', checked);
		if (checked) {
			location.val('').removeClass('error valid')
				.closest('.control-group').removeClass('error')
				.find('.validate-text').remove();
		}
	}

	return page;
}(Page || {}, jQuery));
