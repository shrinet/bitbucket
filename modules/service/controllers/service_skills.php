<?php

class Service_Skills extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';

	public $list_model_class = 'Service_Skill';
	public $list_record_url = null;
	public $list_display_as_tree = true;
	public $list_search_enabled = true;
	public $list_search_fields = array('@name', '@url_name');
	public $list_search_prompt = 'find skills by name';

	public $form_model_class = 'Service_Skill';
	public $form_redirect = null;
	public $form_create_save_redirect = null;

	public $form_create_title = 'New Skill';
	public $form_edit_title = 'Edit Skill';
	public $form_not_found_message = 'Skill Not Found';
	public $form_edit_save_flash = 'Skill has been successfully saved';
	public $form_create_save_flash = 'Skill has been successfully added';
	public $form_edit_delete_flash = 'Skill has been successfully deleted';	

	protected $required_permissions = array('service:manage_skills');

	//public $global_handlers = array();

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'service';
		$this->app_page = 'skills';
		$this->app_module_name = 'Service';
		$this->list_record_url = url('service/skills/edit');
		$this->form_redirect = url('service/skills');
		$this->form_create_save_redirect = url('service/skills/edit/%s/'.uniqid());
	}

	public function index()
	{
		$this->app_page_title = 'Create Skills';
	}

	public function form_after_create_save($page, $session_key)
	{
		if (post('create_close'))
			$this->form_create_save_redirect = url('service/skills');
	}
}

