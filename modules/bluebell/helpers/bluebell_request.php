<?php

class Bluebell_Request 
{
	// Request booked status
	const status_booked = 'booked';
	const status_booked_cancelled = 'booked_cancelled';

	const required_by_flexible = 'flexible';
	const required_by_urgent = 'urgent';
	const required_by_firm = 'firm';

	const required_type_flexible = 'flexible';
	const required_type_flexible_week = 'flexible_week';
	const required_type_flexible_month = 'flexible_month';

	// Displays location as a string
	public static function location($request, $is_short=false)
	{
		$default = ($is_short) ? __('Anywhere', true) : __('Job can be performed remotely', true);
		
		return ($request->is_remote || !$request->location_string) 
			? $default
			: $request->location_string;
	}

	// Displays time as a string
	public static function required_by($request)
	{
		switch ($request->required_by)
		{
			case self::required_by_flexible:
				switch ($request->required_type)
				{
					case self::required_type_flexible: default: return __('Flexible (anytime)', true); break;
					case self::required_type_flexible_week: return __('Flexible (within 7 days)', true); break;
					case self::required_type_flexible_month: return __('Flexible (within 30 days)', true); break;
				}
			break;

			case self::required_by_urgent:
				return __('Urgent (As soon as possible)', true);
			break;

			case self::required_by_firm:
				$str = __('Flexible (anytime)', true);

				if (!($request->firm_start instanceof Phpr_DateTime)) 
					$request->firm_start = Phpr_DateTime::parse($request->firm_start, '%x %H:%M');
				if (!($request->firm_end instanceof Phpr_DateTime)) 
					$request->firm_end = Phpr_DateTime::parse($request->firm_end, '%x %H:%M');
				if ($request->firm_start && $request->firm_end)
				{
					$str = $request->firm_start->to_short_date_format() . " " 
					 . $request->firm_start->format('%I:%M %p') . " - " 
					 . $request->firm_end->format('%I:%M %p');
				}

				if ($request->firm_alt_start && $request->firm_alt_end)
				{
					if (!($request->firm_alt_start instanceof Phpr_DateTime)) 
						$request->firm_alt_start = Phpr_DateTime::parse($request->firm_alt_start, '%x %H:%M');
					if (!($request->firm_alt_end instanceof Phpr_DateTime)) 
						$request->firm_alt_end = Phpr_DateTime::parse($request->firm_alt_end, '%x %H:%M');

					if ($request->firm_alt_start && $request->firm_alt_end)
					{
						$str .= ', ' 
							 . $request->firm_alt_start->to_short_date_format() . " " 
							 . $request->firm_alt_start->format('%I:%M %p') . " - " 
							 . $request->firm_alt_end->format('%I:%M %p');
					}
				}

				return $str;
			break;

			default:
				return __('Flexible (anytime)', true);
			break;
		}
	}

	public static function get_bookings($user)
	{
		$activity = Db_Data_Feed::create();
		$activity->add(Service_Request::create()->apply_user_quote_with_status($user, Service_Quote_Status::status_accepted)->apply_status(Bluebell_Request::status_booked));
		$activity->add(Service_Request::create()->apply_status(Bluebell_Request::status_booked)->apply_owner($user));
		return $activity;
	}
	
	public static function get_need_review($user)
	{
		$activity = Db_Data_Feed::create();
		$activity->add(Service_Request::create()->apply_user_quote_with_status($user, Service_Quote_Status::status_accepted)->apply_status(Service_Status::status_closed)->apply_user_todo_ratings($user));
		$activity->add(Service_Request::create()->apply_status(Service_Status::status_closed)->apply_owner($user)->apply_user_todo_ratings($user));
		return $activity;
	}

	public static function get_category_from_role_name($role_name)
	{
		$role_name = trim($role_name);
		if (!strlen($role_name))
			return null;

		return Service_Category::create()->find_by_name($role_name);
	}

	public static function get_custom_form_fields($request)
	{
		if (!$request->custom_form_data)
			return false;

		$category = self::get_category_from_role_name($request->title);

		if (!$category)
			return false;

		$data = (is_array($request->custom_form_data)) ? $request->custom_form_data : Phpr_Xml::to_array($request->custom_form_data);
		$form = $category->form;
		if (!$form || !$form->fields)
			return false;

		$result = array();
		foreach ($form->fields as $field)
		{
			$code = $field->code;
			if (isset($data[$code]))
				$field->current_value = is_array($data[$code]) ? implode(', ', $data[$code]) : $data[$code];

			if (strlen(trim($field->current_value)))
				$result[] = $field;
		}

		return $result;
	}

	public static function match_to_user_providers($user)
	{
		if (!$user)
			return null;

		$provider_ids = Service_Provider::get_profile_ids_from_user($user);

		$requests = Service_Request::create();
		
		// Filter categories
		$requests->apply_provider_categories($provider_ids, $user->id);
			
		// Where provider has permission to view
		$requests->apply_provider_visibility($provider_ids);

		return $requests;
	}


	/**
	 * Match a request's location to a provider's work area
	 * @param  Service_Request  $request  Request model object
	 * @param  Service_Provider $provider Provider model object
	 * @return Service_Provider
	 */
	public static function match_to_area($requests)
	{
		$default_unit = Location_Config::create()->default_unit;
		$bind = array(
			'unit' => ($default_unit == 'km')  ? 6371 : 3959,
		);

		$requests->join('service_providers', 'service_providers.id = service_categories_providers.provider_id');
		$requests->where("(service_requests.is_remote is not null) or 
			(( :unit * acos( 
				cos( radians( service_providers.latitude ) ) 
				* cos( radians( service_requests.latitude ) ) 
				* cos( radians( service_requests.longitude ) - radians( service_providers.longitude ) ) 
				+ sin( radians( service_providers.latitude ) ) 
				* sin( radians( service_requests.latitude ) ) 
			) ) < service_providers.service_radius)", $bind);

		return $requests;
	}


}
