jQuery(document).ready(function($){	
	$('#Service_Request_country_id').bind('change', function(){

		$('#Service_Request_country_id').phpr().post('on_update_states_list', {
			loadIndicator: { show: false }
		}).send();

	});
});