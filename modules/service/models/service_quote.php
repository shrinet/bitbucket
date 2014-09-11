<?php

class Service_Quote extends Db_ActiveRecord
{
	public $implement = 'Db_Model_Dynamic, Db_Model_Log';

	protected $api_added_columns = array();

	const status_new = 'new';
	const status_approved = 'approved';
	public $moderation_status = 'new';
	public static $moderation_statuses = array(
		'new'=>'New',
		'approved'=>'Approved'
	);

	public $belongs_to = array(
		'status' => array('class_name'=>'Service_Quote_Status', 'foreign_key'=>'status_id'),
		'user' => array('class_name' => 'User', 'foreign_key' => 'user_id'),
		'provider' => array('class_name' => 'Service_Provider', 'foreign_key' => 'provider_id'),
		'request' => array('class_name' => 'Service_Request', 'foreign_key' => 'request_id'),
	);

	public $has_many = array(
		'messages' => array('class_name'=>'User_Message', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Service_Quote'", 'order'=>'sent_at asc, id'),
	);
	
	public $calculated_columns = array(
		'status_code' => array('sql'=>'status_calculated_join.code', 'type'=>db_varchar),
		'rating_status' => array('sql'=>"if(service_quotes.moderation_status = 'new', 'New', 'Approved')"),
	);

	public $custom_columns = array(
		'comment_html' => db_varchar,
	);

	public function define_columns($context = null)
	{
		$this->define_relation_column('status', 'status', 'Status', db_varchar, '@name');
		$this->define_column('moderation_status', 'Moderation Status')->invisible();

		$this->define_column('id', '#');
		$this->define_column('created_at', 'Created');
		$this->define_column('updated_at', 'Last Updated');
		$this->define_relation_column('request', 'request', 'Request', db_varchar, "@title")->validation()->required('Quote must have a Request');
		$this->define_relation_column('user', 'user', 'User', db_varchar, '@username')->validation()->required('Quote must have a User');
		$this->define_relation_column('provider', 'provider', 'Provider', db_varchar, "@id")->validation();
		$this->define_column('comment', 'Comment');
		$this->define_column('price', 'Price')->currency(true);
		$this->define_column('start_at', 'Available to Start')->time_format('%I:%M %p')->date_as_is();
		$this->define_column('duration', 'Duration (Days)');

		// Extensibility
		$this->defined_column_list = array();
		Phpr::$events->fire_event('service:on_extend_quote_model', $this, $context);
		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('status', 'left')->tab('Quote');
		$this->add_form_field('moderation_status', 'right')->display_as(frm_dropdown)->tab('Quote');

		$this->add_form_field('user','left')
			->display_as(frm_record_finder, array(
				'sorting'=>'first_name, last_name, email',
				'list_columns'=>'first_name,last_name,email',
				'search_prompt'=>'Find user by name or email',
				'form_title'=>'Find User',
				'display_name_field'=>'first_name',
				'display_description_field'=>'email',
				'prompt'=>'Click Find to find a user'))->tab('Quote');

		$this->add_form_field('provider','right')
			->display_as(frm_record_finder, array(
				'sorting'=>'business_name',
				'list_columns'=>'business_name',
				'search_prompt'=>'Find provider by business name',
				'form_title'=>'Find Provider',
				'display_name_field'=>'business_name',
				'display_description_field'=>'id',
				'prompt'=>'Click Find to find a provider'))->tab('Quote');

		$this->add_form_field('start_at','left')->tab('Quote');
		$this->add_form_field('duration','right')->tab('Quote');
		$this->add_form_field('price','left')->tab('Quote');
		$this->add_form_field('comment','full')->tab('Quote');

		// Extensibility
		Phpr::$events->fire_event('service:on_extend_quote_form', $this, $context);
		foreach ($this->api_added_columns as $column_name)
		{
			$form_field = $this->find_form_field($column_name);
			if ($form_field)
				$form_field->options_method('get_added_field_options');
		}
	}

	// Events
	//

	public function before_create($session_key = null)
	{
		$this->set_status(Service_Quote_Status::status_new);
	}

	public function after_delete()
	{
		User_Message::delete_message_from_object('Service_Quote', $this->id);        
	}

	public function after_update()
	{
		Phpr::$events->fire_event('service:on_after_update_quote', $this);
	}

	public function after_modify($operation, $session_key)
	{
		Service_Provider::update_stat_fields($this->provider_id);
	}

	// Extensibility
	//

	public function get_added_field_options($db_name, $current_key_value = -1)
	{
		$result = Phpr::$events->fire_event('service:on_get_request_field_options', $db_name, $current_key_value);
		foreach ($result as $options)
		{
			if (is_array($options) || (strlen($options && $current_key_value != -1)))
				return $options;
		}

		return false;
	}

	// Custom columns
	//

	public function eval_comment_html()
	{
		if (strlen($this->comment))
			return Phpr_Html::paragraphize($this->comment);
		else
			return null;
	}

	// Filters
	// 

	// Where user or provider has permission to view
	public function apply_security($user)
	{
		$this->join('service_requests', 'service_requests.id = service_quotes.request_id');
		$this->join('service_providers', 'service_providers.id = service_quotes.provider_id');
		$this->where('service_providers.user_id=:user_id OR service_requests.user_id=:user_id', array('user_id'=>$user->id));
		return $this;
	}
	
	// Where user owns the quote
	public function apply_owner($user)
	{
		$this->where('user_id=?', $user->id);
		return $this;
	}

	public function apply_status($code)
	{
		if (!is_array($code))
			$code = array($code);

		$this->join('service_quote_statuses', 'service_quote_statuses.id = service_quotes.status_id');
		return $this->where('service_quote_statuses.code in (?)', array($code));
	}

	public function apply_visibility()
	{
		$this->where('deleted_at is null');
		return $this;
	}

	// Service methods
	// 

	public function set_status($status_code)
	{
		$status = Service_Quote_Status::create()->find_by_code($status_code);
		if (!$status)
			return false;

		$this->status = $status;
		$this->status_id = $status->id;
		$this->status_code = $status->code;
		return true;
	}

	public function set_notify_vars(&$template, $prefix = '')
	{
		$template->set_vars(array(
			$prefix.'comment'        => $this->comment,
			$prefix.'price'          => $this->price,
			$prefix.'duration'       => $this->duration,
			$prefix.'start_at'       => Phpr_DateTime::format_safe($this->start_at, '%X %F'),
			$prefix.'start_at_short' => Phpr_DateTime::format_safe($this->start_at, '%X %x'),
		));
	}

	// Moderation
	// 

	public function get_moderation_status_options($key_value = -1)
	{
		return self::$moderation_statuses;
	}

	public function approve()
	{
		$bind = array(
			'id' => $this->id,
			'status' => self::status_approved
		);
		Db_Helper::query("update service_quotes set moderation_status=:status where id=:id", $bind);

		if ($this->provider_id)
			Service_Provider::update_stat_fields($this->provider_id);
	}

}
