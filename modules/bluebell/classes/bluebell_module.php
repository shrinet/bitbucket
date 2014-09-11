<?php

class Bluebell_Module extends Core_Module_Base
{
	protected function set_module_info()
	{
		return new Core_Module_Detail(
			"Bluebell",
			"Bluebell specific functionality",
			"Scripts Ahoy!",
			"http://scriptsahoy.com/"
		);
	}

	public function subscribe_events()
	{
		Phpr::$events->add_event('user:on_extend_user_model', $this, 'extend_user_model');

		// Requests
		Phpr::$events->add_event('service:on_extend_service_requests_table', $this, 'extend_requests_table');
		Phpr::$events->add_event('service:on_extend_request_model', $this, 'extend_request_model');
		Phpr::$events->add_event('service:on_extend_request_form', $this, 'extend_request_form');
		Phpr::$events->add_event('service:on_get_request_field_options', $this, 'get_request_field_options');

		// Providers
		Phpr::$events->add_event('service:on_extend_service_providers_table', $this, 'extend_providers_table');
		Phpr::$events->add_event('service:on_extend_provider_form', $this, 'extend_provider_form');
		Phpr::$events->add_event('service:on_extend_provider_model', $this, 'extend_provider_model');
		Phpr::$events->add_event('service:on_after_update_provider', $this, 'after_update_provider');

		// Quotes
		Phpr::$events->add_event('service:on_extend_service_quotes_table', $this, 'extend_quotes_table');
		Phpr::$events->add_event('service:on_extend_quote_model', $this, 'extend_quote_model');
		Phpr::$events->add_event('service:on_extend_quote_form', $this, 'extend_quote_form');
		Phpr::$events->add_event('service:on_after_update_quote', $this, 'after_update_quote');
		
		// Categories
		Phpr::$events->add_event('service:on_extend_service_categories_table', $this, 'extend_categories_table');
		Phpr::$events->add_event('service:on_extend_category_model', $this, 'extend_category_model');
		Phpr::$events->add_event('service:on_extend_category_form', $this, 'extend_category_form');

		// Config form
		Phpr::$events->add_event('service:on_extend_config_form', $this, 'extend_config_form');
		Phpr::$events->add_event('service:on_init_config_data', $this, 'init_config_data');

		// Location module
		Phpr::$events->add_event('location:after_geocode_from_address', $this, 'location_after_geocode_from_address');
	}

	// Extend location module
	// 
	public function location_after_geocode_from_address($result)
	{
		if (!$result->zipcode)
		{
			$postcode = Bluebell_Geocode::get_postcode($result->city, $result->countryCode);
			if (isset($postcode->postalCodes[0]->postalcode))
				$result->zipcode = $postcode->postalCodes[0]->postalcode;
		}
	}

	// Cron
	// 

	public function subscribe_crontab()
	{
		$crontab = array();
		$crontab['process_directory'] = array('method'=>'cron_process_directory', 'interval' => 5);
		
		if (Phpr::$config->get('DEMO_MODE'))
			$crontab['refresh_demo'] = array('method'=>'cron_refresh_demo', 'interval' => (60 * 24 * 7));

		$crontab['close_booked_requests'] = array('method'=>'cron_close_booked_requests', 'interval' => 60);

		return $crontab;
	}

	public function cron_refresh_demo()
	{
		Cms_Demo::refresh('bluebell');
		return true;
	}

	public function cron_process_directory()
	{
		Bluebell_Directory_City::process_directory(10);
		return true;
	}

	public function cron_close_booked_requests()
	{
		$requests = Service_Request::create()
			->apply_status(Bluebell_Request::status_booked)
			->join('service_quotes', 'service_quotes.request_id = service_requests.id')
			->join('service_quote_statuses', 'service_quote_statuses.id = service_quotes.status_id')
			->where('service_quote_statuses.code=?', Service_Quote_Status::status_accepted)
			->where('service_quotes.start_at<=?', Phpr_DateTime::gmt_now()->to_sql_datetime());

		foreach ($requests->find_all() as $request)
		{
			Service_Request_Manager::close_request($request);
		}

		return true;
	}

	// Extend categories
	// 

	public function extend_categories_table($table)
	{
		$table->column('form_id', db_number)->index();
	}

	public function extend_category_model($model)
	{
        $model->add_relation('belongs_to', 'form', array('class_name'=>'Builder_Form', 'foreign_key'=>'form_id'));
	}

	public function extend_category_form($model)
	{
		$model->define_multi_relation_column('form', 'form', 'Form', '@name')->invisible();
        $model->add_form_field('form')->tab('Form')->empty_option('<no form selected>');
	}

	// Extend config
	// 

	public function extend_config_form($config)
	{
		$config->add_field('min_labor_price', 'Minimum Labor Price', 'right', db_number)->tab('Quotes')->comment('Set the minimum labor price allowed by providers.', 'above');
		$config->find_column_definition('min_labor_price')->currency(true)->validation()->required();
		
		$config->add_field('quote_hide_materials', 'Hide Materials Form', 'left', db_bool)->tab('Quotes')->comment('Use this checkbox if you do not wish to display materials on the quote form.', 'above');
		$config->add_field('quote_materials_required', 'Quote materials field is required', 'left', db_bool)->tab('Quotes')->comment('Use this checkbox to force providers to specify materials on the quote form.', 'above');
		
		$config->add_field('quote_hide_onsite', 'Hide Onsite Visit Form', 'right', db_bool)->tab('Quotes')->comment('Use this checkbox if you do not wish to display the onsite visit section on the quote form.', 'above');
	}

	public function init_config_data($config)
	{
		$config->min_labor_price = 25;
		$config->show_materials_quote = false;
	}

	// Extend user
	//

	public function extend_user_model($user, $context)
	{
		$user->calculated_columns['is_provider'] = "select count(*) from service_providers where user_id = users.id";
		$user->calculated_columns['is_requestor'] = "select count(*) from service_requests where user_id = users.id";
	}

	// Extend Request
	//

	public function extend_requests_table($table)
	{
		$table->column('required_by', db_varchar, 100);
		$table->column('required_type', db_varchar, 100);
		$table->column('firm_start', db_datetime);
		$table->column('firm_end', db_datetime);
		$table->column('firm_alt_start', db_datetime);
		$table->column('firm_alt_end', db_datetime);
		$table->column('is_remote', db_bool);
		$table->column('custom_form_data', db_text);
	}

	public function extend_request_model($request, $context)
	{
		$request->define_column('required_by', 'Required By', 'full')->default_invisible();
		$request->define_column('required_type', 'Flexible')->default_invisible();

		$request->define_column('firm_start', 'Specific Time Start')->time_format('%I:%M %p')->default_invisible();
		$request->define_column('firm_end', 'Specific Time End')->time_format('%I:%M %p')->default_invisible();

		$request->define_column('firm_alt_start', 'Alternate Specific Time Start')->time_format('%I:%M %p')->default_invisible();
		$request->define_column('firm_alt_end', 'Alternate Specific Time End')->time_format('%I:%M %p')->default_invisible();

		$request->define_column('is_remote', 'Remote Job')->default_invisible();
		$request->define_column('custom_form_data', 'Custom Form Details')->invisible();
	}

	public function extend_request_form($request, $context)
	{
		if ($context != "preview")
		{
			$request->add_form_field('required_by')->display_as(frm_dropdown)->tab('Requirements');
			$request->add_form_field('required_type')->display_as(frm_dropdown)->tab('Requirements')->empty_option(__('Not specified', true))->comment('When required by is flexible');

			$request->add_form_field('firm_start', 'left')->tab('Requirements')->empty_option(__('Not specified', true));
			$request->add_form_field('firm_end', 'right')->tab('Requirements')->empty_option(__('Not specified', true));

			$request->add_form_field('firm_alt_start', 'left')->tab('Requirements')->empty_option(__('Not specified', true));
			$request->add_form_field('firm_alt_end', 'right')->tab('Requirements')->empty_option(__('Not specified', true));
		}

		$request->add_form_partial(PATH_APP.'/modules/bluebell/partials/request_preview_custom_form_data.htm')->tab('Request');
	}

	public function get_request_field_options($field_name, $current_value)
	{
		if ($field_name == 'required_by')
		{
			return array('flexible'=>__("I'm flexible", true), 'urgent'=>__("Within 48 hours (It's urgent!)", true), 'firm'=>__('At a specific date and time', true));
		}
		else if ($field_name == 'required_type')
		{
			return array('flexible'=>__('anytime',true), 'flexible_week'=>__('this week',true), 'flexible_month'=>__('this month',true));
		}
	}

	// Extend Provider
	//
	
	public function extend_providers_table($table)
	{
		$table->column('role_name', db_varchar);
		$table->column('description_experience', db_text);
		$table->column('description_speciality', db_text);
		$table->column('description_why_us', db_text);
		$table->column('service_codes', db_text);
		$table->column('service_radius', db_number);
	}

	public function after_update_provider($provider) 
	{ 
		Bluebell_Provider_Zip::update_provider($provider); 
		Bluebell_Directory_City::update_provider($provider); 
	}

	public function extend_provider_model($provider, $context)
	{
		$provider->define_column('role_name', 'Role Name')->default_invisible();
		$provider->define_column('description_experience', 'Our Experience')->default_invisible();
		$provider->define_column('description_speciality', 'Our Speciality')->default_invisible();
		$provider->define_column('description_why_us', 'Why us?')->default_invisible();
		$provider->define_column('service_codes', 'Service Area')->default_invisible();
		$provider->define_column('service_radius', 'Service Radius')->default_invisible();

		for ($x = 0; $x < 7; $x++) // Do seven times
		{
			$provider->define_dynamic_column('schedule_'.$x.'_start', Core_Locale::date('A_weekday_'.($x+1)).' - Start time', db_varchar)->default_invisible();
			$provider->define_dynamic_column('schedule_'.$x.'_end', Core_Locale::date('A_weekday_'.($x+1)).' - Finish time', db_varchar)->default_invisible();
		}
	}

	public function extend_provider_form($provider, $context)
	{
		if ($context != "preview")
		{
			$provider->add_form_field('role_name', 'left')->tab('Categories');
			$provider->add_form_field('description_experience')->size('small')->tab('Profile');
			$provider->add_form_field('description_speciality')->size('small')->tab('Profile');
			$provider->add_form_field('description_why_us')->size('small')->tab('Profile');
			$provider->add_form_field('service_codes')->size('small')->tab('Profile')->comment('These are the postal/zip codes that this provider can service.');
			$provider->add_form_field('service_radius', 'left')->tab('Profile');

			for ($x = 0; $x < 7; $x++) // Do seven times
			{
				$provider->add_dynamic_field('schedule_'.$x.'_start', 'left')->tab('Schedule');
				$provider->add_dynamic_field('schedule_'.$x.'_end', 'right')->tab('Schedule');
			}
		}
		else
		{
			//$provider->add_form_field('service_codes')->size('large')->tab('Provider')->comment('These are the postal/zip codes that this provider can service.');
		}
	}

	// Extend Quote
	//

	public function extend_quotes_table($table)
	{
		$table->column('quote_type', db_varchar, 100);
		$table->column('flat_items', db_text);
		$table->column('flat_labor_description', db_text);
		$table->column('flat_labor_price', db_float, array(15,2))->set_default('0.00');
		$table->column('onsite_price_start', db_float, array(15,2))->set_default('0.00');
		$table->column('onsite_price_end', db_float, array(15,2))->set_default('0.00');
		$table->column('onsite_travel_required', db_bool);
		$table->column('onsite_travel_price', db_float, array(15,2))->set_default('0.00');
		$table->column('onsite_travel_waived', db_bool);
	}

	public function after_update_quote($quote) 
	{
		if (!isset($quote->fetched['status_id']) || $quote->status_id == $quote->fetched['status_id'])
			return;

		if ($quote->status_id != Service_Quote_Status::find_id_from_code(Service_Quote_Status::status_accepted))
			return;

		// If a quote status shifts to accepted, then book the job
		$quote->request->set_status(Bluebell_Request::status_booked);
		$quote->request->save();

		// Send notifications
		Notify::trigger('bluebell:new_customer_booking', array('quote'=>$quote));
		Notify::trigger('bluebell:new_provider_booking', array('quote'=>$quote));
	}

	public function extend_quote_model($quote, $context)
	{
		$quote->define_column('quote_type', 'Quote Type')->default_invisible();

		// Flat price
		$quote->define_column('flat_items', 'Price of Materials')->default_invisible();
		$quote->define_column('flat_labor_description', 'Labour Description')->default_invisible();
		$quote->define_column('flat_labor_price', 'Labour Price')->default_invisible();

		// Onsite
		$quote->define_column('onsite_price_start', 'Price Range (Start)')->default_invisible();
		$quote->define_column('onsite_price_end', 'Price Range (End)')->default_invisible();
		$quote->define_column('onsite_travel_required', 'Travel Required')->default_invisible();
		$quote->define_column('onsite_travel_price', 'Travel Price')->default_invisible();
		$quote->define_column('onsite_travel_waived', 'Travel Price Waived')->default_invisible();
	}

	public function extend_quote_form($quote, $context)
	{
		// Flat price
		$quote->add_form_field('flat_labor_description','left')->size('tiny')->tab('Flat Rate');
		$quote->add_form_field('flat_labor_price', 'right')->tab('Flat Rate');
		$quote->add_form_field('flat_items')->display_as(frm_widget, array(
			'class'=>'Db_MultiText_Widget', 
			'fields' => array(
				'description' => array('type'=>db_varchar, 'label'=>'Description', 'align'=>'left'),
				'price' => array('type'=>db_float, 'label'=>'Price', 'align'=>'right')
			)
		))->tab('Flat Rate');

		// Onsite
		$quote->add_form_field('onsite_price_start','left')->tab('Onsite');
		$quote->add_form_field('onsite_price_end','right')->tab('Onsite');
		$quote->add_form_field('onsite_travel_required','left')->tab('Onsite');
		$quote->add_form_field('onsite_travel_price','right')->tab('Onsite');
		$quote->add_form_field('onsite_travel_waived','left')->tab('Onsite');
	}
}

