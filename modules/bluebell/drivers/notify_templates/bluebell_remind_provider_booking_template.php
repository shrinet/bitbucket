<?php

class Bluebell_Remind_Provider_Booking_Template extends Notify_Template_Base
{
	public $required_params = array('quote');

	public function get_info()
	{
		return array(
			'name'=> 'Remind Provider Booking',
			'description' => 'Sent to a provider 24 hours before booking time.',
			'code' => 'bluebell:remind_provider_booking'
		);
	}

	public function get_subject()
	{
		return 'Appointment time reminder for {request_title} request';
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
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
