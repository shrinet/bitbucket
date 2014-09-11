var Page = (function(page, $){

	page.constructor = $(function() {
		// Dress up breadcrumb
		$('ul.breadcrumbs li:last').addClass('current');

		page.bindRequestPopup();
		page.bindSearchForm();
		page.bindRequestForm();

	});

	page.selectLetter = function(letter) {
		return $.phpr.post()
			.action('bluebell:on_directory')
			.data('letter', letter)
			.update('#p_directory', 'directory:letter')
			.send();
	},

	page.bindSearchForm = function() {

		var search_form = $('#form_directory_search');
		if (search_form.length == 0)
			return;

		page.directorySearchFormFields.validate(search_form)
			.action('bluebell:on_directory_search')
			.update('#p_directory', 'directory:city');
	},

	page.bindRequestPopup = function() {

		var popup_request = $('#popup_request'),
			popup_request_success = $('#popup_request_success');

		if (popup_request.length == 0)
			return;

		
		popup_request.popup({ trigger: '#button_request_popup', size:'xlarge' });
		popup_request_success.popup({ size:'xlarge'});

		page.requestQuickFormFields.validate('#form_request')
			.action('bluebell:on_directory_create_request')
			.success(function() {
				popup_request.popup('closePopup');
				popup_request_success.popup('openPopup');
			});
	},

	page.bindRequestForm = function() {
		var validate_extra_options;
		if ($('#request_role_name').length > 0) {
			page.bindRoleName();
			
			// Prevent validation and autocomplete conflict 
			validate_extra_options = { onfocusout:page.checkRequestRoleName };
		}

		if ($('#form_request_panel').length > 0) {
			page.requestQuickFormFields.validate('#form_request_panel', validate_extra_options)
				.action('bluebell:on_directory_create_request')
				.update('#p_directory_request_panel', 'directory:request_panel');
		}
	},


	page.ignore_role_name_validation = true;

	page.bindRoleName = function() {
		// Role auto complete
		page.bindRoleSelect('#request_role_name', { onSelect: function() {
				// Prevent validation and autocomplete conflict
				page.ignore_role_name_validation = true;
				setTimeout(function() { page.ignore_role_name_validation = false; }, 0);
			}
		});
	},

	// Prevent validation and autocomplete conflict
	page.checkRequestRoleName = function(element) {        
		if (!$(element).is('#request_role_name') || !page.ignore_role_name_validation) {
			if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {
				this.element(element);
			}
		}
	}

	return page;
}(Page || {}, jQuery));