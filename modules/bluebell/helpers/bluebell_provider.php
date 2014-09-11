<?php

class Bluebell_Provider 
{

	public static function avatar($provider)
	{
		$photo = theme_url('assets/images/avatar_thumb.jpg', true, true);
		
		if ($provider)
			$photo = $provider->get_photo(100, $photo);

		return $photo;
	}

	public static function logo($provider)
	{
		$logo = theme_url('assets/images/avatar_thumb.jpg', true, true);
		
		if ($provider)
			$logo = $provider->get_logo(100, $logo);

		return $logo;
	}

	public static function match_to_request($request)
	{
		$providers = Service_Provider::create();

		// Filter categories
		$providers->find_in_object_categories($request);

		// Filter work area
		$providers = self::match_to_area($request, $providers);

		$providers = $providers->find_all();

		return $providers;
	}
	
	public static function match_to_request2($request)
	{
		$providers = Service_Provider::create()->find_in_object_categories($request)->find_all();
		
		// Filter categories
		#$providers->find_in_object_categories($request);
		
		// Filter work area
		$providers = self::match_to_area($request, $providers);
		
		$providers = $providers->find_all();
		
		return $providers;
	}


	/**
	 * Match a request's location to a provider's work area
	 * @param  Service_Request  $request  Request model object
	 * @param  Service_Provider $provider Provider model object
	 * @return Service_Provider
	 */

	public static function match_to_area($request, $provider)
	{
		if ($request->is_remote)
			return $provider;

		$default_unit = Location_Config::create()->default_unit;
		$bind = array(
			'unit' => ($default_unit == 'km')  ? 6371 : 3959,
			'lat' => $request->latitude,
			'lng' => $request->longitude
		);

		$provider->where("( :unit * acos( 
				cos( radians( :lat ) ) 
				* cos( radians( service_providers.latitude ) ) 
				* cos( radians( service_providers.longitude ) - radians( :lng ) ) 
				+ sin( radians( :lat ) ) 
				* sin( radians( service_providers.latitude ) ) 
			) ) < service_providers.service_radius", $bind);

		return $provider;
	}

	/**
	 * Match a request's schedule to a provider
	 * @param  Service_Request  $request  Request model object
	 * @param  Service_Provider $provider Provider model object
	 * @return boolean
	 */
	public static function match_to_schedule($request, $provider)
	{
		// Ozlancer code
		$user = unserialize($user_schedule);
		$request = unserialize($request_schedule);

		$day = strtolower(date('D', strtotime($request[0]['date'])));
		if (isset($user[$day]))
		{
			$r_from = strtotime($request[0]['from']);
			$r_to = strtotime($request[0]['to']);
			$u_from = strtotime($user[$day]['from']);
			$u_to = strtotime($user[$day]['to']);

			if (($u_from >= $r_from) && ($u_from <= $r_to))
				return true;
			else if (($u_to >= $r_from) && ($u_to <= $r_to))
				return true;
			else if ($r_from > $u_from && $r_to < $u_to)
				return true;
			else
				return false;
		}
		return false;
	}

}