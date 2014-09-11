<?php

class Service_Request_Submit_Event extends Payment_Fee_Event_Base
{
	public function get_info()
	{
		return array(
			'name' => 'Service Request Submit',
			'description' => 'User submits a new Service Request.'
		);
	}
}
