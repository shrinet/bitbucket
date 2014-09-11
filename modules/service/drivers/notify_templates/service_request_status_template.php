<?php

class Service_Request_Status_Template extends Notify_Template_Base
{
	public $required_params = array('request', 'provider');

	public function get_info()
	{
		return array(
			'name'=> 'Update Request Status',
			'description' => 'Sent to a user when their service request is marked as complete.',
			'code' => 'service:request_status'
		);
	}

	public function get_subject()
	{
		return 'Your request status has been updated';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$provider->set_notify_vars($template, 'provider_');
		$request->set_notify_vars($template, 'request_');

		$template->set_vars(array());

		$template->add_recipient($user);
	}
}