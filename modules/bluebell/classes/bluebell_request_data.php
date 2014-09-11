<?php

class Bluebell_Request_Data
{		

	public static function get_request_object()
	{
		$request_data = self::load();
		
		if (empty($request_data))
			return null;
		
		$request = Service_Request::create();
		$request->set_data($request_data);
		return $request;
	}

	public static function save_session_key()
	{
		$request_data = self::load();
		$request_data['session_key'] = post('session_key');
		self::save($request_data);
	}

	public static function get_session_key()
	{
		$request_data = self::load();
		if (isset($request_data['session_key']))
			return $request_data['session_key'];

		return null;
	}

	public static function save_role_name()
	{
		$request_data = self::load();

		$role_name = post_array('Request', 'title');
		if (!$role_name)
			return false;
		
		$category = Service_Category::create()->find_by_name(trim($role_name));
		if (!$category)
			return false;

		$request_data['title'] = $role_name;
		$request_data['category_id'] = $category->id;
		self::save($request_data);
		return true;
	}

	public static function save_request_data()
	{
		$request_data = self::load();
		$request_data = array_merge($request_data, post('Request', array()));

		// Geocode address
		if (post_array('Request', 'address')) {
			$request_data['is_remote'] = false;
			$request_data = Location_Geocode::address_to_array($request_data, post_array('Request', 'address'));
		}

		// Save custom data
		$request_data['custom_form_data'] = self::get_custom_form_data();

		self::save($request_data);
		self::save_required_by_data();
	}

	public static function get_custom_form_data()
	{
		$request_data = self::load();
		
		if (post('Custom'))
			return post('Custom');
		
		if (isset($request_data['custom_form_data']))
			return $request_data['custom_form_data'];

		return array();
	}

	public static function get_request_data()
	{
		$request_data = self::load();
		return $request_data;
	}

	public static function save_required_by_data() 
	{
		$request_data = self::load();

		if (isset($request_data['required_by']) && $request_data['required_by'] == "firm")
		{
			if (post('firm_date'))
			{
				$request_data['firm_start'] = Phpr_DateTime::parse(post('firm_date') . ' ' .post('firm_time_start', '09:00:00'), '%x %H:%M:%S')->format('%x %I:%M %p');
				$request_data['firm_end'] = Phpr_DateTime::parse(post('firm_date') . ' ' .post('firm_time_end', '10:00:00'), '%x %H:%M:%S')->format('%x %I:%M %p');
			}

			if (post('firm_date_alt'))
			{
				$request_data['firm_alt_start'] = Phpr_DateTime::parse(post('firm_date_alt') . ' ' .post('firm_time_alt_start', '09:00:00'), '%x %H:%M:%S')->format('%x %I:%M %p');
				$request_data['firm_alt_end'] = Phpr_DateTime::parse(post('firm_date_alt') . ' ' .post('firm_time_alt_end', '10:00:00'), '%x %H:%M:%S')->format('%x %I:%M %p');
			}
		}

		self::save($request_data);
		return $request_data;
	}	

	public static function reset_all()
	{
		$request_data = array();
		self::save($request_data);
	}

	protected static function load()
	{
		return Phpr::$session->get('bluebell_request_data', array());
	}
	
	protected static function save(&$data)
	{
		Phpr::$session['bluebell_request_data'] = $data;
	}
}

