/**
 * Scripts Ahoy! Software
 *
 * Copyright (c) 2012 Scripts Ahoy! (scriptsahoy.com)
 * All terms, conditions and copyrights as defined
 * in the Scripts Ahoy! License Agreement
 * http://www.scriptsahoy.com/license
 *
 */


/**
 * Request quotes behavior
 */

var Page = (function(page, $){

	var _quote_list,
		_quotes;

	page.bindQuotesUI = function(element) {
		element = $(element);
		
		_quote_list = element.find('.quote_list li');
		_quotes = element.find('.quote_panel .quote');

		// Init
		_quote_list.first().addClass('active');
		_quotes.first().show();

		// Events
		_quote_list.click(function() {
			var id = $(this).attr('data-quote-id');
			page.clickQuote(id);
		});

		// Bind events
		_quotes.each(function(){
			var id = $(this).attr('data-quote-id');
			page.bindPopup(id);
			page.bindConversation(id);
			page.bindBookingTime(id);
		});

	}

	page.clickQuote = function(id) {
		_quotes.hide();
		_quote_list.removeClass('active');
		$('#p_quote_panel_'+id).show();
		$('#quote_list_'+id).addClass('active');

	};

	// Popup
	page.bindPopup = function(id) {
		$('#popup_quote_'+id).popup({ 
			trigger: '#button_quote_'+id, 
			moveToElement: '#page_request_manage', 
			size: 'large' 
		});

		$('#popup_quote_'+id+'_country').countrySelect({ 
			stateElementId: '#popup_quote_'+id+'_state',
			afterUpdate: function(obj) { 
				obj.stateElement = $('#popup_quote_'+id+'_state');
			}
		});

		// Validate
		Page.requestQuotePanelFormFields.validate('#form_quote_'+id)
			.action('bluebell:on_accept_request_quote');
	};

	// Conversation
	page.bindConversation = function(id) {

		var question_button = $('#button_question_'+id);
		var conversation_form = $('#form_conversation_'+id);
		var has_conversation = (conversation_form.find('.conv_message:first').length > 0);

		if (has_conversation) {
			question_button.closest('.conversation_question').hide().siblings('.info:first').hide();
		} else {
			conversation_form.hide();
			question_button.click(function() {
				question_button.closest('.conversation_question').hide().siblings('.info:first').hide();
				conversation_form.show();
			});
		}

		page.validateConversation(conversation_form);
	};

	page.validateConversation = function(form) {
		// Validate
		page.controlConversationFormFields.validate(form)
			.action('bluebell:on_send_quote_message')
			.update('#'+form.attr('id'), 'control:conversation');
	};

	// Suggest booking time
	page.bindBookingTime = function(id) {
		var booking_time_link = $('#link_booking_time_suggest_'+id);
		var booking_time_form = $('#form_booking_time_'+id);
		var booking_time_container = $('#booking_time_suggest_container_'+id);

		booking_time_link.click(function() {			
			booking_time_container.show();
		});

		// Validate
		page.requestBookingTimeFormFields.validate(booking_time_form)
			.action('bluebell:on_update_request_booking_time')
			.update('#p_request_booking_time_'+id, 'request:booking_time')
			.afterUpdate(function() {
				page.bindBookingTime(id);
			});
	};

	return page;
}(Page || {}, jQuery));