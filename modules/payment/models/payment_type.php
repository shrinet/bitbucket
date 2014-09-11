<?php

class Payment_Type extends Db_ActiveRecord
{
	public $table_name = 'payment_types';
	public $implement = 'Db_Model_Dynamic';
	protected $payment_type_obj = null;
	public $is_enabled = 1;
	public $invoice;
	private static $default_type = null;

	protected $added_fields = array();
	protected $hidden_fields = array();
	protected $form_context = null;

	public $custom_columns = array('gateway_name' => db_text, 'receipt_page' => db_text);
	public $encrypted_columns = array('config_data');

	public $fetched_data = array();

	protected $form_fields_defined = false;
	protected static $cache = array();    

	public $has_and_belongs_to_many = array(
		'countries'=>array('class_name'=>'Location_Country', 'join_table'=>'payment_type_countries', 'order'=>'name')
	);

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name')->order('asc')->validation()->fn('trim')->required('Please specify the payment gateway name');
		$this->define_column('gateway_name', 'Payment Gateway');
		$this->define_column('description', 'Description')->validation()->fn('trim');
		$this->define_column('is_enabled', 'Enabled');
		$this->define_column('is_default', 'Default');
		$this->define_column('code', 'API Code')->default_invisible()->validation()->fn('trim')->fn('mb_strtolower')->unique('A payment gateway with the specified API code already exists');
		$this->define_multi_relation_column('countries', 'countries', 'Countries', '@name')->default_invisible();
	}

	public function define_form_fields($context = null)
	{
		// Prevent duplication
		if ($this->form_fields_defined) return false;
		$this->form_fields_defined = true;

		// Mixin provider class
		if ($this->class_name && !$this->is_extended_with($this->class_name))
			$this->extend_with($this->class_name);

		$this->form_context = $context;

		if ($context != 'admin_payment_form')
		{
			$this->add_form_field('is_enabled')->tab('General');
			$this->add_form_field('name', 'left')->tab('General');
			$this->add_form_field('code', 'right')->tab('General')->comment('A unique code used to reference this payment gateway by other modules. Leave blank unless instructed.');
			$this->add_form_field('description')->tab('General')->size('small');
			$this->add_form_field('countries')->tab('Countries')->comment('Restrict payment type to selected countries. If none are selected the payment gateway is applicable to all countries.', 'above')->reference_sort('name');
			$this->build_config_ui($this, $context);

			if ($this->is_new_record())
				$this->init_config_data($this);
		}
		else
		{
			$this->add_form_partial(PATH_APP.'/modules/payment/controllers/payment_invoices/_pay_hidden_fields.htm')->tab('Payment Information');
			$this->build_payment_form($this, $context);
		}
	}

	// Custom columns
	//

	public function eval_gateway_name()
	{
		return $this->get_name();
	}

	// Options
	//

	public function get_added_field_options($db_name, $key_value = -1)
	{
		$method_name = "get_".$db_name."_options";

		if (!$this->method_exists($method_name))
			throw new Phpr_SystemException("Method ".$method_name." is not defined in ".$this->class_name." class");

		return $this->$method_name($key_value);
	}
	
	public function get_paymenttype_object()
                 {
                         if ($this->payment_type_obj !== null)
                                 return $this->payment_type_obj;
                         
                         $payment_types = Payment_Type_Manager::get_payment_type_class_names();
                         foreach ($payment_types as $class_name)
                         {
                                 if ($this->class_name == $class_name)
                                         return $this->payment_type_obj = new $class_name();
                         }
                         
                         throw new Phpr_ApplicationException("Class {$this->class_name} not found.");
                 }
	

	// Events
	//

	public function after_fetch()
	{
		// Mixin gateway class
		if ($this->class_name && !$this->is_extended_with($this->class_name))
			$this->extend_with($this->class_name);
	}

	public function before_save($session_key = null)
	{
		$this->validate_config_on_save($this);
	}

	public function before_delete($session_key = null)
	{
		if ($this->is_default)
			throw new Phpr_ApplicationException('Gateway '.$this->name.' is set as default. Set a different default gateway and try again.');

		$count = Db_Helper::scalar('select count(*) from payment_invoices where payment_type_id=:id', array('id'=>$this->id));
		if ($count)
			throw new Phpr_ApplicationException('Cannot delete this payment gateway because there are invoices referring to it.');
	}

	// Filters
	// 

	public function apply_enabled()
	{
		$this->where('is_enabled=1');
		return $this;
	}

	// Getters
	//

	public static function get_default($country_id=null)
	{
		if (self::$default_type !== null)
			return self::$default_type;

		$default_type = self::create()->apply_enabled()->where('is_default=1')->find();

		// Use anything!
		if (!$default_type) {
			$default_type = self::create()->apply_enabled()->find();
			if ($default_type)
				$default_type->make_default();
		}

		return self::$default_type = $default_type;
	}

	public static function list_applicable($country_id, $amount=null)
	{
		return self::create()->apply_enabled()->find_all();
	}

	public static function find_by_code($code)
	{
		$code = mb_strtolower($code);
		return self::create()->where('code=?', $code)->find();
	}

	public static function find_by_id($id)
	{
		if (!array_key_exists($id, self::$cache))
			self::$cache[$id] = self::create()->find($id);

		return self::$cache[$id];
	}

	public function get_partial_path($partial_name = null)
	{
		$class_path = File_Path::get_path_to_class($this->class_name);
		return $class_path.'/'.strtolower($this->class_name).'/'.$partial_name;
	}

	// Dynamic model
	//

	public function add_field($code, $title, $side = 'full', $type = db_text)
	{
		$this->define_dynamic_column($code, $title, $type);
		$form_field = $this->add_dynamic_form_field($code, $side);

		if ($this->form_context != 'admin_payment_form')
			$form_field->tab('Configuration');
		else
			$form_field->tab('Payment Information');

		$this->added_fields[$code] = $form_field;

		return $form_field;
	}

	// Service methods
	//

	public function make_default()
	{
		if (!$this->is_enabled)
			throw new Phpr_ApplicationException('Payment type '.$this->name.' is disabled and cannot be set as default.');

		$bind = array('id' => $this->id);
		Db_Helper::query('update payment_types set is_default=1 where id=:id', $bind);
		Db_Helper::query('update payment_types set is_default=null where id!=:id', $bind);
	}

	public static function invoice_status_deletion_check($status)
	{
		$methods = self::create()->find_all();

		foreach ($methods as $method)
		{
			$method->init_form_fields();
			$method->status_deletion_check($method, $status);
		}
	}

	// Partial management
	//

	public function display_payment_form($controller)
	{
		$this->before_display_payment_form($this);

		$pos = strpos($this->class_name, '_');
		$payment_type_file = strtolower(substr($this->class_name, $pos+1, -8));
		$partial_name = 'payment:'.$payment_type_file;

		if (Cms_Partial::create()->get_by_name($partial_name))
			$controller->display_partial($partial_name);
	}

}
