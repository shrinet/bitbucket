<?php

class Bluebell_New_Provider_Booking_Template extends Notify_Template_Base
{
	public $required_params = array('quote');

	public function get_info()
	{
		return array(
			'name'=> 'New Provider Booking',
			'description' => 'Sent to a provider when a job has been booked.',
			'code' => 'bluebell:new_provider_booking'
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

		$user = $quote->provider->user;

		$quote->set_notify_vars($template, 'quote_');
		$quote->request->set_notify_vars($template, 'request_');
		$quote->request->user->set_notify_vars($template, 'user_');
		$template->set_vars(array(
			'quote_summary' => Bluebell_Quote::price_summary($quote)
		));

		$template->add_recipient($user);
	}
}
