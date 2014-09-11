<?php

class Payment_Fee extends Db_ActiveRecord
{
	public $table_name = 'payment_fees';
	public $implement = 'Db_Model_Dynamic';
	public $has_and_belongs_to_many = array(
		'user_groups'=>array('class_name'=>'User_Group', 'join_table'=>'payment_fees_user_groups', 'order'=>'name')
	);

	protected $added_fields = array();
	protected $action_obj = null;
	protected $event_obj = null;

	public $custom_columns = array('fee_conditions_field'=>db_text);

	public $enabled = true;

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name')->validation()->fn('trim')->required("Please specify the fee name");
		$this->define_column('description', 'Description')->validation()->fn('trim');
		$this->define_column('sort_order', 'Sort Order')->order('asc')->invisible();
		$this->define_column('enabled', 'Enabled');
		$this->define_column('date_start', 'From Date')->validation();
		$this->define_column('date_end', 'To Date')->validation();
		$this->define_multi_relation_column('user_groups', 'user_groups', 'Customer Groups', '@name');
		$this->define_multi_relation_column('user_group_ids', 'user_groups', 'Customer Group Ids', '@id');
		
		// @todo:
		//$this->define_column('fee_conditions_field', 'Conditions');
		
		$this->define_column('event_class_name', 'Event')->validation()->required();
		$this->define_column('action_class_name', 'Action')->validation()->required();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('enabled')->tab('Fee Settings');
		$this->add_form_field('name')->tab('Fee Settings');
		$this->add_form_field('description')->tab('Fee Settings')->size('tiny')->comment('This will appear on a user invoice.');
		$this->add_form_field('date_start', 'left')->tab('Fee Settings');
		$this->add_form_field('date_end', 'right')->tab('Fee Settings');
		$this->add_form_field('user_groups')->tab('Fee Settings')->comment('Please select user groups the fee is enabled for. Do not select any group to make the fee enabled for all user groups.', 'above');
		
		// @todo:
		//$this->add_form_field('fee_conditions_field')->tab('Conditions');
		//$this->form_tab_css_class('Conditions', 'conditions_tab');
		
		$this->add_form_field('event_class_name')->tab('Event')->display_as(frm_dropdown)->comment('Select WHEN this fee should be charged', 'above');
		$this->add_form_field('action_class_name')->tab('Action')->display_as(frm_dropdown)->comment('Select HOW this fee should be charged', 'above');
		
		$this->init_events();
		$this->init_actions();
	}

	// Events
	// 
	
	public function init_events()
	{
		if (!strlen($this->event_class_name))
		{
			$events = $this->get_event_class_name_options();
			$event_classes = array_keys($events);
			if (!isset($event_classes[0]))
				throw new Phpr_SystemException('No fee types found');
			
			$this->event_class_name = $event_classes[0];
		}

		$event_obj = $this->get_event_obj();
		$event_obj->build_config_form($this);
		$event_obj->init_fields_data($this);
		
		$event_info = (object)$event_obj->get_info();
		$this->find_form_field('event_class_name')->comment($event_info->description);
	}
	
	public function get_event_class_name_options($key_value = -1)
	{
		$result = array();
		$events = Payment_Fee_Event_Base::find_events();
		
		foreach ($events as $event_class)
		{
			$event_obj = new $event_class();

			// Disabled
			if ($event_obj->get_info() === null)
				continue;

			$event_info = (object)$event_obj->get_info();
			$result[$event_class] = $event_info->name;
		}

		asort($result);
		return $result;
	}

	public function get_event_obj()
	{
		// @todo Load single, not all
		$events = Payment_Fee_Event_Base::find_events();
		$class_name = $this->event_class_name;        
		if ($this->event_obj != null && get_class($this->event_obj) == $class_name)
			return $this->event_obj;

		if (!class_exists($class_name))
			throw new Phpr_SystemException('Fee class "'.$class_name.'" not found');

		return $this->event_obj = new $class_name();
	}

	// Trigger point for fees to be implemented. If $params is empty, $_POST will be used
	//   Usage: 
	//     module_exists('payment') && Payment_Fee::trigger_event('User_Register_Event', array('handler'=>'user:on_register'));
	//
	// Return true to halt parent process, otherwise return false 
	public static function trigger_event($class_name, $options = array(), $params = array())
	{
		$options = (object)$options;
		if (!isset($options->handler))
			throw new Phpr_SystemException('Missing handler for fee class '.$class_name.' trigger');

		if (!isset($options->is_ajax))
			$options->is_ajax = true;

		if (!$params)
			$params = $_POST;

		// Are we resolving a deferred event?
		// 
		if ($promise_hash = post('payment_fee_promise_hash'))
		{
			$promise = Payment_Fee_Promise::find_promise($promise_hash);
			if (!$promise)
				throw new Phpr_SystemException('Unable to process this transaction. There is nothing to do!');

			$promise->end_promise();
			
			$event = $promise->fee;
			$event_obj = $event->get_event_obj();
			$action_obj = $event->get_action_obj();
			
			$event_obj->resolve($event, $promise);
			$action_obj->resolve($event, $promise);
			return;
		}

		// No this is a first, trigger the event and action
		// 
		$events = self::create()->find_active_events($class_name);
		if (!count($events))
			return;

		foreach ($events as $event)
		{
			$event_obj = $event->get_event_obj();
			$action_obj = $event->get_action_obj();

			// Trigger returned "true" so abort
			if ($event_obj->trigger($event, $options, $params)) 
				continue;

			$action_obj->trigger($event, $options, $params);
		}
	}

	public function find_active_events($class_name)
	{
		$this->where('enabled=1');
		$this->where('event_class_name=?', $class_name);
		$this->order('sort_order');
		$events = $this->find_all();
		if (!$events->count)
			return null;

		$all_events = array();

		foreach ($events as $event)
		{
			if ($event->is_active_today())
				$all_events[] = $event;
		}

		return $all_events;
	}

	// Actions
	// 

	public function init_actions()
	{
		if (!strlen($this->action_class_name))
		{
			$actions = $this->get_action_class_name_options();
			$action_classes = array_keys($actions);
			$this->action_class_name = $action_classes[0];
		}

		$action_obj = $this->get_action_obj();
		$action_obj->build_config_form($this);
		$action_obj->init_fields_data($this);
		
		$action_info = (object)$action_obj->get_info();
		$this->find_form_field('action_class_name')->comment($action_info->description);
	}

	public function get_action_class_name_options($key_value = -1)
	{
		$result = array();
		$actions = Payment_Fee_Action_Base::find_actions();
		
		foreach ($actions as $action_class)
		{
			$action_obj = new $action_class();

			if (!$action_obj->is_applicable($this))
				continue;

			// Disabled
			if ($action_obj->get_info() === null)
				continue;

			$action_info = (object)$action_obj->get_info();
			$result[$action_class] = $action_info->name;
		}

		asort($result);
		return $result;
	}

	public function get_action_obj()
	{
		// @todo Load single, not all
		$actions = Payment_Fee_Action_Base::find_actions();
		$class_name = $this->action_class_name;
		if ($this->action_obj != null && get_class($this->action_obj) == $class_name)
			return $this->action_obj;

		if (!class_exists($class_name))
			throw new Phpr_SystemException('Fee class "'.$class_name.'" not found');

		return $this->action_obj = new $class_name();
	}


	// Custom fields
	// 

	public function add_field($code, $title, $side = 'full', $type = db_text, $tab = 'Event', $hidden = false)
	{
		$this->define_dynamic_column($code, $title, $type)->validation();
		if (!$hidden)
			$form_field = $this->add_dynamic_form_field($code, $side)->options_method('get_added_field_options')->option_state_method('get_added_field_option_state')->tab($tab);
		else
			$form_field = null;

		$this->added_fields[$code] = $form_field;

		return $form_field;
	}
	
	public function field_error($field, $message)
	{
		$this->validation->set_error($message, $field, true);
	}

	// Extensibility
	// 

	public function get_added_field_options($db_name)
	{
		$obj = $this->get_action_obj();
		$method_name = "get_".$db_name."_options";
		if (method_exists($obj, $method_name))
			return $obj->$method_name($this);
	 
		$obj = $this->get_event_obj();
		$method_name = "get_".$db_name."_options";
		if (method_exists($obj, $method_name))
			return $obj->$method_name($this);

		throw new Phpr_SystemException("Method ".$method_name." is not defined in ".$this->class_name." class.");
	}
	
	public function get_added_field_option_state($db_name, $key_value)
	{
		$obj = $this->get_action_obj();
		$method_name = "get_".$db_name."_option_state";
		if (method_exists($obj, $method_name))
			return $obj->$method_name($key_value);
		
		$obj = $this->get_event_obj();
		$method_name = "get_".$db_name."_option_state";
		if (method_exists($obj, $method_name))
			return $obj->$method_name($key_value);
			
		throw new Phpr_SystemException("Method ".$method_name." is not defined in ".$this->class_name." class.");
	}
	
	// Events
	// 

	public function after_validation($session_key = null)
	{
		if (strlen($this->date_start) && strlen($this->date_end))
		{
			$start_obj = Phpr_DateTime::parse($this->date_start, Phpr_DateTime::universal_date_format);
			$end_obj = Phpr_DateTime::parse($this->date_end, Phpr_DateTime::universal_date_format);

			if ($start_obj && $end_obj && $start_obj->compare($end_obj) > 0)
				$this->validation->set_error('The start date cannot be less than the end date.', 'date_start', true);
		}
		
		return true;
	}

	public function after_create() 
	{
		Db_Helper::query('update payment_fees set sort_order=:sort_order where id=:id', array(
			'sort_order'=>$this->id,
			'id'=>$this->id
		));

		$this->sort_order = $this->id;
	}

	// Service methods
	// 

	public function set_orders($ids, $orders)
	{
		foreach ($ids as $index=>$id)
		{
			if (!isset($orders[$index]))
				continue;
			
			$order = $orders[$index];
			Db_Helper::query('update payment_fees set sort_order=:order where id=:id', array(
				'order'=>$order,
				'id'=>$id
			));
		}
	}

	public function is_active_today()
	{
		if (!$this->date_start && !$this->date_end)
			return true;

		$current_user_time = Phpr_Date::user_date(Phpr_DateTime::now());

		if ($this->date_start && $this->date_end)
			return $this->date_start->compare($current_user_time) <= 0 && $this->date_end->compare($current_user_time) >= 0;
			
		if ($this->date_start)
			return $this->date_start->compare($current_user_time) <= 0;

		if ($this->date_end)
			return $this->date_end->compare($current_user_time) >= 0;
			
		return false;
	}

}
