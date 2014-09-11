<?php

class Service_New_Question_Template extends Notify_Template_Base
{
	public $required_params = array('question');

	public function get_info()
	{
		return array(
			'name'=> 'New Question',
			'description' => 'Sent to a user when a provider asks a question about their request.',
			'code' => 'service:new_question'
		);
	}

	public function get_subject()
	{
		return 'Question about your request';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $question->request->user;

		$question->set_notify_vars($template, 'question_');
		$question->request->set_notify_vars($template, 'request_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
