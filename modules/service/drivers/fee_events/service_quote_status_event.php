<?php

class Service_Quote_Status_Event extends Payment_Fee_Event_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Quote Status Change',
			'description' => 'Quote status is changed by either party.'
		);
	}     

	public function build_config_form($host)
	{
		$host->add_field('status_id', 'Status', 'full', db_float, 'Event')
			->comment('Please specify the quote status.')
			->validation()->required('You must specify a status');

		$form_field = $host->find_form_field('status_id')->display_as(frm_dropdown);
	}

	public function get_status_id_options($host, $key_value = -1)
	{
		$statuses = Service_Quote_Status::create()->find_all()->as_array('name', 'id');
		return $statuses;
	}

	public function trigger($host, $options, $params=array())
	{
		$quote = $options->quote;

		// Do not pass if no quote object or no status id
		if (!isset($options->quote) && !isset($quote->status_id))
			return true;

		// Do not pass if quote status is not as defined above.
		if ($host->status_id != $quote->status_id)
			return true;

		// No change in status, abort
		if (isset($options->previous_status) 
			&& $options->previous_status 
			&& $options->previous_status->code == $quote->status_code)
			return true;
	}
}

