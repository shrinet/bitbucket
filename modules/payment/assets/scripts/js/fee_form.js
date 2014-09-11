function bind_action_event() {
   var action_select = jQuery('#Payment_Fee_action_class_name');
	if (action_select.length > 0) {
		action_select.not('.fee_binded').addClass('fee_binded').on('change', function(){
			
			action_select.phpr().post('on_update_action', {
				loadIndicator: {
					hideOnSuccess: true
				},
				update: 'multi',
				afterUpdate: bind_action_event
			}).send();

		});
	}

   var event_select = jQuery('#Payment_Fee_event_class_name');
	if (event_select.length > 0) {
		event_select.not('.fee_binded').addClass('fee_binded').on('change', function(){

			event_select.phpr().post('on_update_action', {
				loadIndicator: {
					hideOnSuccess: true
				},
				update: 'multi',
				afterUpdate: bind_action_event
			}).send();

		});
	}
}

jQuery(document).ready(function($) { 
	bind_action_event();
});
