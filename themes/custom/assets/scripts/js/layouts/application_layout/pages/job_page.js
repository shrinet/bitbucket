var Page = (function(page, $){

	page.constructor = $(function() {

		$('#link_edit_quote').live('click', page.click_edit_quote);
		$('#link_delete_quote').live('click', page.click_delete_quote);

		// Init others
		page.bind_ask_question();
		page.bind_request_ignore();
	
	});

	// Quote
	//

	page.initQuoteForms = function() {

		// Forms
		page.bindQuoteFlatRate();
		page.bindQuoteOnsite();
	};

	// Flat rate quote
	page.bindQuoteFlatRate = function() {

		if (!$('#p_quote_flat_rate').length)
			return;

		// Quote behavior
		page.bindQuoteFlatRateUI('#p_quote_flat_rate');

		// Validate
		var validateObj = page.jobQuoteFlatRateFormFields.validate('#form_quote_flat_rate'),
			postObj = validateObj.action('bluebell:on_create_quote')
				.update('#p_quote_panel', 'job:quote_summary');

		if (page.jobQuoteMessageFormFields)
			validateObj.addFormFields(page.jobQuoteMessageFormFields);

		// Message
		page.bindQuoteMessageUI('#p_personal_message_flat');
	};

	// On site quote
	page.bindQuoteOnsite = function() {

		if (!$('#p_quote_onsite').length)
			return;

		// Quote behavior
		page.bindQuoteOnSiteUI('#p_quote_onsite');

		// Validate
		var validateObj = page.jobQuoteOnSiteFormFields.validate('#form_quote_onsite'),
			postObj = validateObj.action('bluebell:on_create_quote')
				.update('#p_quote_panel', 'job:quote_summary');
		
		if (page.jobQuoteMessageFormFields)
			validateObj.addFormFields(page.jobQuoteMessageFormFields);

		// Message
		page.bindQuoteMessageUI('#p_personal_message_onsite');

	};

	// Quote summary
	//

	page.click_edit_quote = function() {
		return $('#form_quote_summary').phpr().post()
			.action('bluebell:on_create_quote')
			.update('#p_quote_panel', 'job:quote_submit')
			.send();
	};

	page.click_delete_quote = function() {
		return $('#form_quote_summary').phpr().post()
			.action('bluebell:on_create_quote')
			.data('delete', true)
			.update('#p_quote_panel', 'job:quote_submit')
			.send();
	};

	// Question
	//

	page.bind_ask_question = function() {
		// Show answers
		$('#job_questions span.answer').hide();
		$('#job_questions a.question').live('click', function() {
			$('#job_questions span.answer').hide();
			$(this).next().show();
		})

		// Ask a question
		if (!page.askQuestionFormFields)
			return;
		
		$('#link_ask_question').click(function() {
			$(this).hide();
			$('#ask_question').show();
		});

		// Validate
		page.askQuestionFormFields.validate('#ask_question_form')
			.action('service:on_create_question')
			.update('#p_ask_question', 'job:ask_question')
			.afterUpdate(function(){ page.bind_ask_question(); });
	};

	// Ignore request
	page.bind_request_ignore = function() {
		
		$('#popup_request_ignore').popup({ trigger: '#link_request_ignore' });

		$('#button_request_ignore').click(function() {
			var request_id = $(this).data('request-id');
			$.phpr.post().action('service:on_ignore_request').data('request_id', request_id).data('redirect', root_url('dashboard')).send();
		});

	}

	return page;
}(Page || {}, jQuery));
