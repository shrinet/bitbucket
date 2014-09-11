<?php

class Service_Job_Invite_Template extends Notify_Template_Base
{
	public $required_params = array('request', 'provider');

	public function get_info()
	{
		return array(
			'name'=> 'Provider Invitation',
			'description' => 'Notification sent to a provider when a user invites them to their request.',
			'code' => 'service:job_invite'
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
