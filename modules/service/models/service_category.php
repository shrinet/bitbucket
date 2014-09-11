<?php

class Service_Category extends Db_ActiveRecord
{
	public $implement = 'Db_AutoFootprints, Db_Act_As_Tree, Db_Model_Csv, Db_Model_Eventful';
	public $act_as_tree_parent_key = 'parent_id';
	public $act_as_tree_sql_filter = null;
	public $auto_footprints_visible = true;
	public $csv_file_name = 'service_category_export.csv';
	public $csv_columns = array('parent', 'name', 'description', 'keywords', 'code', 'is_hidden');

	public $belongs_to = array(
		'parent' => array('class_name' => 'Service_Category', 'foreign_key'=>'parent_id')
	);

	public $has_and_belongs_to_many = array(
		'related_categories' => array('class_name'=>'Service_Category', 'join_table'=>'service_categories_categories', 'foreign_key'=>'related_category_id', 'primary_key'=>'category_id'),
		'requests' => array('class_name'=>'Service_Request', 'join_table'=>'service_categories_requests', 'foreign_key'=>'request_id', 'primary_key'=>'category_id')
	);

	public $has_many = array(
		'children_categories' => array('class_name'=>'Service_Category', 'foreign_key'=>'parent_id', 'primary_key'=>'category_id')
	);

	public function define_columns($context = null)
	{
		$this->define_relation_column('parent', 'parent', 'Parent', db_varchar, '@name')->default_invisible();
		$this->define_column('name', 'Name');
		$this->define_column('description', 'Description');
		$this->define_column('keywords', 'Keywords')->default_invisible();
		$this->define_column('code', 'API Code')->default_invisible();
		$this->define_column('is_hidden', 'Hide')->default_invisible();

		$this->define_multi_relation_column('related_categories', 'related_categories', 'Similar Categories', '@name')->default_invisible()->validation();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('name')->tab('Category');
		$this->add_form_field('description')->tab('Category')->size('small');
		$this->add_form_field('keywords')->tab('Category')->display_as(frm_tags, array('available_tags'=>array('hello', 'hello2')));

		$this->add_form_field('is_hidden', 'left')->tab('Category')->comment('Hide category from category lists.');
		$this->add_form_field('code','right')->tab('Category');

		$this->add_form_field('parent')->tab('Related')->empty_option('<none>')->options_html_encode(true);
		$this->add_form_field('related_categories')->tab('Related')->comment('Select any categories this category is similar to.', 'above')->reference_sort('name');
	}

	// Events
	// 

	public function after_delete()
	{
		Db_Helper::query('delete from service_categories_categories where category_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_categories_categories where related_category_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_categories_providers where category_id=:id', array('id'=>$this->id));
	}

	public function before_delete($session_key = null)
	{
		$in_use = Db_Helper::scalar('select count(*) from service_categories where parent_id=:id', array('id'=>$this->id));

		if ($in_use)
			throw new Phpr_ApplicationException("Unable to delete the category because it has child categories.");
	}    

	public function after_create($session_key = null)
	{
		$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->name, 90);
		$bind = array(
			'id' => $this->id,
			'url_name' => $this->url_name
		);
		Db_Helper::query('update service_categories set url_name=:url_name where id=:id', $bind);

	}

	public function before_update($session_key = null)
	{
		if (isset($this->fetched['name']) && $this->fetched['name'] != $this->name)
			$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->name, 90);
	}

	// Getters
	// 
	
	public static function get_popular_categories()
	{
		$categories = self::create();
		$categories->join('service_categories_requests', 'service_categories_requests.category_id = service_categories.id');
		$categories->group('service_categories.id');
		$categories->order('COUNT(service_categories.id) DESC');
		return $categories;
	}

	public function get_prefix_string($prefix = '&mdash;')
	{
		$category = $this;
		$depth = '';

		while ($category->parent) {
			$depth .= $prefix . ' ';
			$category = $category->parent;
		}

		return $depth;
	}

	// Service methods
	// 


	public static function search_categories($query, $options=array())
	{
		$options = array_merge(array(
			'min_length' => 2,
			'target_name' => null
		), $options);

		extract($options);

		$bind = array(); 
		$search_query = Db_Helper::format_search_query($query, array('name', 'keywords'), $min_length);

		if ($target_name) {
			$bind['target_name'] = $target_name;
			$search_query .= ' and name=:target_name';
		}

		$data = Db_Helper::object_array("select id, parent_id, name from service_categories where ".$search_query, $bind);
		return $data;
	}

	// Filters
	// 

	public function apply_visibility()
	{
		$this->where('is_hidden is null');
		return $this;
	}
}
