var Page = (function(page, $){

	page.constructor = $(function() {
		page.bindMessageLink();
		page.bindSearchForm();
		page.bindDeleteLink();
	});

	page.bindMessageLink = function() {
		$('#p_message_panel .message-link').live('click', function() {
			window.location = $(this).data('url');
		});
	}

	page.bindSearchForm = function() {
		$('#form_message_search').submit(function() {
			return $('#form_message_search').phpr().post()
				.action('user:on_search_messages')
				.update('#p_message_panel', 'messages:message_panel')
				.send();
		});
	}

	page.bindDeleteLink = function() {
		$('#p_message_panel').on('click', '.link-delete', function() {
			var message_id = $(this).data('message-id');
			$.phpr.post()
				.action('user:on_delete_message')
				.data('message_id', message_id)
				.update('#p_message_panel', 'messages:message_panel')
				.send();
		});
	}

	return page;
}(Page || {}, jQuery));
