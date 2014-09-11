<?php

class Location_Config extends Core_Settings_Base
{
	public $record_code = 'location_config';

	public static function create()
	{
		$config = new self();
		return $config->load();
	}
	
	protected function build_form()
	{
		$this->add_field('default_country', 'Default Country', 'full', db_number)->display_as(frm_dropdown)->tab('General');
		$this->add_field('default_state', 'Default Country', 'full', db_number)->display_as(frm_dropdown)->tab('General');
		$this->add_field('default_unit', 'Distance Unit', 'full', db_number)->display_as(frm_dropdown)->tab('General');
		
		// Map marker image
		$this->add_relation('has_many', 'map_marker', array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Location_Config' and field='map_marker'", 'order'=>'id', 'delete'=>true));
		$this->define_multi_relation_column('map_marker', 'map_marker', 'Map Marker', '@name')->invisible();
		$this->add_form_field('map_marker', 'left')->display_as(frm_file_attachments)
			->display_files_as('single_image')
			->add_document_label('Upload map marker')            
			->no_attachments_label('Marker is not uploaded')
			->image_thumb_size(50)
			->no_label()            
			->tab('Maps')
			->comment('Recommended image dimensions 68px height by 42px width');
	}

	protected function init_config_data()
	{
		$this->default_country = 1;
		$this->default_state = 1;
		$this->default_unit = 'mi';
	}

	public function get_default_state_options($key_value = -1)
	{
		return Location_State::get_name_list($this->default_country);
	}

	public function get_default_country_options($key_value = -1)
	{
		return Location_Country::get_name_list();
	}

	public function get_default_unit_options($key_value = -1)
	{
		return array(
			'mi' => 'Miles',
			'km' => 'Kilometers'
		);
	}

	public function is_configured()
	{
		$config = self::create();
		if (!$config)
			return false;

		return true;
	}

	public static function get_map_marker()
	{
		$settings = self::create();
		if ($settings->map_marker->count > 0)
			return root_url($settings->map_marker->first()->get_path());
		else
			return null;
	}    
}