<?php

class Payment_Invoice_Statuses extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Payment_Invoice_Status';
	public $list_record_url = null;

	public $form_create_title = 'New Status';
	public $form_edit_title = 'Edit Status';
	public $form_model_class = 'Payment_Invoice_Status';
	public $form_not_found_message = 'Status not found';
	public $form_redirect = null;

	public $form_edit_save_flash = 'Status has been successfully saved';
	public $form_create_save_flash = 'Status has been successfully added';
	public $form_edit_delete_flash = 'Status has been successfully deleted';

	public function __construct()
	{
		parent::__construct();
		$this->app_module_name = 'Payment';

		$this->list_record_url = url('payment/invoice_statuses/edit');
		$this->form_redirect = url('payment/invoice_statuses');
	}

	public function index()
	{
		$this->app_page_title = 'Invoice Statuses';
	}

	private function getStatusObj($id)
	{
		$state = Payment_Invoice_Status::create();
		return strlen($id) ? $state->find($id) : $state;
	}
}

