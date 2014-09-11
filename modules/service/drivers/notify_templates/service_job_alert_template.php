<?php

class Service_Job_Alert_Template extends Notify_Template_Base
{
	public $required_params = array('request', 'provider');

	public function get_info()
	{
		return array(
			'name'=> 'Provider Alert',
			'description' => 'Notification sent to a provider when a request matches their skills.',
			'code' => 'service:job_alert'
		);
	}

	public function get_subject()
	{
		return 'Request Notification: {request_title}';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $provider->user;

		if (!$user)
			return;

		$request->set_notify_vars($template, 'request_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
