<?php

class Service_Quote_Percent_Action extends Payment_Fee_Action_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Invoice Percentage of Quote',
			'description' => 'Invoice user for percentage of quoted amount.'
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
		$host->add_field('percentage_amount', 'Percentage amount', 'left', db_float, 'Action')
			->comment('Please specify a percentage of the quote value to invoice this user', 'above')
			->validation()->required('Please specify an amount');
		
		$host->add_field('target_user', 'Target user', 'right', db_varchar, 'Action')
			->comment('Please specify who the fee should be applied to.', 'above')
			->validation()->required('You must specify a target user');

		$form_field = $host->find_form_field('target_user')->display_as(frm_dropdown);
	}


	public function get_target_user_options($host, $key_value = -1)
	{
		return array(
			'active_user' => 'Logged in user',
			'provider' => 'Provider',
			'customer' => 'Customer',
		);
	}


	public function trigger($host, $options, $params=array())
	{
		$quote = $options->quote;

		// Who to charge the fee
		switch ($host->target_user) {
			case 'active_user':
				$controller = Cms_Controller::get_instance();
				$user = $controller->user;
				break;
			
			case 'provider':
				$user = $quote->user;
				break;

			case 'customer':
				$user = $quote->request->user;
				break;
		}

		// Generate invoice
		$invoice = Payment_Invoice::raise_invoice($user);

		// User not found
		if (!$user)
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