<?php

class Payment_Types extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Payment_Type';
	public $list_record_url = null;
	public $list_reuse_model = false;

	public $form_preview_title = 'Payment Gateway';
	public $form_create_title = 'New Payment Gateway';
	public $form_edit_title = 'Edit Payment Gateway';
	public $form_model_class = 'Payment_Type';
	public $form_not_found_message = 'Payment gateway not found';
	public $form_redirect = null;

	public $form_edit_save_flash = 'The payment gateway has been successfully saved';
	public $form_create_save_flash = 'The payment gateway has been successfully added';
	public $form_edit_delete_flash = 'The payment gateway has been successfully deleted';
	
	protected $required_permissions = array('payment:manage_types');

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'payment';
		$this->app_page = 'types';
		$this->app_module_name = 'Payment';

		$this->list_record_url = url('payment/types/edit');
		$this->form_redirect = url('payment/types');
	}

	public function index()
	{
		$this->app_page_title = 'Gateways';
		Payment_Type_Manager::create_partials();
	}

	public function list_get_row_class($model)
	{
		if (!$model->is_enabled) 
			return 'disabled';
			
		if ($model->is_default) 
			return 'important';
	}

	public function form_create_model_object()
	{
		$obj = Payment_Type::create();

		$class_name = Phpr::$router->param('param1');

		if (!Phpr::$class_loader->load($class_name))
			throw new Phpr_ApplicationException('Class '.$class_name.' not found');

		$obj->class_name = $class_name;
		$obj->init_columns();
		$obj->init_form_fields();

		return $obj;
	}

	protected function index_on_load_add_popup()
	{
		try
		{
			$payment_types = Payment_Type_Manager::get_payment_type_class_names();

			$type_list = array();
			foreach ($payment_types as $class_name)
			{
				$obj = new $class_name();
				$info = $obj->get_info();
				if (array_key_exists('name', $info))
				{
					$info['class_name'] = $class_name;
					$type_list[] = $info;
				}
			}

			usort($type_list, array('Payment_Types', 'payment_type_cmp'));

			$this->view_data['type_list'] = $type_list;
		}
		catch (Exception $ex)
		{
			$this->handle_page_error($ex);
		}

		$this->display_partial('add_gateway_form');
	}
		
	protected function index_on_show_default_gateway_form()
	{
		try
		{
			$ids = post('list_ids', array());
			$this->view_data['type_id'] = count($ids) ? $ids[0] : null;
			$this->view_data['types'] = Payment_Type::create()->where('is_default is null')->order('name')->find_all();
		} 
		catch (Exception $ex)
		{
			$this->handle_page_error($ex);
		}

		$this->display_partial('set_default_gateway_form');
	}
	
	protected function index_on_set_default_gateway()
	{
		try
		{
			$type_id = post('type_id');
			
			if (!$type_id)
				throw new Phpr_ApplicationException('Please select a default gateway.');
				
			$type = Payment_Type::create()->find($type_id);
			if (!$type)
				throw new Phpr_ApplicationException('Gateway not found');

			$type->make_default();

			Phpr::$session->flash['success'] = 'Gateway "'.h($type->name).'" is now the default payment method.';
			$this->display_partial('types_page_content');
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	public static function payment_type_cmp($a, $b)
	{
		return strcasecmp($a['name'], $b['name']);
	}

}
