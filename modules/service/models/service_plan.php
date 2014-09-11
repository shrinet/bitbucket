<?php

class Service_Plan extends Db_ActiveRecord
{

	public $table_name = 'service_plan';

	const status_new = 'new';
	const status_approved = 'approved';
	public $moderation_status = 'new';
	protected static $cache = null;
	protected static $name_list = null;
	protected static $object_list = null;
	protected static $old = null;
	protected static $id_cache = array();
	public static $moderation_statuses = array(
		'new'=>'New',
		'approved'=>'Approved'
	);

	public $belongs_to = array(        
		
	);

	public $calculated_columns = array(
		
	);

	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
#		$this->define_column('moderation_status', 'Moderation Status')->invisible();
		$this->define_column('name', 'Name');
		$this->define_column('credits', 'Credits');
		$this->define_column('description', 'Description');

#		$this->define_relation_column('plan', 'plan', 'Plan', db_varchar, '@name')->validation()->required('Rating must be from someone');
#		$this->define_relation_column('provider', 'provider', 'Provider', db_varchar, '@business_name')->validation();
		

		// Extensibility
		$this->defined_column_list = array();
#		Phpr::$events->fire_event('service:on_extend_rating_model', $this, $context);
#		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public function define_form_fields($context = null)
	{

#		$this->add_form_field('rating','right')->display_as(frm_dropdown)->empty_option('<no rating specified>');
		$this->add_form_field('name');
		$this->add_form_field('credits');
		$this->add_form_field('description');
				
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

#		$this->add_form_field('comment','full')->size('small');

		// Extensibility
		Phpr::$events->fire_event('service:on_extend_membership_form', $this, $context);
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
//		if ($is_rated)
			throw new Phpr_ApplicationException('You may only submit one rating per request');
	}

	
	
	public static function get_list($plan_id = null)
	{
		$obj = new self(null, array('no_column_init'=>true, 'no_validation'=>true));
		$obj->order('name')->where('enabled = 1');
		
		if (strlen($plan_id))
			$obj->or_where('id=?', $plan_id);
			
		return $obj->find_all();
	}
	

	public static function get_object_list($default = -1)
	{
		if (self::$object_list && !$default)
			return self::$object_list;

		$records = Db_Helper::object_array('select * from service_plan where enabled=1 or id=:id order by price', array('id' => $default));
		$result = array();
		foreach ($records as $plan) {
			$result[$plan->id] = $plan;
		}

		if (!$default)
			return self::$object_list = $result;
		else 
			return $result;
	}

	public static function get_name_list()
	{
		if (self::$name_list)
			return self::$name_list;
		
		$plans = self::get_object_list();
		$result = array();
		foreach ($plans as $id=>$plan) {
			$result[$id] = $plan->name;
		}
			
		return self::$name_list = $result;
	}


	// Helper methods
	// 

	public static function apply_membership($obj)
	{
		$service_plan = new Service_Plan;
		if(self::change_plan($obj->plan_id, $obj->id))
		{
			$newPlan = self::find_by_id($obj->plan_id);
			$bind = array(
			'id' => $obj->id,
			'credits' => $newPlan->credits,
			'active_date' => Phpr_DateTime::now()->to_sql_datetime(),
			'end_date' => Phpr_DateTime::now()->add_months(1)->to_sql_datetime()
			);
			Db_Helper::query('update service_providers set credits=:credits, active_date=:active_date, end_date=:end_date where id=:id', $bind);
			return;
		} else {
			return;
		}
	}
	
	public static function apply_new_plan($provider_id, $plan_id){
		$newPlan = self::find_by_id($plan_id);
			$bind = array(
			'id' => $provider_id,
			'plan_id' => $plan_id,
			'credits' => $newPlan->credits,
			'active_date' => Phpr_DateTime::now()->to_sql_datetime(),
			'end_date' => Phpr_DateTime::now()->add_months(1)->to_sql_datetime()
			);
			Db_Helper::query('update service_providers set plan_id=:plan_id, credits=:credits, active_date=:active_date, end_date=:end_date where id=:id', $bind);
			return;
	}
	
	public static function check_plan($provider){
		$credits = $provider->credits;
		$end_date = $provider->end_date;
		if(Phpr_DateTime::now()->compare($end_date) === -1){
			if($credits > 0){
				return true;
			}
			return false;
		}	
		return false;
	}
	
	public static function quote_apply($provider){
		$bind = array(
			'id' => $provider->id,
			'credits' => $provider->credits-1,
			);
			Db_Helper::query('update service_providers set credits=:credits where id=:id', $bind);
			return;
	}
	
	public static function change_plan($plan_id, $id)
	{
		$old_plan = self::get_current_plan($id);

		if(isset($old_plan) && $plan_id != $old_plan->plan_id)
		{
			return true;
		}
		return false;
	}
	
	public static function find_by_id($id)
	{
		$plans = self::list_plans();

		if (array_key_exists($id, $plans))
			return $plans[$id];
			
		return null;
	}
	
	public static function list_plans()
	{
		if (self::$cache === null)
			self::$cache = self::create()->find_all()->as_array(null, 'id');
		return self::$cache;
	}
	
	public static function get_current_plan($id)
	{
		$old_plan = self::get_old();
		if (array_key_exists($id, $old_plan))
			return $old_plan[$id];
			
		return null;
	}
	
	public static function get_old()
	{
		if (self::$old === null)
			self::$old = Service_Provider::create()->find_all()->as_array(null, 'id');
		return self::$old;
	}
}
