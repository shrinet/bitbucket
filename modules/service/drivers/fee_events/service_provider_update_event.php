<?php

class Service_Provider_Update_Event extends Payment_Fee_Event_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Service Provider Update',
			'description' => 'Provider Membership.'
		);
	}
	
	public function build_config_form($host)
	{
#		$host->comment('Please specify the quote status.')
#			->validation()->required('You must specify a status');

#		$form_field = $host->find_form_field('status_id')->display_as(frm_dropdown);
	}
	
	public function trigger($host, $options, $params=array())
	{
//		$provider = $options->quote;
#		print_r($options);
		// Do not pass if no quote object or no status id
/*		if (!isset($options->quote) && !isset($quote->status_id))
			return true;

		// Do not pass if quote status is not as defined above.
		if ($host->status_id != $quote->status_id)
			return true;

		// No change in status, abort
		if (isset($options->previous_status) 
			&& $options->previous_status 
			&& $options->previous_status->code == $quote->status_code)
			return true;*/
	}
}
