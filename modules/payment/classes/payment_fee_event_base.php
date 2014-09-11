<?php

class Payment_Fee_Event_Base
{
	public static $driver_folder = 'fee_events';
	public static $driver_suffix = '_event';

	// Returns information about the event
	public function get_info()
	{
		return array(
			'name' => 'Event',
			'description' => 'Generic event'
		);
	}

	// Define the configurable fields
	public function build_config_form($host)
	{
	}
	
	// Set defaults for configurable fields
	public function init_fields_data($host)
	{
	}

	// Validate configurable fields on submit
	public function validate_settings($host)
	{
	}

	// Logic to perform when event is triggered. Eg: When a user signs up
	// $options can define how this event should behave
	// $params is the data captured from the user    
	// Return "true" to prevent the action from triggering
	public function trigger($host, $options, $params=array())
	{
	}

	// Triggered when an action is complete. Eg: When action is complete.
	public function resolve($host, $promise)
	{
	}

	public static function find_events()
	{
		return Phpr_Driver_Manager::get_class_names('Payment_Fee_Event_Base');
	}
}