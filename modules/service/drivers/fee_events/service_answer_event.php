<?php

class Service_Answer_Event extends Payment_Fee_Event_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Answer Provided',
			'description' => 'User answers a question about their Request.'
		);
	}
}
