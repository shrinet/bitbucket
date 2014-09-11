<?php

class Bluebell_New_Customer_Booking_Template extends Notify_Template_Base
{
	public $required_params = array('quote');

	public function get_info()
	{
		return array(
			'name'=> 'New Customer Booking',
			'description' => 'Sent to a customer when their request has been booked.',
			'code' => 'bluebell:new_customer_booking'
		);
	}

	public function get_subject()
	{
		return 'Job has been scheduled!';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $quote->request->user;

		$quote->set_notify_vars($template, 'quote_');
		$quote->request->set_notify_vars($template, 'request_');
		$quote->provider->set_notify_vars($template, 'provider_');
		$quote->provider->user->set_notify_vars($template, 'provider_');
		$template->set_vars(array(
			'quote_summary' => Bluebell_Quote::price_summary($quote)
		));

		$template->add_recipient($user);
	}
}
