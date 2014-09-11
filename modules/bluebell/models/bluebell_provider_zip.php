<?php

class Bluebell_Provider_Zip extends Db_ActiveRecord
{
	public $table_name = 'bluebell_provider_zip';

	public $belongs_to = array(
		'provider' => array('class_name' => 'Service_Provider', 'foreign_key' => 'provider_id'),
	);

	public static function create()
	{
		return new self();
	}

	public function define_columns($context = null)
	{
		$this->define_column('zip', 'Zip');
		$this->define_relation_column('provider', 'provider', 'Provider', db_varchar, "@id");
	}
	
	public function define_form_fields($context = null)
	{
		$this->add_form_field('zip');

		$this->add_form_field('provider','full')->
			display_as(frm_record_finder, array(
				'sorting'=>'business_name',
				'list_columns'=>'business_name',
				'search_prompt'=>'Find provider by business name',
				'form_title'=>'Find Provider',
				'display_name_field'=>'business_name',
				'display_description_field'=>'id',
				'prompt'=>'Click the Find button to find a provider'));
	}

	public static function match_to_provider($provider)
	{
		$providers = Service_Provider::create();
		$providers->find_in_object_categories($provider);
		$providers->join('bluebell_provider_zip', 'bluebell_provider_zip.zip = service_providers.zip');
		$providers->where('bluebell_provider_zip.zip=?', $provider->zip);
		$providers->group('service_providers.id');
		return $providers;
	}

	public static function update_provider($provider)
	{
		if (isset($provider->fetched['service_codes']) && $provider->fetched['service_codes'] == $provider->service_codes)
			return;
		
		$zip_codes = explode('|', $provider->service_codes);

		if (!$provider || !isset($provider->id) || !$zip_codes || !is_array($zip_codes))
			return false;

		$bind = array('provider_id'=>$provider->id);
		$values_arr = array();

		foreach ($zip_codes as $zip)
		{
			$zip = trim($zip);

			if ($zip=="")
				continue;

			$values_arr[] = "(:provider_id, '".$zip."')";
		}

		if (!$values_arr)
			return;

		$values = implode(',', $values_arr);

		Db_Helper::query('delete from bluebell_provider_zip where provider_id=?', $provider->id);
		Db_Helper::query('insert ignore into bluebell_provider_zip (provider_id, zip) VALUES '.$values, $bind);
	}



}
