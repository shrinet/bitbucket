<?php

// Payment Fee Promisary Class
// 
// This class allows you to defer site activities.
// 
// It supports these modes:
// 
//  ajax_action - eg: 
//   user:on_register -> Cms_Action_Manager::exec_ajax_handler('user:account', $controller);
//  
//  page_action - eg: 
//   user:register -> Cms_Action_Manager::exec_action('user:account', $controller);
//   
//  class_method - eg: 
//   User:register -> User::register($params);
// 
// --
// 
// Defer a an action:
//   Payment_Fee_Promise::create_promise('user:on_register', array('email'=>'hello@scriptsahoy.com'));
//   
// Resolve an action:
//   Payment_Fee_Promise::find_promise($hash)->resolve_promise();
// 
// You can defer a page/ajax action until another
// completely separate action is complete, for example, 
// when an invoice is paid.
// 
// It works by capturing the action name (or ajax handler name)
// along with the $_POST data. The original process is then halted.
// 
// When the promise is "resolved" the action is then triggered again 
// by repopulating the $_POST array and using
// 
//   Cms_Action_Manager::exec_action('user:account', $controller);
// or
//   Cms_Action_Manager::exec_ajax_handler('user:on_update_account', $controller);
// 
// 

class Payment_Fee_Promise extends Db_ActiveRecord
{

	const mode_ajax_action = 'ajax_action';
	const mode_page_action = 'page_action';
	const mode_class_method = 'class_method';

	public $table_name = 'payment_fee_promises';

	public $belongs_to = array(
		'fee'=>array('class_name'=>'Payment_Fee', 'foreign_key'=>'fee_id'),
		'invoice'=>array('class_name'=>'Payment_Invoice', 'foreign_key'=>'invoice_id'),
	);

	public function define_columns($context = null)
	{ 
		$this->define_column('param_data', 'Parameter data')->validation()->required();

		$this->define_relation_column('invoice', 'invoice', 'Invoice', db_varchar, '@id');
		$this->define_relation_column('fee', 'fee', 'Fee', db_varchar, '@name');

		$this->define_column('deleted_at', 'Deleted')->invisible()->date_format('%x %H:%M');
	}

	public function define_form_fields($context = null)
	{

	}

	// Events
	// 

	public function before_create($session_key = null)
	{
		$this->hash = $this->create_hash();
		while (Db_Helper::scalar('select count(*) from payment_fee_promises where hash=:hash', array('hash'=>$this->hash)))
			$this->hash = $this->create_hash();
	}

	// Service methods
	// 

	public static function create_promise($mode, $command_name, $param_data=array(), $user=null)
	{
		$obj = new self();

		$obj->mode = $mode;
		$obj->command_name = $command_name;
		$obj->param_data = serialize($param_data);
		
		if ($user)
			$obj->user_id = $user->id;
		
		return $obj;
	}

	public static function find_promise($hash)
	{
		$promise = self::create()->apply_active()->find_by_hash($hash);
		if (!$promise)
			throw new Phpr_SystemException('Invalid promise token for fee class');

		return $promise;
	}

	public function resolve_promise()
	{
		$params = unserialize($this->param_data);
		$params['payment_fee_promise_hash'] = $this->hash;

		$controller = Cms_Controller::get_instance();

		if (!$controller)
			$controller = Cms_Controller::create();

		switch($this->mode)
		{
			case self::mode_ajax_action:
				$_POST = $params;
				Cms_Action_Manager::exec_ajax_handler($this->command_name, $controller);
				break;

			case self::mode_page_action:
				$_POST = $params;
				Cms_Action_Manager::exec_action($this->command_name, $controller);
				break;

			case self::mode_class_method:
				self::exec_class_action($this->command_name, $params);
				break;
		}
	}

	public static function exec_class_action($command, $params)
	{
		$parts = explode('::', $command);
		if (count($parts) < 1)
			return;

		$model_class = $parts[0];
		$method_name = $parts[1];

		call_user_func_array(array($model_class, $method_name), $params);
	}

	public function end_promise()
	{
		$this->deleted_at = Phpr_DateTime::now();
		$this->save();
	}

	public static function resolve_from_invoice($invoice)
	{
		$promises = self::create()->apply_active()->where('invoice_id=?', $invoice->id)->find_all();

		if (!$promises->count)
			return false;

		$found = false;

		// Loop through each invoice line item
		foreach ($invoice->items as $item)
		{
			// Loop each promise to find a match
			foreach ($promises as $promise)
			{
				if ($promise->invoice_item_id != $item->id)
					continue;

				$promise->resolve_promise();
				$found = true;
			}
		}

		return $found;
	}

	// Filters
	// 

	public function apply_active()
	{
		return $this->where('deleted_at is null');
	}

	// Helpers
	// 

	protected function create_hash()
	{
		return md5(uniqid('promise', microtime()));
	}
}
