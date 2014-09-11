<?php

class Service_Provider_Groups extends Admin_Controller
{
	public $implement = 'Db_List_Behavior, Db_Form_Behavior';

	public $list_model_class = 'Service_Provider_Group';
	public $list_record_url = null;
	public $list_search_fields = array();
	public $list_search_prompt = '';
	public $list_columns = array();
	public $list_custom_body_cells = null;
	public $list_custom_head_cells = null;
	public $list_custom_prepare_func = null;
	public $list_search_enabled = false;
	public $list_no_setup_link = false;
	public $list_items_per_page = 20;

	public $form_model_class = 'Service_Provider_Group';
	public $form_redirect = null;
	public $form_create_save_redirect = null;

	public $form_create_title = 'New Group';
	public $form_edit_title = 'Edit Group';
	public $form_not_found_message = 'Group Not Found';
	public $form_edit_save_flash = 'Group has been successfully saved';
	public $form_create_save_flash = 'Group has been successfully added';
	public $form_edit_delete_flash = 'Group has been successfully deleted';

	protected $global_handlers = array(
		'on_load_add_provider_form',
		'on_add_providers',
		'on_update_provider_list',
		'on_remove_provider',
		'on_remove_selected_providers',
	);

	public function __construct()
	{
		parent::__construct();
		$this->app_menu = 'service';
		$this->app_page = 'providers';
		$this->app_module_name = 'Service';
		$this->list_record_url = url('service/provider_groups/edit');
		$this->form_redirect = url('service/provider_groups');
		$this->form_create_save_redirect = url('service/provider_groups/edit/%s/'.uniqid());

		if (post('add_provider_mode'))
		{
			$this->list_name = null;
			$this->list_model_class = 'Service_Provider';
			$this->list_columns = array('business_name');
			$this->list_custom_body_cells = PATH_SYSTEM.'/modules/db/behaviors/list_behavior/partials/_list_body_cb.htm';
			$this->list_custom_head_cells = PATH_SYSTEM.'/modules/db/behaviors/list_behavior/partials/_list_head_cb.htm';
			$this->list_custom_prepare_func = 'prepare_provider_list';
			$this->list_record_url = null;
			$this->list_search_enabled = true;
			$this->list_no_setup_link = true;
			$this->list_search_fields = array('@business_name');
			$this->list_search_prompt = 'find providers by business_name';
			$this->list_items_per_page = 10;
		}
	}

	public function index()
	{
		$this->app_page_title = 'Create Groups';
	}

	// Events
	// 
	
	public function form_after_save($model, $session_key)
	{
		$model->set_provider_orders(post('provider_ids', array()), post('provider_order', array()));
	}

	public function form_after_create_save($page, $session_key)
	{
		if (post('create_close'))
			$this->form_create_save_redirect = url('service/provider_groups');
	}

	// Add Provider
	//

	protected function on_load_add_provider_form()
	{
		try
		{
			$this->view_data['session_key'] = post('edit_session_key');
			$this->display_partial('add_provider_form');
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	public function prepare_provider_list()
	{
		$id = Phpr::$router->param('param1');
		$obj = $this->get_group_object($id);

		$group_obj = $obj->get_deferred('providers', $this->form_get_edit_session_key());
		$providers = Db_Helper::object_array($group_obj->build_sql());

		$bound = array();
		$obj = Service_Provider::create();
		foreach ($providers as $provider)
			$bound[] = $provider->id;

		if (count($bound))
			$obj->where('id not in (?)', array($bound));

		return $obj;
	}

	protected function on_add_providers($id = null)
	{
		try
		{
			$id_list = post('list_ids', array());
			if (!count($id_list))
				throw new Phpr_ApplicationException('Please select provider(s) to add.');

			$group_obj = $this->get_group_object($id);
			$added_providers = $group_obj->get_all_deferred('providers', post('edit_session_key'));
			$added_ids = array();
			foreach ($added_providers as $provider)
				$added_ids[] = $provider->id;

			$providers = Service_Provider::create()->where('id in (?)', array($id_list));
			if (count($added_ids))
				$providers->where('id not in (?)', array($added_ids));

			$providers = $providers->find_all();

			foreach ($providers as $provider)
				$group_obj->providers->add($provider, post('edit_session_key'));
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	// Provider list
	//

	protected function on_update_provider_list($id = null)
	{
		$this->view_data['form_model'] = $this->get_group_object($id);
		$this->display_partial('provider_list');
	}


	protected function get_provider_sort_order($provider_id)
	{
		$provider_ids = post('provider_ids', array());
		$provider_orders = post('provider_order', array());
		$group_id = Phpr::$router->param('param1');
		$group_obj = $this->get_group_object($group_id);
		if (!$group_obj)
			return null;

		$group_provider_orders = $group_obj->get_provider_orders();

		foreach ($provider_ids as $index=>$list_provider_id)
		{
			if ($list_provider_id == $provider_id)
			{
				if (array_key_exists($index, $provider_orders))
					return $provider_orders[$index];

				if (array_key_exists($provider_id, $group_provider_orders))
					return $group_provider_orders[$provider_id];

				return $provider_id;
			}
		}

		if (array_key_exists($provider_id, $group_provider_orders) && strlen($group_provider_orders[$provider_id]))
			return $group_provider_orders[$provider_id];

		return $provider_id;
	}

	protected function get_provider_list($form_model)
	{
		$provider_obj = $form_model->get_deferred('providers', $this->form_get_edit_session_key());
		$providers = Db_Helper::object_array($provider_obj->build_sql());

		$provider_list = array();
		foreach ($providers as $provider)
		{
			$sort_order = $this->get_provider_sort_order($provider->id);
			$item = array('provider'=>$provider, 'sort_order'=>$sort_order);
			$provider_list[] = (object)$item;
		}

		uasort($provider_list, array('Service_Provider_Groups', 'sort_providers'));

		return $provider_list;
	}

	// Remove Providers
	//

	protected function on_remove_provider($id = null)
	{
		try
		{
			$group = $this->get_group_object($id);
			$provider_id = post('provider_id');

			$provider = Service_Provider::create()->find($provider_id);
			if ($provider)
				$group->providers->delete($provider, $this->form_get_edit_session_key());

			$this->view_data['form_model'] = $group;
			$this->display_partial('provider_list');
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}

	protected function on_remove_selected_providers($id = null)
	{
		try
		{
			$group = $this->get_group_object($id);

			$id_list = post('list_ids', array());
			
			if (count($id_list)) {
				$providers = Service_Provider::create()->where('id in (?)', array($id_list))->find_all();

				foreach ($providers as $provider) {
					$group->providers->delete($provider, $this->form_get_edit_session_key());
				}
			}

			$this->view_data['form_model'] = $group;
			$this->display_partial('provider_list');
		}
		catch (Exception $ex)
		{
			Phpr::$response->ajax_report_exception($ex, true, true);
		}
	}


	// Helpers
	//

	protected static function sort_providers($a, $b)
	{
		if ($a->sort_order == $b->sort_order)
			return 0;

		if ($a->sort_order > $b->sort_order)
			return 1;

		return -1;
	}

	private function get_group_object($id)
	{
		return (strlen($id)) ? Service_Provider_Group::create()->find($id) : Service_Provider_Group::create();
	}


}

