function makeProvidersSortable() {
	var providerList = jQuery('#group_providers_list');
	if (providerList.length > 0) {
		providerList.sortableList({
			handler: 'onSetOrders',
			handleClass: '.sort-handle',
			inputIds: 'provider_id',
			inputOrders: 'provider_order',
			onDragComplete: providersUpdateOrders
		});
	}
}

function providersUpdateOrders(sortable_list_orders) {
	jQuery('#group_providers_list').children().each(function(index, el) {
		var element = jQuery(this);
		var order_input = element.find('input.provider_order');
		if (order_input) {
			if (index <= sortable_list_orders.length-1)
				order_input.val(sortable_list_orders[index]);
		}
		
		if (index % 2)
			element.addClass('even');
		else
			element.removeClass('even');
	})
}

jQuery(document).ready(function($) { 
	makeProvidersSortable();
})