<?php

class Bluebell_Suggest_Time_Template extends Notify_Template_Base
{
	public $required_params = array('from_user', 'quote');

	public function get_info()
	{
		return array(
			'name'=> 'Quote time suggestion',
			'description' => 'Sent to other user if a different appointment time is suggested.',
			'code' => 'bluebell:suggest_time'
		);
	}

	public function get_subject()
	{
		return 'Appointment time suggestion';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = ($quote->provider->user == $from_user) ? $quote->request->user : $quote->provider->user;

		$quote->set_notify_vars($template, 'quote_');
		$quote->request->set_notify_vars($template, 'request_');
		$from_user->set_notify_vars($template, 'from_user_');
		$user->set_notify_vars($template, 'user_');

		$template->add_recipient($user);
	}
}
