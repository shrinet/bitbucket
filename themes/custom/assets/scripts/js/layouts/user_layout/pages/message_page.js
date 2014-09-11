var Page = (function(page, $){

	page.constructor = $(function() {

		// Autogrow textarea
		$('#message_reply_message').autogrow();

		// Submit reply
		Page.messagesReplyFormFields.validate('#form_message_reply').action('bluebell:on_send_message')
			.update('#p_messages_thread', 'messages:thread')
			.update('#p_messages_reply_form', 'messages:reply_form');

	});

	return page;
}(Page || {}, jQuery));
