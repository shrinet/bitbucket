<?php

class Service_Question_Event extends Payment_Fee_Event_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Question Asked',
			'description' => 'Provider asks a question about a Request.'
		);
	}        
}
