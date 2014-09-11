<?php

class Bluebell_Gateway
{
	protected static $instance = null;

	public static function create()
	{
		if (!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	public function request($type, $params = array(), $raw = false)
	{
		return $this->request_server_data($type.'/'.$this->get_hash(), $params, $raw);
	}

	// ### TODO LICENSE_POINT
	protected function request_server_data($url, $params = array(), $raw = false)
	{

		$server_url = 'scriptsahoy.com/ahoy_webservices';
		if (!strlen($server_url))
			throw new Exception('Location server cannot be found');

		$result = null;
		try
		{
			$net = Net_Request::create('http://'.$server_url.'/'.$url);
			$net->disable_redirects();
			$net->set_post($params);
			$response = $net->send();
			$result = $response->data;
		} 
		catch (Exception $ex) {}

		if (!$result || !strlen($result))
			throw new Exception("Error connecting to the location server");

		if ($raw)
			return $result;

		$result_data = false;
		try
		{
			$result_data = @json_decode($result);
		} catch (Exception $ex) {
			throw new Exception("Invalid response from the location server");
		}

		if ($result_data === false)
			throw new Exception("Invalid response from the location server");

		if ($result_data->error)
			throw new Exception($result_data->error);

		return $result_data;
	}

	protected function get_hash()
	{
		// Debug
		return '3e2686baf262f9f6da5f7dabadbe5724';

		$hash = Phpr_Module_Parameters::get('core', 'license_hash');
		if (!$hash)
			throw new Phpr_ApplicationException('License information not found');

		$framework = Phpr_SecurityFramework::create();
		return $framework->decrypt(base64_decode($hash));
	}
}