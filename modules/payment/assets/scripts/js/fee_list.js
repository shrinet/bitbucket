jQuery(document).ready(function($) { 
	feeBindSort();
});

function feeAfterDrag() {
	var list = jQuery('#fee_list');
	var items = list.find('> li');
	var last_index = items.length - 1;
	
	items.each(function(index, val) {
		var item = jQuery(this);
		item.css('z-index', 1000 + last_index - index);
		
		if (index == 0)
			item.removeClass('last').addClass('first');

		if (index == last_index)
			item.removeClass('first').addClass('last');

		if (index != last_index && index != 0)
			item.removeClass('first').removeClass('last');
	});
}

function fee_toggle(element, fee_id) {
	var new_status_value = jQuery(element).closest('li').hasClass('collapsed');
	
	$(element).phpr().post('on_set_fee_collapse_status', {
		loadIndicator: { show: false },
		data: { 
			new_status: (new_status_value) ? 0 : 1, 
			fee_id: fee_id
		}
	}).send();
	
	if (new_status_value)
		jQuery(element).closest('li').removeClass('collapsed');
	else
		jQuery(element).closest('li').addClass('collapsed');
		
	return false;
}

function feeBindSort() {
	if (jQuery('#fee_list').length > 0) {
		var fee_list = jQuery('#fee_list');
		fee_list.sortableList({
			handler: 'on_set_fee_orders', 
			inputOrders: 'fee_order', 
			inputIds: 'fee_id', 
			handleClass: '.drag_handle',
			onDragComplete: feeAfterDrag
		});
	}
}

function fee_delete(element, fee_id) {
	$(element).phpr().post('on_delete_fee', {
		confirm: 'Do you really want to delete this fee?',
		customIndicator: LightLoadingIndicator,
		update: '#fee_list_container',
		error: popupAjaxError,
		data: {fee_id: fee_id},
		afterUpdate: feeBindSort
	}).send();
	
	return false;
}