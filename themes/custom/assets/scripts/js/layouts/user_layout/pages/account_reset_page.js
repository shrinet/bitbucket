var Page = (function(page, $){

	page.constructor = $(function() {

		page.forgotPasswordFormFields.validate('#form_reset_pass')
			.action('user:on_reset_password')
			.success(function(){
				$('#reset_pass_form, #reset_pass_success').toggle();
				$('#login').val($('#forgot_email').val());
			});

	});

	return page;
}(Page || {}, jQuery));
