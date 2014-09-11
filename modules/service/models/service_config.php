<?php

class Service_Config extends Core_Settings_Base
{
	public $record_code = 'service_config';

	protected $api_added_columns = array();

	public static function create()
	{
		$config = new self();
		return $config->load();
	}   
	
	protected function build_form()
	{
		$this->add_field('requests_auto_approve', 'Auto Approve Requests', 'full', db_bool)->display_as(frm_onoffswitcher)->tab('Requests')->comment('Enable if you wish to automatically approve new requests.', 'above');
		$this->add_field('request_default_length', 'Default Auction Length (Days)', 'left', db_number)->tab('Requests')->comment('Enter the number of days a request should remain open for bidding');
		$this->add_field('request_max_bids', 'Request Maximum Bids', 'right', db_number)->tab('Requests')->comment('The maximum amount of quotes a request recieves before being closed (Leave blank for unlimited)');

		// Extensibility
		$this->defined_column_list = array();
		Phpr::$events->fire_event('service:on_extend_config_form', $this);
		$this->api_added_columns = array_keys($this->defined_column_list);        
	}

	protected function init_config_data()
	{
		$this->requests_auto_approve = true;
		$this->request_default_length = 7;
		$this->request_max_bids = null;

		Phpr::$events->fire_event('service:on_init_config_data', $this);
	}

	public function validate_config_on_save()
	{
		Phpr::$events->fire_event('service:on_validate_config_data', $this);
	}

	public function is_configured()
	{
		return true;
	}
}