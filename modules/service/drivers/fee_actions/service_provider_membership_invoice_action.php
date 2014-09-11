<?php

class Service_Provider_Membership_Action extends Payment_Fee_Action_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Membership Fee',
			'description' => 'Add membership Fee.'
		);
	}    

	// Returns true if action is applicable to the selected event
	public function is_applicable($host)
	{
		if (!$host->event_obj)
			return true;

		if ($host->event_obj instanceof Service_Provider_Update_Event)
			return true;

		return false;
	}

	public function build_config_form($host)
	{

		
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
#		$provider = $options->quote;
		#print_r($options);

		$plan_id = $options->plan_id;
		
		$provider = $options->provider;
		
		$new_plan = Service_Plan::find_by_id($plan_id);
		
		$invoice = Payment_Invoice::raise_invoice($provider->user);
		$fee_description = 'Activation of Membership '.$new_plan->name;#($host->description) ? $host->description : $host->name;
		$invoice->add_line_item(1, $fee_description, $new_plan->price);#$this->calculate_fee($host, $quote));

		$invoice->save();
		
		 // Redirect to pay page
        $pay_page = Cms_Page::create()->find($host->pay_page);
        Phpr::$response->redirect(root_url('payment/invoice'.'/'.$invoice->hash));

	}

	// Internal 
	// 
	
	private function calculate_fee($host, $quote)
	{
#		return $quote->price * ((int)$host->percentage_amount/100);
		return '78';
	}

}