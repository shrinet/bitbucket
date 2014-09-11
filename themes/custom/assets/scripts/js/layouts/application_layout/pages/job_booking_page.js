var Page = (function(page, $){

	var _map = null,
		_address = null;

	page.constructor = $(function() {

		// Low priority load
		$(window).load(page.bindAddressMap);

		// Init others
		page.bindQuestionAnswers();
		page.bindBookingTime();
		page.bindJobCancel();
		page.bindRatingForm();
		page.bindConversation();
        page.bindRequestRelease();
	});

	page.bindQuestionAnswers = function() {
		$('#job_questions span.answer').hide();
		$('#job_questions a.question').bind('click', function() {
			$('#job_questions span.answer').hide();
			$(this).next().show();
		})
	}

	page.bindAddressMap = function() {
		_map = $('#map_booking_address');
		var address = $('#booking_address');
		var address_string = address.text();

		if (address.length==0 || _map.length==0)
			return;

		_map.gmap({start_address: address_string, disable_control: true})
			.gmap('addMarkerFromAddressString', address_string, null, { 
				zoom: 9,
				callback: function(address){
					_address = address;
				} 
			});

		// Auto align map to center
		$(window).bind('resize', function() {
			_map.gmap('alignToAddressObject', _address)
		});

	}

	// Cancel job
	page.bindJobCancel = function() {
		$('#popup_job_cancel').popup({ trigger: '#link_job_cancel' });

		$('#button_job_cancel').click(function() {
			var quoteId = $(this).data('quote-id');
			$.phpr.post().action('bluebell:on_cancel_booking').data('quote_id', quoteId).send();
		});

	}

    //Request Release
    page.bindRequestRelease = function(){
        $('#popup_request_release').popup({trigger:'#request_escrow'});

        $('#button_request_release').click(function(){
            var escrowId = $(this).data('escrow-id');
            $.phpr.post().action('payment:on_request_release').data('escrow_id', escrowId).send();
        });
    }

	// Leave a rating
	page.bindRatingForm = function() {
		if (!page.jobRatingFormFields)
			return;

		page.jobRatingFormFields.validate('#form_rating')
			.action('service:on_create_rating')
			.update('#p_job_rating_form', 'job:rating_form');
	}

	// Conversation
	page.bindConversation = function() {
		page.controlConversationFormFields.validate('#form_conversation')
			.action('bluebell:on_send_quote_message')
			.update('#form_conversation', 'control:conversation');
	};

	// Suggest booking time
	page.bindBookingTime = function() {
		var booking_time_link = $('#link_booking_time_suggest'),
			booking_time_form = $('#form_booking_time'),
			booking_time_container = $('#booking_time_suggest_container');

		booking_time_link.click(function() {
			booking_time_container.show();
		});

		// Validate
		if (page.jobBookingTimeFormFields) {
			page.jobBookingTimeFormFields.validate(booking_time_form)
				.action('bluebell:on_update_request_booking_time')
				.update('#p_job_booking_time', 'job:booking_time')
				.afterUpdate(function() {
					page.bindBookingTime();
				});
		}
	}

	return page;
}(Page || {}, jQuery));
