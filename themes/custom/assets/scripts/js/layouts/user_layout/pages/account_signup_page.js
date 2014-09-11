var Page = (function(page, $){

	page.constructor = $(function() {

		page.siteRegisterFormFields.validate('#form_register').action('user:on_register');

	});

	return page;
}(Page || {}, jQuery));