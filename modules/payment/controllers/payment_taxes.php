<?php

class Payment_Taxes extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Payment_Tax';
	public $list_record_url = null;

	public $form_preview_title = 'Tax Class';
	public $form_create_title = 'New Tax Class';
	public $form_edit_title = 'Tax Table';
	public $form_model_class = 'Payment_Tax';
	public $form_not_found_message = 'Tax class not found';
	public $form_redirect = null;

	public $form_edit_save_flash = 'Tax class has been successfully saved';
	public $form_create_save_flash = 'Tax class has been successfully added';
	public $form_edit_delete_flash = 'Tax class has been successfully deleted';
	
	public $form_grid_csv_export_url = null;

	protected $required_permissions = array('payment:manage_taxes');

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'payment';
		$this->app_page = 'taxes';
		$this->app_module_name = 'Payment';

		$this->list_record_url = url('payment/taxes/edit');
		$this->form_redirect = url('payment/taxes');
		$this->form_grid_csv_export_url = url('payment/taxes');
	}
	
	public function index()
	{
		Phpr::$response->redirect(url('payment/taxes/edit/1'));
		$this->app_page_title = 'Tax Classes';
	}
}

