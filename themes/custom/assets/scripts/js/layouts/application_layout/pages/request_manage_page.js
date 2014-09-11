var Page = (function(page, $){

	page.constructor = $(function() {
		// Quotes
		page.bindQuotesUI('#p_quotes');

		// Init others
		page.bindAddDescription();
		page.bindQuestionSubmenu();
		page.bindRequestCancel();
		page.bindExtendTime();		
	});

	// Add photos
	page.bindAddPhotos = function() {

		$('#input_add_photos').uploader({
			mode: 'multi_image', 
			linkId:'#link_add_photos', 
			paramName:'request_images[]',
			onRemove: function(obj, file_id) {
				$('#input_add_photos').phpr().post().action('service:on_delete_request_attachment').data('file_id', file_id).send();
			}			
		});

	}

	// Description
	//

	page.bindAddDescription = function() {
		$('#link_add_description, #link_add_description_cancel').live('click', function(){
			$('#panel_add_description').toggle();
			$('#link_add_description').toggleText();
		});
	}

	// Questions
	//

	page.bindQuestionSubmenu = function() {
		var container = $('#p_questions');
		container.find('.answer_form.flagged').hide();

		container.find('.nav a').click(function() {
			var type = $(this).data('filter-type');

			var listItem = $(this).closest('li');
			if (listItem.hasClass('disabled'))
				return false; 

			listItem.siblings().removeClass('active');
			$(this).parent().addClass('active');

			if (type=="all")
				container.find('.answer_form').show().end().find('.answer_form.flagged').hide();
			else if (type=="unanswered")
				container.find('.answer_form').hide().end().find('.answer_form.unanswered').show();
			else if (type=="flagged")
				container.find('.answer_form').hide().end().find('.answer_form.flagged').show();

		});

	}

	page.questionEditToggle = function(question_id) {
		var form = $('#form_answer_question_'+question_id);
		form.find('.view').toggle();
		form.find('.edit').toggle();
	}

	page.questionFlag = function(question_id, remove_flag) {
		var form = $('#form_answer_question_'+question_id);
		var ajax = form.phpr().post().action('service:on_flag_question');

		if (remove_flag)
			ajax.data('remove_flag', true);

		ajax.update('#p_request_answer_form_'+question_id, 'request:answer_form')
			.afterUpdate(function() {
				page.questionValidateForm(question_id);
				$('#form_answer_question_'+question_id).parent().hide();
			});

		return ajax.send();
	}

	page.questionValidateForm = function(question_id, status) {
		var action = (status=="answered") ? 'service:on_update_answer' : 'service:on_create_answer';
		var form = $('#form_answer_question_'+question_id);

		Page.requestAnswerFormFields.validate(form)
			.action(action)
			.update('#p_request_answer_form_'+question_id, 'request:answer_form')
			.afterUpdate(function() {
				page.questionValidateForm(question_id);
			});
	}

	// Cancel request
	page.bindRequestCancel = function() {

		$('#popup_request_cancel').popup({ trigger: '#link_request_cancel' });
		
		$('#button_request_cancel').click(function() {
			var requestId = $(this).data('request-id');
			$.phpr.post().action('service:on_cancel_request')
				.data('request_id', requestId)
				.data('redirect', root_url('dashboard'))
				.send();
		});

	}

	// Extend request time
	page.bindExtendTime = function() {
		$('#link_extend_time').click(function() {
			var requestId = $(this).data('request-id');
			$.phpr.post().action('service:on_extend_request')
				.data('request_id', requestId)
				.update('#p_status_panel', 'request:status_panel')
				.afterUpdate(page.bindExtendTime)
				.send();
		});

	}


	return page;
}(Page || {}, jQuery));


