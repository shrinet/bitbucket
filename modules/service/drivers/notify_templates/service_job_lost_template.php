<?php

class Service_Job_Lost_Template extends Notify_Template_Base
{
	public $required_params = array('request', 'provider');

	public function get_info()
	{
		return array(
			'name' => 'Provider Lost a Job',
			'description' => 'Notification sent to a provider when they have lost a request.',
			'code' => 'service:job_lost'
		);
	}

	public function get_subject()
	{
		return 'Your quote was not chosen.';
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
