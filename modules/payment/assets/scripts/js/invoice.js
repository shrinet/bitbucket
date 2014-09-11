$(window).on('phpr_recordfinder_update', function() {
	$('#Payment_Invoice_user_id').phpr().post('on_user_change', { 
		loadIndicator: {show: false}, 
		update:'multi' 
	}).send();
})

jQuery(document).ready(function($){  
	$('#Payment_Invoice_billing_country_id').bind('change', function(){
		
		$('#Payment_Invoice_billing_country_id').phpr().post('on_update_state_list', {
			loadIndicator: {show: false}
		}).send();

	});
});