<?php

class Service_Suggest_Category_Template extends Notify_Template_Base
{
	public $required_params = array('user', 'category_name');

	public function get_info()
	{
		return array(
			'name'=> 'Suggest a Category',
			'description' => 'When a user suggests a category.',
			'code' => 'service:suggest_category'
		);
	}

	public function get_internal_subject()
	{
		return 'New category suggestion';
	}

	public function get_internal_content()
	{
		return file_get_contents($this->get_partial_path('email_content_internal.htm'));
	}

	public function prepare_template($template, $params=array())
	{
		extract($params);        

		$template->set_vars(array(
			'name' => $user->name,
			'email' => $user->email,
			'category_name' => $category_name
		));

		$users = Admin_User::list_users_having_permission('service', 'manage_categories');
		$template->add_recipients($users, true);
	}
}
