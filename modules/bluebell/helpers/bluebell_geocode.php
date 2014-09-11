<?php

// For functional details see ../vendor/geocoder/README.md

class Bluebell_Geocode
{
	public static function get_nearby_areas($zip, $country, $radius=1, $raw=false)
	{
		try 
		{
			$data = array(
				'country' => $country,
				'postalcode' => urlencode($zip),
				'radius' => $radius,
				'maxRows' => 500
			);
			return Bluebell_Gateway::create()->request('nearby_postcode', $data, $raw);
		} 
		catch (Exception $e)
		{			
			return null;
		}
	}

	public static function get_postcode($city, $country, $raw=false)
	{
		try 
		{        
			$data = array(
				'country' => $country,
				'city' => $city,
				'maxRows' => 1
			);
			return Bluebell_Gateway::create()->request('get_postcode', $data, $raw);
		} 
		catch (Exception $e)
		{
			return null;
		}        
	}
}
