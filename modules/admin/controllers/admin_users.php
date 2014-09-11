<?php

class Admin_Users extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Admin_User';
	public $list_record_url = null;

	public $form_preview_title = 'Staff member';
	public $form_create_title = 'New Staff member';
	public $form_edit_title = 'Edit Staff member';
	public $form_model_class = 'Admin_User';
	public $form_not_found_message = 'User not found';
	public $form_redirect = null;

	public $form_edit_save_flash = 'Staff member has been successfully saved';
	public $form_create_save_flash = 'Staff member has been successfully added';
	public $form_edit_delete_flash = 'Staff member has been successfully deleted';

	public $list_search_enabled = true;
	public $list_search_fields = array('@first_name', '@last_name', '@email', '@login');
	public $list_search_prompt = 'find users by name, login or email';

	protected $required_permissions = array('admin:manage_users');

	protected $access_exceptions = array('mysettings');

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'system';
		$this->app_page = 'admins';
		$this->app_module_name = 'System';

		$this->list_record_url = url('admin/users/edit');
		$this->form_redirect = url('admin/users');
	}

	public function index()
	{
		$this->app_page_title = 'Staff members';
	}

	//
	// My settings
	//

	public function mysettings()
	{
		$this->edit($this->active_user->id, 'mysettings');
		$this->app_page_title = 'My Settings';
	}

	protected function mysettings_on_save()
	{
		$this->form_redirect = null;
		$this->form_edit_save_flash = null;

		$this->edit_on_save($this->active_user->id);
		echo Admin_Html::flash_message('Your settings have been saved.');
	}

	protected function mysettings_on_reset_preferences()
	{
		$this->form_redirect = null;
		$this->form_edit_save_flash = null;

		Phpr_User_Parameters::reset($this->active_user->id);

		echo Admin_Html::flash_message('Your preferences have been reset.');
	}	
}

