<?php

class Bluebell_Actions extends Bluebell_Directory_Actions
{

	// 
	// Home
	// 

	public function home()
	{
		// Featured providers
		$featured = array();
		$featured_group = Service_Provider_Group::create()->find_by_code('featured');
		if ($featured_group && $featured_group->providers)
		{
			foreach ($featured_group->providers as $provider)
			{
				$portfolio = $provider->get_portfolio();
				if (!$portfolio)
					continue;

				$first_image = reset($portfolio);
				$first_image->provider = $provider;
				$featured[] = $first_image;
			}
		}

		// Activity
		$activity = Db_Data_Feed::create();
		$activity->add(Service_Provider::create());
		$activity->add(Service_Request::create()->apply_visibility());
		$activity->limit(6);

		$this->data['featured_providers'] = $featured;
		$this->data['activity'] = $activity->find_all();
		$this->data['categories'] = Service_Category::get_popular_categories()->where('parent_id is not null')->limit(10)->find_all();
		$this->data['blog_post'] = Blog_Post::create()->limit(1)->find();
	}

	//
	// Dash
	// 

	public function dashboard()
	{
		// Requests
		$requests = Service_Request::create()->where('user_id=?', $this->user->id)->order('expired_at desc')->limit(3)->find_all();

		// Job offers
		if ($this->user->is_provider)
		{
			$latest_profile = Service_Provider::create()->where('user_id=?',$this->user->id)->limit(1)->order('updated_at desc')->find();
			$provider_ids = Service_Provider::get_profile_ids_from_user($this->user);

			$jobs = Bluebell_Request::match_to_user_providers($this->user);
			$jobs = Bluebell_Request::match_to_area($jobs);
			$jobs->apply_provider_link($provider_ids, Service_Request::link_type_banned, true);
			$jobs->order('expired_at desc');
			$jobs->limit(3);
			$jobs->apply_status(Service_Status::status_active);
			$jobs = $jobs->find_all();
				
			$this->data['latest_profile'] = $latest_profile;
			$this->data['jobs'] = $jobs;
		}
		$this->data['requests'] = $requests;
	}

	public function on_dashboard_filter_job_offers()
	{
		// Job offers
		$filter = post('filter');

		$provider_ids = Service_Provider::get_profile_ids_from_user($this->user);

		$jobs = Bluebell_Request::match_to_user_providers($this->user);
		$jobs->order('expired_at desc');
		$jobs->limit(3);

		switch ($filter)
		{
			case "active":
				$jobs = Bluebell_Request::match_to_area($jobs);
				$jobs->apply_status(Service_Status::status_active)->apply_provider_link($provider_ids, Service_Request::link_type_banned, true);
			break;
			case "booked":
				$jobs->apply_status(array(Bluebell_Request::status_booked, Service_Status::status_closed))->apply_user_quote_with_status($this->user, Service_Quote_Status::status_accepted);
			break;
			case "ended":
				$activity = Db_Data_Feed::create();

				// Matched jobs that ended
				$ended_jobs = clone $jobs;
				$ended_jobs = Bluebell_Request::match_to_area($ended_jobs);
				$activity->add($ended_jobs->apply_status(array(Service_Status::status_expired, Service_Status::status_cancelled, Bluebell_Request::status_booked_cancelled)));
				
				// Quoted jobs that were lost
				$lost_jobs = clone $jobs;
				$activity->add($lost_jobs->apply_status(array(Bluebell_Request::status_booked, Service_Status::status_closed))->apply_user_quote_with_status($this->user, Service_Quote_Status::status_accepted, true));
				$jobs = $activity;

			break;
		}

		$this->data['jobs'] = $jobs->find_all();
		$this->data['filter'] = $filter;
	}

	//
	// Account
	// 

	public function account()
	{
		$profiles = Service_Provider::create()->where('user_id=?', $this->user->id)->find_all();
		$this->data['profiles'] = $profiles;

		$invoices = Payment_Invoice::create()->where('user_id=?', $this->user->id)->find_all();
		$this->data['invoices'] = $invoices;

		if ($mode = $this->request_param(0))
			$_POST['mode'] = $mode;

		$this->on_account_filter_history();

		Cms_Action_Manager::exec_action('user:account', $this);
	}
	
	public function on_profile_create_rating()
	{
		#print_r($_POST);
		Phpr::$response->ajaxReportException('You are not permitted');
	}
	
	public function on_account_filter_history()
	{

		$page = post('page', 1);
		$mode = post('mode', ($this->user->is_provider) ? 'offers' : 'requests');
		$submode = post('submode', 'open');

		if ($mode == 'offers')
		{
			$provider_ids = Service_Provider::get_profile_ids_from_user($this->user);

			$jobs = Bluebell_Request::match_to_user_providers($this->user);
			$jobs->order('expired_at desc');

			if ($submode == 'open') {
				$jobs = Bluebell_Request::match_to_area($jobs);
				$jobs->apply_status(Service_Status::status_active);
			}
			else if ($submode == 'performed')
				$jobs->apply_status(array(Bluebell_Request::status_booked, Service_Status::status_closed))->apply_user_quote_with_status($this->user, Service_Quote_Status::status_accepted);
			else if ($submode == 'lost')
				$jobs->apply_status(Bluebell_Request::status_booked)->apply_user_quote_with_status($this->user, Service_Quote_Status::status_new);
			else if ($submode == 'cancelled') {
				$jobs = Bluebell_Request::match_to_area($jobs);
				$jobs->apply_status(Service_Status::status_cancelled);
			}
			
			if ($submode == 'ignored')
				$jobs->apply_provider_link($provider_ids, Service_Request::link_type_banned);
			else
				$jobs->apply_provider_link($provider_ids, Service_Request::link_type_banned, true);

			$activity = $jobs;

		}
		else
		{
			$requests = Service_Request::create()->where('user_id=?', $this->user->id)->order('expired_at desc');
			$activity = $requests;
		}

		$pagination = $activity->paginate($page-1, 5);
		$activity = $activity->find_all();
		
		if ($pagination->get_row_count()<=0)
			$activity = $pagination = null;

		$this->data['page'] = $page;
		$this->data['pagination'] = $pagination;
		$this->data['activity'] = $activity;

		$this->data['mode'] = $mode;
		$this->data['submode'] = $submode;
	}

	// 
	// Booking
	// 

	public function booking()
	{
		Cms_Action_Manager::exec_action('service:request', $this);
		extract($this->data);

		if (!$provider || !$quote)
		{
			$this->data['quote'] = $quote = Service_Quote::create()
				->where('request_id=?', $request->id)
				->apply_status(Service_Quote_Status::status_accepted)
				->find();

			if (!$quote)
				return $this->data['provider'] = null;

			$this->data['provider'] = $provider = $quote->provider;
		}

		$this->data['is_cancelled'] = $is_cancelled = ($request->status->code == Bluebell_Request::status_booked_cancelled);
		$this->data['is_success'] = $this->request_param(1, false);
		$this->data['is_provider'] = $is_provider = ($this->user) && ($request->user_id != $this->user->id);
		$this->data['opp_user_name'] = $opp_user_name = $is_provider ? $request->user->name : $provider->business_name;
		$this->data['opp_user_id'] = $opp_user_id = $is_provider ? $request->user_id : $provider->user_id;
		$this->data['rating'] = ($this->user) ? Service_Rating::is_rated($request->id, $this->user->id, $opp_user_id) : null;
		$this->data['can_rate'] = ($is_cancelled||$request->status->code == Service_Status::status_closed);
	}

	public function on_cancel_booking()
	{
		if (!post('quote_id'))
			throw new Cms_Exception('Missing ID');

		$quote = Service_Quote::create()->apply_security($this->user)->find(post('quote_id'));
		if (!$quote)
			throw new Cms_Exception(__('Sorry that request could not be found'));

		$request = $quote->request;
		$request->set_status(Bluebell_Request::status_booked_cancelled);
		$request->save();

		$this->data['request'] = $request;

		// Send notifications
		Notify::trigger('bluebell:cancel_customer_booking', array('quote'=>$quote));
		Notify::trigger('bluebell:cancel_provider_booking', array('quote'=>$quote));

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);

		if (!post('no_refresh'))
			Phpr::$response->redirect($request->get_url('job/booking'));
	}

	//
	// Profile
	// 

	public function profile()
	{
		Cms_Action_Manager::exec_action('service:provider', $this);
		extract($this->data);

		if (!$provider)
			return;
		
		$controller = Cms_Controller::get_instance();
    if ($controller->user)
			$jobs = Bluebell_Request::get_need_review($controller->user)->find_all();
		
		$this->data['jobs'] = $jobs;
		$other_providers = Bluebell_Provider_Zip::match_to_provider($provider)->limit(5)->find_all();        
		$this->data['other_providers'] = $other_providers;
	}

	//
	// Requests
	// 

	public function review_request()
	{
		$session_key = Bluebell_Request_Data::get_session_key();
		if ($session_key)
			$_POST['session_key'] = $session_key;

		$this->data['request'] = Bluebell_Request_Data::get_request_object();
	}

	public function on_review_request()
	{
		Bluebell_Request_Data::save_session_key();
		Bluebell_Request_Data::save_request_data();

		// Validate role name
		if (!Bluebell_Request_Data::save_role_name())
			Phpr::$response->redirect(post('redirect_assist', root_url('request/assist')));

		$_POST['Request'] = Bluebell_Request_Data::get_request_data();
		$this->exec_ajax_handler('service:on_review_request');
	}

	public function on_update_request_category()
	{
		Bluebell_Request_Data::save_session_key();
		Bluebell_Request_Data::save_request_data();
		$request = Bluebell_Request_Data::get_request_object();
		$this->data['request'] = $request;
		$this->data['role_name'] = post('role_name', $request->title);
	}

	public function create_request()
	{
		$this->exec_action('service:create_request');

		// Populate the role name (if possible)
		$role_name = post('role_name', null);
		if ($this->data['category'])
			$role_name = $this->data['category']->name;

		if ($this->request_param(0) != "edit") 
			Bluebell_Request_Data::reset_all();

		// Editing
		$request = Bluebell_Request_Data::get_request_object();
		if ($request)
			$role_name = $request->title;
		else
			$request = Service_Request::create();

		$this->data['request'] = $request;
		$this->data['role_name'] = $role_name;
	}

	public function on_create_request()
	{
		Bluebell_Request_Data::save_request_data();
		$_POST['Request'] = Bluebell_Request_Data::get_request_data();
		$_POST['Request']['custom_form_data'] = Phpr_Xml::from_array($_POST['Request']['custom_form_data']);

		//
		// Following code is duplicate of service:on_create_request
		// with the difference of using Bluebell_Request_Manager::create_request
		// 

		$_POST = array_merge($_POST, post('Request', array()));

		// Login or register
		if (!$this->user) {
			if (post_array('User', 'login')) {
				Cms_Action_Manager::exec_ajax_handler('user:on_login', $this);
				$this->user = Phpr::$frontend_security->authorize_user();
			}
			else if (post_array('User', 'email'))
				$this->user = Cms_Action_Manager::exec_ajax_handler('user:on_register', $this);
		}

		// Fee check
		Phpr_Module_Manager::module_exists('payment') && Payment_Fee::trigger_event('Service_Request_Submit_Event', array('handler'=>'service:on_create_request'));

		$request = Bluebell_Request_Manager::create_request($this->user, $_POST, $_FILES, post('session_key'));

		// Invite providers
		if (post('invite_providers')) {
			$invited_providers = post('invite_providers');
			if (!is_array($invited_providers))
				$invited_providers = array($invited_providers);

			foreach ($invited_providers as $invited_provider)
			{
				Service_Request_Manager::invite_provider($request, $invited_provider);
			}
		}

		$this->data['request'] = $request;
		if ($redirect = post('redirect_request'))
			Phpr::$response->redirect($request->get_url($redirect));

		return $request;
	}

	// 
	// Provider
	// 

	public function on_update_provider_description()
	{
		$description = "";
		$description .= post_array('Provider', 'description_experience') . PHP_EOL.PHP_EOL;
		$description .= post_array('Provider', 'description_speciality') . PHP_EOL.PHP_EOL;
		$description .= post_array('Provider', 'description_why_us') . PHP_EOL.PHP_EOL;
		$_POST['Provider']['description'] = trim($description);

		Cms_Action_Manager::exec_ajax_handler('service:on_update_provider', $this);
	}

	public function on_create_provider()
	{
		// Populate category IDs from role name
		if ($role_name = post_array('Provider', 'role_name'))
			$_POST['Provider']['categories'] = $this->find_categories_from_role_name($role_name);

		// No nearby areas have been found
		if (post_array('Provider', 'service_codes') == '||')
			$_POST['Provider']['service_codes'] = '|'.post_array('Provider', 'zip').'|';

		// Save address details to user
		if ($this->user) {
			$this->user->password = null;
			$this->user->save($_POST['Provider']);
		}

		Cms_Action_Manager::exec_ajax_handler('service:on_create_provider', $this);
	}

	// Inherits service:on_update_provider
	public function on_update_provider()
	{
		// Populate category IDs from role name
		if ($role_name = post_array('Provider', 'role_name'))
			$_POST['Provider']['categories'] = $this->find_categories_from_role_name($role_name);

		// No nearby areas have been found
		if (post_array('Provider', 'service_codes') == '||')
			$_POST['Provider']['service_codes'] = '|'.post_array('Provider', 'zip').'|';

		Cms_Action_Manager::exec_ajax_handler('service:on_update_provider', $this);
	}

	//
	// Quote
	// 

	// Inherits service:on_create_quote
	public function on_create_quote()
	{
		$quote_type = post_array('Quote', 'quote_type');
		// Quote submitted
		if ($quote_type)
		{
			$wipe_fields = array();
			$final_price = 0;

			// Flat quote
			if ($quote_type == Bluebell_Quote::quote_type_flat_rate)
			{

				$final_price += floatval(post_array('Quote', 'flat_labor_price', 0));

				// Build flat price line items
				$flat_item_descriptions = post_array('Quote', 'flat_item_description', array());
				$flat_item_prices = post_array('Quote', 'flat_item_price');
				$flat_items = array();
				foreach ($flat_item_descriptions as $key=>$value)
				{
					$description = $value;
					$price = isset($flat_item_prices[$key]) ? $flat_item_prices[$key] : 0;
					if (strlen($description)&&strlen($price))
					{
						$flat_items[] = array('description'=>$description, 'price'=>$price);
						$final_price += floatval($price);
					}

				}

				$_POST['Quote']['flat_items'] = json_encode($flat_items);

				$wipe_fields = array('onsite_price_start',
									 'onsite_price_end',
									 'onsite_travel_required',
									 'onsite_travel_price',
									 'onsite_travel_waived');

			}
			// On site
			else if ($quote_type == Bluebell_Quote::quote_type_onsite)
			{ 
				$start_price = post_array('Quote', 'onsite_price_start');
				$end_price = post_array('Quote', 'onsite_price_end');

				if ($start_price && $end_price)
					$final_price = (floatval($start_price) + floatval($end_price)) / 2;
				else if ($start_price)
					$final_price = $start_price;
				else
					$final_price = $end_price;

				if (post_array('Quote', 'onsite_travel_required') && !post_array('Quote', 'onsite_travel_waived'))
					$final_price += floatval(post_array('Quote', 'onsite_travel_price'));

				$wipe_fields = array('flat_items',
									 'flat_labor_description',
									 'flat_labor_price');
			}

			foreach ($wipe_fields as $field)
				$_POST['Quote'][$field] = null;

			$_POST['Quote']['price'] = $final_price;
		}

		Cms_Action_Manager::exec_ajax_handler('service:on_create_quote', $this);
	}

	// Inherits service:on_update_quote_status
	public function on_accept_request_quote()
	{
		if (!$this->user->id||!post('request_id')||!post('quote_id'))
			throw new Cms_Exception('Missing details');

		try
		{
			$redirect = post('redirect');
			$_POST['redirect'] = null;

			// Locate the quote
			$quote = Service_Quote::create();
			$quote->apply_security($this->user);
			$quote = $quote->find(post('quote_id'));

			if (!$quote)
				throw new Cms_Exception(__('Sorry that quote could not be found'));

			if ($quote->request->user_id != $this->user->id)
				throw new Cms_Exception('This service request does not belong to you.');

			// Update the users account with latest details supplied by them
			Cms_Action_Manager::exec_ajax_handler('user:on_update_account', $this);

			// Update provider stats
			if ($quote->provider_id)
				Service_Provider::update_stat_fields($quote->provider_id);

			// Accept the quote
			Service_Quote_Manager::accept_quote($quote);

			if ($redirect)
				Phpr::$response->redirect($redirect);
		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}
	}

	public function on_update_request_booking_time()
	{
		if (!post('quote_id')||!$this->user)
			throw new Cms_Exception(__('Sorry that request could not be found'));

		$quote = Service_Quote::create()->apply_security($this->user)->find(post('quote_id'));
		if (!$quote)
			throw new Cms_Exception(__('Sorry that request could not be found'));

		if (!post('start_date') || !post('start_time'))
			throw new Cms_Exception(__('Missing start time'));

		$start_date = Phpr_DateTime::parse(post('start_date') . ' ' .post('start_time'), '%x %H:%M:%S');
		$quote->start_at = $start_date;
		$quote->save();

		// Send notification
		Notify::trigger('bluebell:suggest_time', array('from_user'=>$this->user, 'quote'=>$quote));

		$this->data['quote'] = $quote;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	// Inherits user:on_send_message
	public function on_send_quote_message()
	{
		if (!post_array('Message','object_id'))
			throw new Cms_Exception('Quote not found');

		$quote = Service_Quote::create()->find(post_array('Message','object_id'));

		if (!$quote)
			throw new Cms_Exception('Quote not found');

		$this->data['quote'] = $quote;
		$this->data['request'] = $request = $quote->request;
		$this->data['provider'] = $provider = $quote->provider;
		$this->data['is_provider'] = $is_provider = $request->user_id != $this->user->id;
		$this->data['to_user_id'] = $is_provider ? $request->user_id : $provider->user_id;

		Cms_Action_Manager::exec_ajax_handler('user:on_send_message', $this);
	}

	// Inherits user:on_send_message
	public function on_send_message()
	{
		Cms_Action_Manager::exec_ajax_handler('user:on_send_message', $this);
	
		// This is a reply
		if ($thread_id = post_array('Message', 'thread_id'))
		{
			$_POST['message_id'] = $thread_id;
			Cms_Action_Manager::exec_action('user:message', $this);
		}
	}

	//
	// Services
	// 
	
	public function on_nearby_areas()
	{
		$country = post('country');
		$postcode = post('postcode');
		$radius = post('radius');
		if (!$radius||!$postcode||!$country)
			return;

		$result = Bluebell_Geocode::get_nearby_areas($postcode, $country, $radius, true);
		echo $result;
	}

	// Internals
	//

	private function find_categories_from_role_name($role_name=null)
	{
		if (!$role_name)
			throw new Exception(__('Missing role name'));

		$category = Service_Category::create()->find_by_name($role_name);

		if (!$category)
			throw new Exception(__('Please select a valid service form the list',true));

		$category_list = array();

		$category_list[] = $category->id;

		foreach ($category->related_categories as $cat)
		{
			if (in_array($cat->id, $category_list))
				continue;
			
			$category_list[] = $cat->id;
		}

		return $category_list;
	}

}