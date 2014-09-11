<?php

class Service_New_Answer_Template extends Notify_Template_Base
{
	public $required_params = array('question');

	public function get_info()
	{
		return array(
			'name'=> 'New Answer to Question',
			'description' => 'Sent to a provider when their question has been answered.',
			'code' => 'service:new_answer'
		);
	}


	public function get_subject()
	{
		return 'Answer to your question';
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $question->provider->user;

		$question->set_notify_vars($template, 'question_');
		$question->request->set_notify_vars($template, 'request_');
		$question->answer->set_notify_vars($template, 'answer_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
