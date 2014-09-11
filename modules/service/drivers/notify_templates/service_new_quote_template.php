<?php

class Service_New_Quote_Template extends Notify_Template_Base
{
	public $required_params = array('quote');

	public function get_info()
	{
		return array(
			'name'=> 'New Quote',
			'description' => 'Sent to a user when a provider submits a price quote.',
			'code' => 'service:new_quote'
		);
	}

	public function get_subject()
	{
		return 'New quote on your request';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $quote->request->user;

		$quote->request->set_notify_vars($template, 'request_');
		$quote->provider->set_notify_vars($template, 'provider_');
		$quote->set_notify_vars($template, 'quote_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
