<?php

class Bluebell_Remind_Customer_Booking_Template extends Notify_Template_Base
{
	public $required_params = array('quote');

	public function get_info()
	{
		return array(
			'name'=> 'Remind Provider Booking',
			'description' => 'Sent to a customer 24 hours before booking time.',
			'code' => 'bluebell:remind_customer_booking'
		);
	}

	public function get_subject()
	{
		return 'Appointment time reminder';
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
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
