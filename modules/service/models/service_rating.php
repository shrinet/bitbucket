<?php

class Service_Rating extends Db_ActiveRecord
{
	public $table_name = 'service_ratings';

	const status_new = 'new';
	const status_approved = 'approved';
	public $moderation_status = 'new';
	public static $moderation_statuses = array(
		'new'=>'New',
		'approved'=>'Approved'
	);

	public $belongs_to = array(        
		'user_from' => array('class_name' => 'User', 'foreign_key'=>'user_from_id'),
		'user_to' => array('class_name' => 'User', 'foreign_key'=>'user_to_id'),
		'provider' => array('class_name' => 'Service_Provider', 'foreign_key'=>'provider_id'),
		'request' => array('class_name' => 'Service_Request', 'foreign_key'=>'request_id')
	);

	public $calculated_columns = array(
		'rating_status' => array('sql'=>"if(service_ratings.moderation_status = 'new', 'New', 'Approved')"),
		'request_title' => array('sql'=>"ifnull(request_calculated_join.title,'Undefined')")
	);

	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
		$this->define_column('moderation_status', 'Moderation Status')->invisible();
		$this->define_column('rating', 'Rating');
		$this->define_column('comment', 'Comment');

		$this->define_relation_column('user_from', 'user_from', 'Author', db_varchar, '@first_name')->validation()->required('Rating must be from someone');
		$this->define_relation_column('user_to', 'user_to', 'User', db_varchar, '@first_name')->validation()->required('Rating must be left for a user');
		$this->define_relation_column('provider', 'provider', 'Provider', db_varchar, '@business_name')->validation();
		$this->define_relation_column('request', 'request', 'Related Request', db_varchar, '@title')->validation()->required('Rating must belong to a request');

		$this->define_column('updated_at', 'Last Updated')->invisible();
		$this->define_column('created_at', 'Created');

		// Extensibility
		$this->defined_column_list = array();
		Phpr::$events->fire_event('service:on_extend_rating_model', $this, $context);
		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public function define_form_fields($context = null)
	{

		$this->add_form_field('moderation_status', 'left')->display_as(frm_dropdown);
		$this->add_form_field('rating','right')->display_as(frm_dropdown)->empty_option('<no rating specified>');

		$this->add_form_field('user_from','left')
			->display_as(frm_record_finder, array(
				'sorting'=>'first_name, last_name, email', 
				'list_columns'=>'username,first_name,last_name,email,guest,created_at', 
				'search_prompt'=>'Find user by username, name or email', 
				'form_title'=>'Find User',
				'display_name_field'=>'name',
				'display_description_field'=>'email',
				'prompt'=>'Click Find to find a user'));

		$this->add_form_field('request','right')
			->display_as(frm_record_finder, array(
				'sorting'=>'title', 
				'list_columns'=>'id,title,created_at', 
				'search_prompt'=>'Find request by id, title or description', 
				'search_fields'=> array('@id', '@title', '@description'),
				'form_title'=>'Find Request',
				'display_name_field'=>'title',
				'display_description_field'=>null,
				'prompt'=>'Click Find to find a request'));
		
		/* Removed-- in context of Service Providers this is populated automatically (detect_recipient)
		$this->add_form_field('user_to','left')->
			display_as(frm_record_finder, array(
				'sorting'=>'first_name, last_name, email', 
				'list_columns'=>'first_name,last_name,email,guest,created_at', 
				'search_prompt'=>'Find user by name or email', 
				'form_title'=>'Find User',
				'display_name_field'=>'name',
				'display_description_field'=>'email',
				'prompt'=>'Click Find to find a user'));
		*/

		/* Removed-- in context of Service Providers this is populated automatically (detect_recipient)
		$this->add_form_field('provider','right')->
			display_as(frm_record_finder, array(
				'sorting'=>'business_name',
				'list_columns'=>'business_name',
				'search_prompt'=>'Find provider by business name',
				'form_title'=>'Find Provider',
				'display_name_field'=>'business_name',
				'display_description_field'=>'email',
				'prompt'=>'Click the Find button to find a provider'));
		*/

		$this->add_form_field('comment','full')->size('small');

		// Extensibility
		Phpr::$events->fire_event('service:on_extend_rating_form', $this, $context);
		foreach ($this->api_added_columns as $column_name)
		{
			$form_field = $this->find_form_field($column_name);
			if ($form_field)
				$form_field->options_method('get_added_field_options');
		}
	}

	// Events
	// 

	public function before_validation_on_create($session_key = null)
	{
		if (!$this->user_to_id)
			$this->detect_recipient();
	}

	public function after_modify($operation, $session_key)
	{
		Service_Provider::update_rating_fields($this->provider_id);
	}

	public function before_create($session_key=null)
	{
		$is_rated = self::is_rated($this->request_id, $this->user_from_id, $this->user_to_id, false);
		if ($is_rated)
			throw new Phpr_ApplicationException('You may only submit one rating per request');
	}

	public static function is_rated($request_id, $user_from_id, $user_to_id, $return_self=true)
	{
		$bind = array(
			'request_id' => $request_id,
			'user_from_id' => $user_from_id,
			'user_to_id' => $user_to_id
		);

		$rating = self::create()->where('request_id=:request_id and user_from_id=:user_from_id and user_to_id=:user_to_id', $bind);
		return ($return_self) ? $rating->find() : $rating->get_row_count();
	}

	// Moderation
	// 

	public function get_moderation_status_options($key_value = -1)
	{
		return self::$moderation_statuses;
	}

	public function get_rating_options($key_value = -1)
	{
		return array(
			1 => '1 star',
			2 => '2 stars',
			3 => '3 stars',
			4 => '4 stars',
			5 => '5 stars',
		);
	}
	
	public function approve()
	{
		$bind = array(
			'id' => $this->id,
			'status' => self::status_approved
		);
		Db_Helper::query("update service_ratings set moderation_status=:status where id=:id", $bind);

		if ($this->provider_id)
			Service_Provider::update_rating_fields($this->provider_id);
	}

	// Service methods
	// 

	public function set_notify_vars(&$template, $prefix='')
	{
		$template->set_vars(array(
			$prefix.'stars'   => $this->rating,
			$prefix.'rating'  => $this->rating,
			$prefix.'comment' => $this->comment,
		));
	}

	// Helper methods
	// 


	// Auto detect rating by request
	private function detect_recipient()
	{
		if (!$this->request && $this->request_id)
			$this->request = Service_Request::create()->find($this->request_id);

		// Recipient is request owner
		if ($this->user_from_id != $this->request->user_id)
			$this->user_to_id = $this->request->user_id;
		else
		{
			if (!$this->provider && $this->provider_id)
				$this->provider = Service_Provider::create()->find($this->provider_id);

			// Recipient is provider
			if ($this->user_from_id != $this->provider->user_id)
				$this->user_to_id = $this->provider->user_id;
		}     
	}

}
