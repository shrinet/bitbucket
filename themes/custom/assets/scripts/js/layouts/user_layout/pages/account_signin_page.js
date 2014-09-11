var Page = (function(page, $){

	page.constructor = $(function() {

		page.siteLoginFormFields.validate('#form_login').action('user:on_login');

		$('#user_login').change(function() { $('#user_password').removeData("previousValue"); });
	});

	return page;
}(Page || {}, jQuery));