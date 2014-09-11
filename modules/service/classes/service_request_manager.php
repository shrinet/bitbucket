 <?php

class Service_Request_Manager 
{

	public static function set_request_files($request, $data, $files=array(), $session_key=null)
	{
		if (!isset($files['request_images']))
			return;

		$file_data = File_Upload::extract_multi_file_info($files['request_images']);
		$size = isset($data['request_file_size']) ? $data['request_file_size'] : '100x75';
		$size = Phpr_String::dimension_from_string($size);

		foreach ($file_data as $file)
		{
			$file = $request->save_attachment_from_post('files', $file, false, $session_key);
		}
		
		echo json_encode(array(
			'id' => $file->id,
			'thumb' => (($file->is_image()) ? $file->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop')) : null)
		));
	}

	public static function validate_request($user, $data, $files=array(), $session_key=null, $context=null)
	{
		// Create Service Request
		$request = Service_Request::create();
		$request->init_columns($context);

		// Set status
		$request->set_status(Service_Status::status_active);

		// Set expiry
		$duration = (isset($data['duration'])) ? $data['duration'] : c('request_default_length', 'Service');
		$request->expired_at = Phpr_DateTime::now()->add_days($duration);

		// Attempt to geocode address
		if (isset($data['address']))
			Location_Geocode::address_to_object($request, $data['address']);

		// Categories
		if (isset($data['category_id'])) 
		{
			$category_ids = is_array($data['category_id']) ? $data['category_id'] : array($data['category_id']);
			$categories = Service_Category::create()->where('id in (?)', array($data['category_id']))->find_all();
		}
		else {
			$categories = new Db_Data_Collection();
		}

		$request->categories = $categories;

		// Skills
		if (isset($data['skill_id'])) 
		{
			$skill_ids = is_array($data['skill_id']) ? $data['skill_id'] : array($data['skill_id']);
			$skills = Service_Skill::create()->find_all(array($data['skill_id']));
		}
		else {
			$skills = new Db_Data_Collection();
		}

		$request->skills = $skills;

		// Find orphaned files @todo used deferred binding
		$files = null;
		if (isset($data['files']))
		{
			$files = Db_File::create()->where('id in (?)', array($data['files']))
				->where('master_object_id is null')
				->find_all();

			foreach ($files as $file)
			{
				$request->files->add($file);
			}
		}        

		$request->user = $user;
		$request->validate_data($data);
		return $request;        
	}
	
	public static function create_request($user, $data, $files=array(), $session_key=null)
	{
		$request = self::validate_request($user, $data, $files, $session_key);
		$request->save(null, $session_key);
		$request = Service_Request::create()->find($request->id);
		
		// Send notification
		Notify::trigger('service:new_request', array('request'=>$request));

		// Private request, end here
		if ($request->type == Service_Request::type_private)
			return $request;
		
		// Notify providers
		//
		$providers = Service_Provider::match_to_request($request);
		foreach ($providers as $provider)
		{
			Db_Helper::query('update service_providers set stat_offers = stat_offers+1 where id=:provider_id', array('provider_id'=>$provider->id));
			Notify::trigger('service:job_alert', array('request'=>$request, 'provider'=>$provider));
		}

		return $request;
	}

	public static function expire_request($request, $session_key=null)
	{
		$request->set_status(Service_Status::status_expired);
		$request->save();

		// Send notification
		Notify::trigger('service:expire_request', array('request'=>$request));       
	}

	public static function close_request($request, $session_key=null)
	{
		$request->set_status(Service_Status::status_closed);
		$request->save();
	}

	public static function cancel_request($request, $session_key=null)
	{
		$request->set_status(Service_Status::status_cancelled);
		$request->save();
	}

	public static function invite_provider($request, $provider)
	{
		if (!($provider instanceof Service_Provider))
		{
			$provider = Service_Provider::create()->where('id=?', $provider)->find();
		}

		if (!$provider)
			return;

		$request->link_provider($provider, Service_Request::link_type_invited);
		
		// Send notification
		Notify::trigger('service:job_invite', array('request'=>$request, 'provider'=>$provider));
	}

	// Quotes
	// 

	public static function create_quote($user, $provider=null, $request=null, $data, $files=array(), $session_key=null)
	{
		// Find exisiting quote
		$quote = Service_Quote::create()
			->where('provider_id=?', $provider->id)
			->where('request_id=?', $request->id)
			->find();

		// Create quote
		if (!$quote)
		{
			$quote = Service_Quote::create();
			$quote->user = $user;
			$quote->request = $request;
			$quote->provider = $provider;
			$quote->set_status(Service_Quote_Status::status_new);
		}

		$quote->validate_data($data);

		$is_new = $quote->is_new_record();

		if ($is_new)
		{
			// Fee check
			Phpr_Module_Manager::module_exists('payment') && Payment_Fee::trigger_event('Service_Quote_Status_Event', array(
				'handler' => 'service:on_create_quote', 
				'previous_status' => null,
				'quote' => $quote
			));
			Service_Plan::quote_apply($provider);
		}

		$quote->deleted_at = null;
		$quote->save(null, $session_key);

		if ($is_new)
		{
			// Automatically approve @todo should be optional
			$quote->approve();

			// Send notification
			Notify::trigger('service:new_quote', array('quote'=>$quote));

			$request->total_quotes++;
			$request_max_bids = c('request_max_bids', 'service');
			if ($request_max_bids && $request->total_quotes >= $request_max_bids)
			{
				// Expire this request (max bids reached)
				Service_Request_Manager::expire_request($request);
			}
		}

		return $quote;
	}

	public static function delete_quote($quote)
	{
		$quote->deleted_at = Phpr_DateTime::now();
		$quote->save();
	}    

	public static function delete_quotes_for_user($user)
	{
		$quotes = Service_Quote::create()->apply_owner($user)->find_all();
		foreach ($quotes as $quote)
		{
			self::delete_quote($quote);
		}
	}

}