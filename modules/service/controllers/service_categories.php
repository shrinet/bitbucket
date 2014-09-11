<?php

class Service_Categories extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';

	public $list_model_class = 'Service_Category';
	public $list_record_url = null;
	public $list_display_as_tree = true;
	public $list_search_enabled = true;
	public $list_search_fields = array('@name', '@url_name');
	public $list_search_prompt = 'find categories by name';

	public $form_model_class = 'Service_Category';
	public $form_redirect = null;
	public $form_create_save_redirect = null;

	public $form_create_title = 'New Category';
	public $form_edit_title = 'Edit Category';
	public $form_not_found_message = 'Category Not Found';
	public $form_edit_save_flash = 'Category has been successfully saved';
	public $form_create_save_flash = 'Category has been successfully added';
	public $form_edit_delete_flash = 'Category has been successfully deleted';	

    public $list_csv_import_url = null;
    public $list_csv_cancel_url = null;

    protected $required_permissions = array('service:manage_categories');

	//public $global_handlers = array();

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'service';
		$this->app_page = 'categories';
		$this->app_module_name = 'Service';
		$this->list_record_url = url('service/categories/edit');
		$this->form_redirect = url('service/categories');
		$this->form_create_save_redirect = url('service/categories/edit/%s/'.uniqid());

        $this->list_csv_import_url = url('service/categories/import');
        $this->list_csv_cancel_url = url('service/categories');

	}

	public function index()
	{
		$this->app_page_title = 'Create Categories';
	}

    public function import()
    {
        $this->app_page_title = 'Import Service Categories';
    }
    
    public function export()
    {
        $this->suppress_view();
        try
        {
            Service_Category::create()->csv_export(true);
        } 
        catch (Exception $ex)
        {
            $this->app_page_title = 'Export Service Categories';
            $this->_suppress_view = false;
            $this->handle_page_error($ex);
        }
    }

	public function form_after_create_save($page, $session_key)
	{
		if (post('create_close'))
			$this->form_create_save_redirect = url('service/categories');
	}

}

