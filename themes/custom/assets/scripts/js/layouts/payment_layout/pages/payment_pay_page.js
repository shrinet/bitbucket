var Page = (function(page, $){

	page.constructor = $(function() {
		
		// Editing
		$('#form_invoice_details').fieldEditor({ implode_char: ' ', on_set_field: function() {} });


		// Select country, populate state
		$('#invoice_billing_country_id').countrySelect({ 
			stateElementId: '#invoice_billing_state_id',
			afterUpdate: function(obj) {
				obj.stateElement = $('#invoice_billing_country_id').parent().next().children().first();
			}
		});

	});

	return page;
}(Page || {}, jQuery));

