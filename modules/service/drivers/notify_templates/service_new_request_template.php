<?php

class Service_New_Request_Template extends Notify_Template_Base
{
	public $required_params = array('request');

	public function get_info()
	{
		return array(
			'name'=> 'New Request',
			'description' => 'Sent to a user when they submit a new service request.',
			'code' => 'service:new_request'
		);
	}

	public function get_subject()
	{
		return 'Your request has been submitted';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $request->user;

		$request->set_notify_vars($template, 'request_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
