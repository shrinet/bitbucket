<?php

class Service_Testimonial_Ask_Template extends Notify_Template_Base
{
	public $required_params = array('testimonial');

	public function get_info()
	{
		return array(
			'name'=> 'Testimonial Request',
			'description' => 'Message template used when requesting a testimonial.',
			'code' => 'service:testimonial_ask'
		);
	}

	public function get_subject()
	{
		return '{subject}';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$provider = $testimonial->provider;
		$provider->set_notify_vars($template);
		$provider->user->set_notify_vars($template);
		
		$template->set_vars(array(
			'subject' => $testimonial->invite_subject,
			'message' => $testimonial->invite_message,
			'business_name' => $provider->business_name,
			'link' => root_url(sprintf('provide/testimonial/%s/%s', $testimonial->hash, $provider->id), true)
		));

		$template->add_recipient($testimonial);
	}    
}