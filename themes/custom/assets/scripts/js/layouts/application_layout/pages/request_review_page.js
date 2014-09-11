var Page = (function(page, $){

	var _validation_object,
		_post_object;

	page.constructor = $(function() {
		
		_validation_object = $('#review_form').phpr().validate()
		_post_object = _validation_object.action('bluebell:on_create_request');

		if ($('#p_account').length > 0)
			page.validateAccountForm();
	});

	/**
	 * Account logic
	 */

	page.validateAccountForm = function(isLogin) {

		_validation_object.resetFormFields();

		if (isLogin)
			_validation_object.addFormFields(Page.siteLoginFormFields);
		else 
			_validation_object.addFormFields(Page.siteRegisterFormFields);
	}

	page.toggleAccountRequest = function() {
		var is_register = $('#title_register').is(':visible');
		var ajax = $('#review_form').phpr().post()
			.action('on_action')
			.success(function(){
				$('#title_register, #title_login').toggle();
			})
			.afterUpdate(function(){
				page.validateAccountForm(is_register);
			});

		if (is_register) 
			ajax.update('#p_account', 'site:login_form').send();
		else 
			ajax.update('#p_account', 'site:register_form').send();
	}

	return page;
}(Page || {}, jQuery));
