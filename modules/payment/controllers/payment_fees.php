<?php 

class Payment_Fees extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Payment_Fee';
	public $list_record_url = null;

	public $list_options = array();
	public $list_name = null;
	public $list_custom_body_cells = null;
	public $list_custom_head_cells = null;
	public $list_custom_prepare_func = null;
	public $list_no_setup_link = false;
	public $list_items_per_page = 20;
	public $list_search_enabled = false;
	public $list_search_fields = array();
	public $list_search_prompt = null;

	public $form_create_title = 'New Fee';
	public $form_edit_title = 'Edit Fee';
	public $form_model_class = 'Payment_Fee';
	public $form_not_found_message = 'Fee not found';
	public $form_redirect = null;

	public $form_edit_save_flash = 'Fee has been successfully saved';
	public $form_create_save_flash = 'Fee has been successfully added';
	public $form_edit_delete_flash = 'Fee has been successfully deleted';

	protected $required_permissions = array('payment:manage_fees');

	public $global_handlers = array(
		'on_set_fee_collapse_status',
		'on_update_action',
		'on_set_fee_orders',
		'on_delete_fee',
	);

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'payment';
		$this->app_page = 'fees';
		$this->app_module_name = 'Payment';
		$this->form_redirect = url('payment/fees');
	}
		
	public function index()
	{
		$this->view_data['fees'] = Payment_Fee::create()->order('sort_order')->find_all();
		$this->app_page_title = 'Site Fees and Charges';
	}

	public function fees_get_collapse_status($fee)
	{
		$statuses = Phpr_User_Parameters::get('payment_fees_status', null, array());
		if (array_key_exists($fee->id, $statuses))
			return $statuses[$fee->id];

		return false;
	}

	public function on_set_fee_collapse_status()
	{
		$id = post('fee_id');
		$statuses = Phpr_User_Parameters::get('payment_fees_status', null, array());
		$statuses[$id] = post('new_status');
		Phpr_User_Parameters::set('payment_fees_status', $statuses);
	}

	protected function on_update_action($fee_id)
	{
		try
		{
			if (strlen($fee_id))
			{
				$fee_obj = Payment_Fee::create()->find($fee_id);
				if (!$fee_obj)
					throw new Phpr_ApplicationException('Fee not found');
			} 
			else 
			{
				$fee_obj = new Payment_Fee();
			}
			
			$params = post('Payment_Fee', array());
			$fee_obj->action_class_name = $params['action_class_name'];
			$fee_obj->event_class_name = $params['event_class_name'];
			$fee_obj->init_form_fields();
			$fee_obj->set_data($params);

			$this->prepare_partial('tab_2');
			$this->form_render_form_tab($fee_obj, 1);
			
			$this->prepare_partial('tab_3');
			$this->form_render_form_tab($fee_obj, 2);
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}    

	public function on_set_fee_orders()
	{        
		try
		{
			$sort_orders = explode(',', post('sort_orders'));
			$item_ids = explode(',', post('item_ids'));
			Payment_Fee::create()->set_orders($item_ids, $sort_orders);
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}
	
	public function on_delete_fee()
	{
		try
		{
			$fee_id = post('fee_id');

			if (strlen($fee_id))
			{
				$obj = Payment_Fee::create()->find($fee_id);

				if ($obj)
				{
					$obj->delete();
					Phpr::$session->flash['success'] = 'The fee has been successfully deleted.';
				}
			}

			$fees = Payment_Fee::create();
			$this->view_data['fees'] = $fees->order('sort_order')->find_all();
			$this->display_partial('fees_container');
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	protected function set_hotswap_classes($model)
	{
		$data = post('Payment_Fee', array());

		$action_class_name = array_key_exists('action_class_name', $data) ? $data['action_class_name'] : $model->action_class_name;
		$model->action_class_name = $action_class_name;

		$event_class_name = array_key_exists('event_class_name', $data) ? $data['event_class_name'] : $model->event_class_name;
		$model->event_class_name = $event_class_name;
	}
	
	public function form_create_model_object()
	{
		$model_class = $this->form_model_class;

		$obj = new $model_class();
		$this->set_hotswap_classes($obj);

		$obj->init_columns();
		$obj->init_form_fields($this->form_get_context());

		return $obj;
	}

	public function form_find_model_object($recordId)
	{
		$model_class = $this->form_model_class;
			
		if (!strlen($recordId))
			throw new Phpr_ApplicationException($this->form_not_found_message);

		$model = new $model_class();
		$obj = $model->find($recordId);
		
		if (!$obj || !$obj->count())
			throw new Phpr_ApplicationException($this->form_not_found_message);
			
		$this->set_hotswap_classes($obj);
		$obj->init_form_fields($this->form_get_context());

		return $obj;
	}

}