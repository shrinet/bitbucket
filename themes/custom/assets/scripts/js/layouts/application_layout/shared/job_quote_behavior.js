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
 * Job quote behavior
 */

var Page = (function(page, $){

	var _message_element,
		_onsite_element,
		_flatrate_element;

	page.bindQuoteMessageUI = function(element) {
		_message_element = $(element);
		page.bindPersonalMessage();
	};
	
	page.bindQuoteOnSiteUI = function(element) {
		_onsite_element = $(element);
		page.bind_onsite_travel();
	};

	page.bindQuoteFlatRateUI = function(element) {
		_flatrate_element = $(element);
		page.bindFlatRateAddItem();
		page.bindFlatRateUpdateTotal();
	};

	// Onsite
	page.bind_onsite_travel = function() {

		if ($('#quote_onsite_travel_required_yes').is(':checked'))
			$('#panel_travel_fee').show();

		$('#quote_onsite_travel_required_yes').change(function() {
			if ($(this).is(':checked'))
				$('#panel_travel_fee').show();
		});
		$('#quote_onsite_travel_required_no').change(function() {

			if ($(this).is(':checked'))
				$('#panel_travel_fee').hide();
		});

	};

	// Flat rate
	page.bindFlatRateAddItem = function() {

		// Set up shell for duplication
		var item_table = _flatrate_element.find('.item_table');
		var shell = item_table.find('tr.shell').hide();
		page.flatRatePopulate();

		// Create empty set for styling alternate rows
		item_table.prepend('<tr class="empty" style="display:none"><td colspan="'+shell.children('td').length+'">&nbsp;</td></tr>');
	};

	page.flatRatePopulate = function() {

		if (!$('#quote_flat_items').length)
			page.clickFlatRateAddItem();

		var data = $('#quote_flat_items').val();
		if ($.trim(data) != "") {
			var result = $.parseJSON(data);
			$.each(result, function(key, val){
			   page.flatRateAddItem(val.description, val.price);
			});
		}
		else {
			page.clickFlatRateAddItem();
		}

	};

	page.clickFlatRateAddItem = function() {		
		var item_table = _flatrate_element.find('.item_table');
		var new_item = item_table.find('tr.shell').clone().removeClass('shell').show();
		item_table.append(new_item);
		return new_item;
	};

	page.flatRateAddItem = function(line_item, price) {
		var new_item = page.clickFlatRateAddItem();
		new_item.find('td.line_item input').val(line_item);
		new_item.find('td.price input').val(price);
	};

	page.bindFlatRateUpdateTotal = function() {
		_flatrate_element.find('td.price input').live('change', function() {
			var fields = _flatrate_element.find('input');
			var value = page.flatRateCalculateTotal(fields);
			$.phpr.post()
				.action('cms:on_currency')
				.loadIndicator(false)
				.data('value', value)
				.success(function(response){
					$('#total_cost_value').text(response.html);
				})
				.send();
		});
	};

	page.flatRateCalculateTotal = function(fields) {

		var total = 0;
		$.each(fields, function(k,v){
			var val = parseFloat($(this).val());
			if (val)
				total += val;
		});

		return total;
	};

	// Personal message
	page.bindPersonalMessage = function() {
		var final_comment = _message_element.find('.final_comment:first');
		
		if (!final_comment.hasClass('use_message_builder'))
			return;

		var greeting = _message_element.find('.message_greeting');
		var description = _message_element.find('.message_description');
		var closer = _message_element.find('.message_closer');
		var parting = _message_element.find('.message_parting');
		_message_element.find('input,select,textarea').change(function() {

			var comment = "";
			comment += page.helperStripHtml(greeting, greeting.find('select').val()) + "\n\n";
			comment += description.find('textarea').val() + "\n\n";
			comment += page.helperStripHtml(closer, closer.find('select').val()) + "\n\n";
			comment += page.helperStripHtml(parting, parting.find('select').val());
			final_comment.val(comment);

		});

	};

	page.helperStripHtml = function(obj, param) {
		var text = $.trim(obj.clone().find('> select').replaceWith('%s').end().find('> *').remove().end().html());
		return text.replace('%s', param);
	};

	return page;
}(Page || {}, jQuery));