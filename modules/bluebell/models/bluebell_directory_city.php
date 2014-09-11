<?php

class Bluebell_Directory_City extends Db_ActiveRecord
{
	public $table_name = 'bluebell_directory_cities';

	public $belongs_to = array(
		'country'=>array('class_name'=>'Location_Country', 'foreign_key' => 'country_id'),
		'state'=>array('class_name'=>'Location_State', 'foreign_key' => 'state_id'),
	);

	public static function create()
	{
		return new self();
	}

	public function define_columns($context = null)
	{
		$this->define_column('zip', 'Zip');
		$this->define_column('name', 'City');
		$this->define_relation_column('country', 'country', 'Country', db_varchar, '@name')->default_invisible();
		$this->define_relation_column('state', 'state', 'State', db_varchar, '@name')->default_invisible();        
	}
	
	public function define_form_fields($context = null)
	{
		$this->add_form_field('name', 'left')->tab('Contact');
		$this->add_form_field('zip', 'right')->tab('Contact');
		$this->add_form_field('country', 'left')->tab('Contact')->display_as(frm_dropdown)->empty_option('Select an Option');
		$this->add_form_field('state', 'right')->tab('Contact')->display_as(frm_dropdown);
	}

	public function find_area($country=null, $state=null, $city=null)
	{
		if ($country)
			$this->where('country_id=?', $country->id);

		if ($state)
			$this->where('state_id=?', $state->id);
		
		if ($city)
			$this->where('url_name=?', $city);

		// Prevent duplicates
		//$this->group('url_name');

		$this->where('is_seed is null');
		return $this;
	}

	public static function process_directory($limit=1)
	{
		$dir_items = self::create()->where('is_seed=1 AND is_processed is null')->limit($limit)->find_all();

		if (!$dir_items)
			return; 

		foreach ($dir_items as $item)
		{
			if (!$item->country)
				$item->delete();

			$areas = Bluebell_Geocode::get_nearby_areas($item->zip, $item->country->code);
			if (!isset($areas->postalCodes) || !is_array($areas->postalCodes))
			{
				$item->delete();
				return;
			}
			
			foreach ($areas->postalCodes as $area)
			{
				$bind_state = array('name'=>$area->adminName1, 'code'=>$area->adminCode1);
				$state = Location_State::create()->where('location_states.name=:name OR location_states.name=:code', $bind_state)->find();

				$bind = array(
					'name' => $area->name,
					'url_name' => Phpr_Inflector::slugify($area->name),
					'state' => ($state) ? $state->id : 'null',
					'country' => $item->country_id,
					'zip' => $item->zip
				);

				Db_Helper::query('insert ignore into bluebell_directory_cities 
					(name, url_name, is_processed, state_id, country_id, zip) values 
					(:name, :url_name, 1, :state, :country, :zip)', $bind);        
			}

			$item->is_processed = true;
			$item->save();
		}
	}

	public static function update_provider($provider)
	{
		if (isset($provider->fetched['service_codes']) && $provider->fetched['service_codes'] == $provider->service_codes)
			return;
		
		$zip_codes = explode('|', $provider->service_codes);

		if (!$provider || !isset($provider->id) || !$zip_codes || !is_array($zip_codes))
			return false;

		$bind = array(
			'country_id'=>$provider->country_id,
			'state_id'=>$provider->state_id,
			'city'=>$provider->city,
			'url_name'=>Phpr_Inflector::slugify($provider->city)
		);
		$values_arr = array();
		
		$values_arr[] = "(:city, :url_name, :state_id, :country_id, '".$provider->zip."', null, 1)";

		foreach ($zip_codes as $zip)
		{
			$zip = trim($zip);

			if ($zip=="")
				continue;

			$values_arr[] = "(1, 1, :state_id, :country_id, '".$zip."', 1, null)";
		}

		if (!$values_arr)
			return;

		$values = implode(',', $values_arr);

		Db_Helper::query('insert ignore into bluebell_directory_cities (name, url_name, state_id, country_id, zip, is_seed, is_processed) VALUES '.$values, $bind);        

	}

}
