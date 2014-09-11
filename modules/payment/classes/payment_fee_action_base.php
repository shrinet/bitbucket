<?php

class Payment_Fee_Action_Base
{
	public static $driver_folder = 'fee_actions';
	public static $driver_suffix = '_action';

	// Returns information about the action
	public function get_info()
	{
		return array(
			'name' => 'Action',
			'description' => 'Generic action'
		);
	}

	// Returns true if action is applicable to the selected event
	public function is_applicable($host)
	{
		return true;
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

	// Logic to perform when action is triggered. Eg: Generate an invoice
	// $options can define how this action should behave
	// $params is the data captured from the user
	public function trigger($host, $options, $params=array())
	{
	}

	// Triggered when an action is complete. Eg: When invoice is paid.    
	public function resolve($host, $promise)
	{
	}

	public static function find_actions()
	{
		return Phpr_Driver_Manager::get_class_names('Payment_Fee_Action_Base');
	}
}