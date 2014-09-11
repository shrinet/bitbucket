<?php

class Service_New_Rating_Template extends Notify_Template_Base
{
	public $required_params = array('rating');

	public function get_info()
	{
		return array(
			'name'=> 'New Rating',
			'description' => 'Sent to a user when a rating has been submitted about them.',
			'code' => 'service:new_rating'
		);
	}

	public function get_subject()
	{
		return 'Rating/review submitted about you';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user_to = $rating->user_to;

		$rating->set_notify_vars($template, 'rating_');
		$rating->request->set_notify_vars($template, 'request_');
		$rating->user_from->set_notify_vars($template, 'user_');
		
		$template->set_vars(array());

		$template->add_recipient($user_to);
	}
}