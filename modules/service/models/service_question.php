<?php

class Service_Question extends Db_ActiveRecord
{
	public $table_name = 'service_questions';

	public $belongs_to = array(
		'provider' => array('class_name' => 'Service_Provider', 'foreign_key'=>'provider_id'),
		'request' => array('class_name' => 'Service_Request', 'foreign_key'=>'request_id'),
		'answer' => array('class_name' => 'Service_Answer', 'foreign_key'=>'answer_id')
	);

	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
		$this->define_column('created_at', 'Created');
		$this->define_column('updated_at', 'Last Updated');
		$this->define_column('description', 'Question');
		$this->define_column('is_public', 'Public');
		$this->define_relation_column('provider', 'provider', 'Provider', db_varchar, '@business_name')->validation();
		$this->define_relation_column('answer', 'answer', 'Answer', db_varchar, '@description')->validation();
		$this->define_relation_column('request', 'request', 'Service Request', db_varchar, '@title')->validation();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('provider','left')
			->display_as(frm_record_finder, array(
				'sorting'=>'business_name',
				'list_columns'=>'business_name',
				'search_prompt'=>'Find provider by business name',
				'form_title'=>'Find Provider',
				'display_name_field'=>'business_name',
				'display_description_field'=>'email',
				'prompt'=>'Click the Find button to find a provider'));

		$this->add_form_field('answer','right')
			->display_as(frm_record_finder, array(
				'sorting'=>'description',
				'list_columns'=>'description',
				'search_prompt'=>'Find answer by description',
				'form_title'=>'Find Answer',
				'display_name_field'=>'description',
				'display_description_field'=>null,
				'prompt'=>'Click the Find button to find a answer',
				'conditions'=>'service_questions.request_id = request_calulated_join.id'));
		//$this->add_form_field('user','left');
		//$this->add_form_field('question','left');
		$this->add_form_field('description','left');
		$this->add_form_field('is_public','right')->comment('Tick if the question should appear publically.', 'above');
		//$this->add_form_field('request','left');
	}

	// Service methods
	// 

	public function set_notify_vars(&$template, $prefix='')
	{
		$template->set_vars(array(
			$prefix.'description' => $this->description,
		));
	}
}
