<?php

class Bluebell_Request_Manager 
{
	public static function create_request($user, $data, $files=array(), $session_key=null)
	{
		$request = Service_Request_Manager::validate_request($user, $data, $files, $session_key);
		$request->save(null, $session_key);
		$request = Service_Request::create()->find($request->id);
		
		// Send notification
		Notify::trigger('service:new_request', array('request'=>$request));

		// Private request, end here
		if ($request->type == Service_Request::type_private)
			return $request;
		
		// Queue the notification mailout
		Phpr_Cron::queue_job('Bluebell_Request_Manager::notify_providers', array($request->id));

		return $request;
	}

	public static function notify_providers($request_id)
	{
		$request = Service_Request::create()->find($request_id);
		if (!$request)
			return;

		// Geocode address if lat/lng not present
		if (!$request->latitude && !$request->longitude) 
			Location_Geocode::geocode_to_object($request, $request->address_string);

		// Notify providers
		//
		$providers = Bluebell_Provider::match_to_request($request);

		foreach ($providers as $provider)
		{
			Db_Helper::query('update service_providers set stat_offers = stat_offers+1 where id=:provider_id', array('provider_id'=>$provider->id));
			Notify::trigger('service:job_alert', array('request'=>$request, 'provider'=>$provider));
		}

	}

}