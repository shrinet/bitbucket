<?php

class Service_Requests extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Service_Request';
	public $list_record_url = null;
    public $list_record_onclick = null;

	public $form_model_class = 'Service_Request';
	public $form_redirect = null;
	public $form_create_save_redirect = null;

	public $form_create_title = 'New Request';
	public $form_edit_title = 'Edit Request';
	public $form_preview_title = 'Service Request';
	public $form_not_found_message = 'Request Not Found';
	public $form_edit_save_flash = 'Request has been successfully saved';
	public $form_create_save_flash = 'Request has been successfully added';
	public $form_edit_delete_flash = 'Request has been successfully deleted';

	public $list_search_enabled = true;
	public $list_search_fields = array('@title');
	public $list_search_prompt = 'find requests by name';

	protected $required_permissions = array('service:manage_requests');

	public $global_handlers = array('on_update_states_list');

	public function __construct()
	{
		$this->app_menu = 'service';
		$this->app_page = 'requests';
		$this->app_module_name = 'Service';
		$this->list_record_url = url('service/requests/preview');
		$this->form_redirect = url('service/requests');
		$this->form_create_save_redirect = url('service/requests/edit/%s');

		parent::__construct();
	}

	public function index()
	{
		$this->app_page_title = 'Requests';
	}

	protected function on_update_states_list()
	{
		$data = post('Service_Request');

		$form_model = $this->form_create_model_object();
		$form_model->country_id = $data['country_id'];
		
		$this->prepare_partial('form_field_container_state_idService_Request');
		$this->form_render_field_container($form_model, 'state');
	}

}
