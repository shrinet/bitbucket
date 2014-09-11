<?php

class Service_Skill extends Db_ActiveRecord
{
	public $implement = 'Db_AutoFootprints, Db_Act_As_Tree';
	public $auto_footprints_visible = true;

	public $has_and_belongs_to_many = array(
		'requests' => array('class_name'=>'Service_Request', 'join_table'=>'service_skills_requests', 'foreign_key'=>'request_id', 'primary_key'=>'skill_id')
	);

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name');
		$this->define_column('description', 'Description');
		$this->define_column('code', 'API Code')->default_invisible();
		$this->define_column('is_hidden', 'Hide')->default_invisible();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('name')->tab('Skill');
		$this->add_form_field('description')->tab('Skill')->size('small');

		$this->add_form_field('is_hidden', 'left')->tab('Skill')->comment('Hide skill from skill lists.');
		$this->add_form_field('code','right')->tab('Skill');
	}

	// Events
	// 

	public function after_delete()
	{
		Db_Helper::query('delete from service_skills_providers where skill_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_skills_requests where skill_id=:id', array('id'=>$this->id));
	}
/*
	public function after_create($session_key = null)
	{
		$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->name, 90);
		$bind = array(
			'id' => $this->id,
			'url_name' => $this->url_name
		);
		Db_Helper::query('update service_skills set url_name=:url_name where id=:id', $bind);
	}

	public function before_update($session_key = null)
	{
		if (isset($this->fetched['name']) && $this->fetched['name'] != $this->name)
			$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->name, 90);
	}*/

	// Service methods
	// 

	public static function get_popular_skills()
	{
		$skills = self::create();
		$skills->join('service_skills_requests', 'service_skills_requests.skill_id = service_skills.id');
		$skills->group('service_skills.id');
		$skills->order('COUNT(service_skills.id) DESC');
		return $skills;
	}

	// Filters
	// 
	public function apply_visibility()
	{
		$this->where('is_hidden is null');
		return $this;
	}
}
