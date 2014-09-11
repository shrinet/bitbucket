<?php

class Bluebell_Quote_Percent_Action extends Payment_Fee_Action_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Invoice Percentage of Quote (Extended)',
			'description' => 'Invoice user for percentage of quoted amount with onsite and materials support.'
		);
	}    

	// Returns true if action is applicable to the selected event
	public function is_applicable($host)
	{
		if (!$host->event_obj)
			return true;

		if ($host->event_obj instanceof Service_Quote_Status_Event)
			return true;

		return false;
	}

	public function build_config_form($host)
	{
		$host->add_field('percentage_amount', 'Percentage amount', 'full', db_float, 'Action')
			->comment('Please specify a percentage of the quote value to invoice this user', 'above')
			->validation()->required('Please specify an amount');

		$host->add_field('exclude_materials', 'Exclude cost of materials', 'full', db_bool, 'Action')
			->comment('Tick this box to not include any costs for supplies and materials in the invoice amount')
			->validation();
	}

	// Set defaults for configurable fields
	public function init_fields_data($host)
	{
		if ($host->exclude_materials === null) 
			$host->exclude_materials = true;
	}

	public function trigger($host, $options, $params=array())
	{
		$quote = $options->quote;

		// No change in status, abort
		if (isset($options->previous_status) 
			&& $options->previous_status 
			&& $quote->status_code == $previous_status->code)
			return;

		// Populate owner details
		$controller = Cms_Controller::get_instance();        

		// Generate invoice
		$invoice = Payment_Invoice::raise_invoice($controller->user);

		// User not logged in
		if (!$controller->user)
			$invoice->billing_email = post_array('User', 'email');

		// Add line item
		$fee_description = ($host->description) ? $host->description : $host->name;
		$invoice->add_line_item(1, $fee_description, $this->calculate_fee($host, $quote));

		$invoice->save();
	}

	// Internal 
	// 
	
	private function calculate_fee($host, $quote)
	{
		return $quote->price * ((int)$host->percentage_amount/100);
	}

}