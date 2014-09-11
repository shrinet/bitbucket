<?php

class Service_Answer extends Db_ActiveRecord
{
	public $table_name = 'service_answers';

	public $belongs_to = array(
		'user' => array('class_name' => 'User'),
		'request' => array('class_name' => 'Service_Request'),
	);

	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
		$this->define_column('created_at', 'Created');
		$this->define_column('updated_at', 'Last Updated');
		$this->define_column('description', 'Answer');
		$this->define_column('is_public', 'Public');
		$this->define_relation_column('user', 'user', 'User', db_varchar, '@username')->validation();
		$this->define_relation_column('request', 'request', 'Service Request', db_varchar, '@title')->validation();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('user','left')
			->display_as(frm_record_finder, array(
				'sorting'=>'first_name, last_name, email',
				'list_columns'=>'first_name,last_name,email',
				'search_prompt'=>'Find user by name or email',
				'form_title'=>'Find User',
				'display_name_field'=>'first_name',
				'display_description_field'=>'email',
				'prompt'=>'Click the Find button to find a user'));

		$this->add_form_field('is_public','right')->comment('Tick if the question should appear publically.', 'above');
		$this->add_form_field('description');
	}

	// Events
	//

	public function after_delete()
	{
		Db_Helper::query('update service_questions set answer_id = null where answer_id=:id', array('id'=>$this->id));
	}

	// Service methods
	// 
	
	public function set_notify_vars(&$template, $prefix='')
	{
		$template->set_vars(array(
			$prefix.'description'   => $this->description,
		));
	}

}
