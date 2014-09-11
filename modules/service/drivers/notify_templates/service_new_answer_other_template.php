<?php

class Service_New_Answer_Other_Template extends Notify_Template_Base
{
	public $required_params = array('question', 'provider');

	public function get_info()
	{
		return array(
			'name'=> 'New Answer to Other Question',
			'description' => "Sent to a provider when a customer answers another provider's question.",
			'code' => 'service:new_answer_other'
		);
	}

	public function get_subject()
	{
		return "Answer to another provider's question";
	}

	public function get_content()
	{
		return file_get_contents($this->get_partial_path('content.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);

		$user = $provider->user;

		$question->set_notify_vars($template, 'question_');
		$question->request->set_notify_vars($template, 'request_');
		$question->answer->set_notify_vars($template, 'answer_');
		$template->set_vars(array());

		$template->add_recipient($user);
	}
}
