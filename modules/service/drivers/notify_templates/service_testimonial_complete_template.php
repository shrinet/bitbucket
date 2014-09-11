<?php

class Service_Testimonial_Complete_Template extends Notify_Template_Base
{
	public $required_params = array('testimonial', 'provider');

	public function get_info()
	{
		return array(
			'name'=> 'Testimonial Complete',
			'description' => 'Message template used when a person has provided a testimonial for a provider.',
			'code' => 'service:testimonial_complete'
		);
	}

	public function get_subject()
	{
		return '{name} wrote a testimonial about you!';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$provider->set_notify_vars($template);
		$provider->user->set_notify_vars($template);
		
		$template->set_vars(array(
			'name' => $testimonial->name,
			'email' => $testimonial->email,
			'location' => $testimonial->location,
			'comment' => $testimonial->comment,
			'link' => root_url(sprintf('provide/manage/%s', $provider->id), true)
		));

		$template->add_recipient($provider->user);
	}    
}