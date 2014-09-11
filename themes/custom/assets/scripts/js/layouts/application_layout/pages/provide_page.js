var Page = (function(page, $){

	page.constructor = $(function() {

		if (page.siteRegisterFormFields)
			page.siteRegisterFormFields.validate('#form_register').action('user:on_register');

	});

	return page;
}(Page || {}, jQuery));