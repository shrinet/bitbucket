<?php

class Cms_Strings extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';
	public $list_model_class = 'Cms_String';
	public $list_record_url = null;
	public $list_handle_row_click = false;
	public $list_record_onclick = null;

	public $form_model_class = 'Cms_String';
	public $form_preview_title = 'Page';
	public $form_create_title = 'New String';
	public $form_edit_title = 'Edit String';
	public $form_not_found_message = 'String not found';
	public $form_redirect = null;
	public $form_create_save_redirect = null;

	public $form_edit_save_flash = 'The string has been successfully saved';
	public $form_create_save_flash = 'The string has been successfully added';
	public $form_edit_delete_flash = 'The string has been successfully deleted';
	public $form_edit_save_auto_timestamp = true;

	public $list_search_enabled = true;
	public $list_search_fields = array('@code', '@content');
	public $list_search_prompt = 'find strings by content or code';

	public $list_csv_import_url = null;
	public $list_csv_cancel_url = null;

	//public $enable_concurrency_locking = true;

	protected $global_handlers = array();

	protected $required_permissions = array('cms:manage_content');

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'cms_content';
		$this->app_module_name = 'CMS';

		$this->list_record_onclick = "new PopupForm('index_onshow_manage_string_form', { ajaxFields: {string_id: '%s' } }); return false;";
		$this->form_redirect = url('cms/strings');
		$this->form_create_save_redirect = url('cms/strings/edit/%s');

		$this->list_csv_import_url = url('cms/strings/import');
		$this->list_csv_cancel_url = url('cms/strings');

		$this->app_page = 'strings';
	}

	public function index()
	{
		$this->app_page_title = 'Strings';
	}

	public function list_prepare_data()
	{
		$obj = Cms_String::create();
		$theme = Cms_Theme::get_edit_theme();
		if ($theme)
			$obj->where('theme_id=?', $theme->code);

		return $obj;
	}
	
	public function import()
	{
		$this->app_page_title = 'Import Strings';
	}
	
	public function export()
	{
		$this->suppress_view();
		try
		{
			Cms_String::create()->csv_export(true);
		} 
		catch (Exception $ex)
		{
			$this->app_page_title = 'Export Strings';
			$this->_suppress_view = false;
			$this->handle_page_error($ex);
		}
	}

	protected function index_onshow_manage_string_form()
	{
		try
		{
			$model_id = post('string_id', null);
			
			$this->reset_form_edit_session_key();

			$model = Cms_String::create();
			if ($model_id)
				$model = $model->find($model_id);

			$model->init_form_fields();
			
			$this->view_data['model'] = $model;
			$this->view_data['new_record_flag'] = !($model_id);
		} 
		catch (Exception $ex)
		{
			$this->handle_page_error($ex);
		}

		$this->display_partial('manage_string_form');
	}

	protected function index_onsave_string()
	{
		try
		{
			$string_id = post('string_id');
			
			if (!$string_id)
				throw new Phpr_ApplicationException("Please select a string");
				
			$model = Cms_String::create()->find($string_id);
			if (!$model)
				throw new Phpr_ApplicationException("String not found");

			$model->save(post('Cms_String'));

			Phpr::$session->flash['success'] = sprintf('String %s updated successfully', h($model->code));
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	protected function index_ondelete_string()
	{
		try
		{
			$string_id = post('string_id');
			
			if (!$string_id)
				throw new Phpr_ApplicationException("Please select a string");
				
			$model = Cms_String::create()->find($string_id);
			if (!$model)
				throw new Phpr_ApplicationException("String not found");

			$model->delete();

			Phpr::$session->flash['success'] = sprintf('String %s has been deleted', h($model->code));
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}
}

