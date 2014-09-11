<?php
	
class Service_Providers extends Admin_Controller
{	
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Service_Provider';
	public $list_record_url = null;
    public $list_record_onclick = null;
	
	public $form_preview_title = 'Service Provider';
	public $form_create_title = 'New Service Provider';
	public $form_edit_title = 'Edit Service Provider';
	public $form_model_class = 'Service_Provider';
	public $form_not_found_message = 'Service Provider not found';
	public $form_redirect = null;
	
	public $form_edit_save_flash = 'Service Provider has been successfully saved';
	public $form_create_save_flash = 'Service Provider has been successfully added';
	public $form_edit_delete_flash = 'Service Provider has been successfully deleted';
	
	public $list_search_enabled = true;
	public $list_search_fields = array('@business_name');
	public $list_search_prompt = 'find providers by name, login or email';
	
	protected $required_permissions = array('service:manage_providers');
	
	public $global_handlers = array('on_update_states_list');
	
	public $list_name = null;
	public $list_custom_prepare_func = null;
	public $list_custom_body_cells = null;
	public $list_custom_head_cells = null;
	public $list_custom_partial = null;
	
	public function __construct()
	{
		$this->app_menu = 'service';
		$this->app_page = 'providers';
		$this->app_module_name = 'Service';
	
		$this->list_record_url = url('service/providers/edit');
		$this->form_redirect = url('service/providers');
	
		parent::__construct();
	}
	
	public function index()
	{
		$this->app_page_title = 'Service Providers';
	}
	
	protected function on_update_states_list()
	{
		$data = post('Service_Provider');
	
		$form_model = $this->form_create_model_object();
		$form_model->country_id = $data['country_id'];
	
		$this->prepare_partial('form_field_container_state_idService_Provider');
		$this->form_render_field_container($form_model, 'state');
	}
	
}	
	
	