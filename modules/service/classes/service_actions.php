<?php

class Service_Actions extends Cms_Action_Base
{
	//
	// Questions
	//

	public function on_create_question()
	{
		$request_id = post('request_id');
		if (!$request_id)
		   throw new Cms_Exception('Missing request ID');

		$request = Service_Request::create()->find($request_id);
		if (!$request)
		   throw new Cms_Exception('Request not found');

		if (!$this->user)
			throw new Cms_Exception(__('You must be logged in to perform this action',true));

		$provider = Service_Provider::create_from_request_and_user($request, $this->user);
		if (!$provider)
			throw new Cms_Exception('Please create a provider profile');

		// Create question
		$question = Service_Question::create();
		$question->request = $request;
		$question->provider = $provider;

		// Fee check
		Phpr_Module_Manager::module_exists('payment') && Payment_Fee::trigger_event('Service_Question_Event', array('handler'=>'user:on_create_question'));

		$question->save(post('Question'));

		// Send notification
		Notify::trigger('service:new_question', array('question'=>$question));

		// Questions
		$questions = Service_Question::create()->where('request_id=?',$request->id)->find_all();

		$this->data['request'] = $request;
		$this->data['question'] = $question;
		$this->data['questions'] = $questions;
	}

	public function on_flag_question()
	{
		$request_id = post('request_id');
		$question_id = post('question_id');

		if (!$request_id||!$question_id)
		   throw new Cms_Exception('Missing request ID');

		if (!$this->user)
			throw new Cms_Exception(__('You must be logged in to perform this action',true));

		$request = Service_Request::create()->where('user_id=?', $this->user->id)->find($request_id);
		if (!$request)
		   throw new Cms_Exception('Request not found');

		$question = Service_Question::create()->find($question_id);
		if (!$question)
		   throw new Cms_Exception('Question not found');

		$remove_flag = post('remove_flag', false);

		if ($remove_flag)
			$question->is_public = true;
		else
			$question->is_public = false;

		$question->save();

		$this->data['request'] = $request;
		$this->data['question'] = $question;
	}

	public function on_create_answer()
	{
		$request_id = post('request_id');
		$question_id = post('question_id');
		if (!$request_id||!$question_id)
		   throw new Cms_Exception('Missing request ID');

		if (!$this->user)
			throw new Cms_Exception(__('You must be logged in to perform this action',true));

		$request = Service_Request::create()->where('user_id=?', $this->user->id)->find($request_id);
		if (!$request)
		   throw new Cms_Exception('Request not found');

		$question = Service_Question::create()->find($question_id);
		if (!$question)
		   throw new Cms_Exception('Question not found');

		// Create answer
		$answer = Service_Answer::create();
		$answer->request_id = $request->id;
		$answer->user_id = $this->user->id;

		// Fee check
		Phpr_Module_Manager::module_exists('payment') && Payment_Fee::trigger_event('Service_Answer_Event', array('handler'=>'user:on_create_answer'));

		$answer->save(post('Answer'));

		$question->answer = $answer;
		$question->save();

		// Send notification
		Notify::trigger('service:new_answer', array('question'=>$question));

		// Find other providers and notify them as well
		$other_providers = Service_Provider::create()->apply_request_touch($request);
		$other_providers->where('service_providers.user_id!=?', $question->provider->user_id);
		$other_providers = $other_providers->find_all();

		foreach ($other_providers as $other_provider)
		{
			Notify::trigger('service:new_answer_other', array('question'=>$question, 'provider'=>$other_provider));
		}

		$this->data['request'] = $request;
		$this->data['question'] = $question;
	}

	public function on_update_answer()
	{
		$request_id = post('request_id');
		$question_id = post('question_id');
		$answer_id = post('answer_id');

		if (!$request_id||!$question_id||!$answer_id)
		   throw new Cms_Exception('Missing request ID');

		if (!$this->user)
			throw new Cms_Exception(__('You must be logged in to perform this action',true));

		$request = Service_Request::create()->where('user_id=?', $this->user->id)->find($request_id);
		if (!$request)
		   throw new Cms_Exception('Request not found');

		$answer = Service_Answer::create()->where('user_id=?', $this->user->id)->find($answer_id);
		if (!$answer)
		   throw new Cms_Exception('Answer not found');

		$answer->save(post('Answer'));

		$question = Service_Question::create()->find($question_id);
		if (!$question)
		   throw new Cms_Exception('Question not found');

		$this->data['request'] = $request;
		$this->data['question'] = $question;
	}

	//
	// Quotes
	//

	public function on_create_quote()
	{
		$_POST = array_merge($_POST, post('Quote', array()));
		
		if (post('delete'))
			return $this->on_delete_quote();

		$request = $this->get_request(post('request_id'), false, true);

		$provider = post('get_provider_from_user') ? Service_Provider::create_from_user($this->user) : Service_Provider::create_from_request_and_user($request, $this->user);
		if (!$provider)
			throw new Cms_Exception('Provider profile not found');

		if($provider->plan_id == NULL)
			throw new Cms_Exception('Your account Not yet active');
		
		if(!Service_Plan::check_plan($provider)){
			throw new Cms_Exception('Your account needs Renewal');
		}

		$request_max_bids = c('request_max_bids', 'service');
		if ($request_max_bids && $request->total_quotes >= $request_max_bids)
			throw new Cms_Exception('Sorry, this request has reached the maximum bid amount');

		$quote = Service_Request_Manager::create_quote($this->user, $provider, $request, $_POST);

		$this->data['request'] = $request;
		$this->data['quote'] = $quote;
		$this->data['provider'] = $provider;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	public function on_delete_quote()
	{
		$_POST = array_merge($_POST, post('Quote', array()));

		$request = $this->get_request(post('request_id'), false, true);

		$provider = Service_Provider::create_from_request_and_user($request, $this->user);
		if (!$provider)
			throw new Cms_Exception('Provider profile not found');

		Service_Request_Manager::delete_quotes_for_user($this->user);

		$this->data['request'] = $request;
		$this->data['quote'] = null;
		$this->data['provider'] = $provider;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	public function on_update_quote_status()
	{
		$quote = $this->get_quote(post('quote_id'));
		$quote_status = post('quote_status', Service_Quote_Status::status_accepted);

		Service_Quote_Manager::set_quote_status($quote, $quote_status);

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);

		return $quote;
	}

	// This will trigger extra functionality, like notifying other providers
	public function on_accept_quote()
	{
		$quote = $this->get_quote(post('quote_id'));

		if ($quote->request->user_id != $this->user->id)
			throw new Cms_Exception('This service request does not belong to you.');

		// Accept the quote
		Service_Quote_Manager::accept_quote($quote);

		// Close the request
		Service_Request_Manager::close_request($quote->request);

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);

		return $quote;
	}

	//
	// Ratings
	// 

	public function on_create_rating()
	{
		try 
		{
			$quote_id = post('quote_id');
			if (!$quote_id)
			   throw new Cms_Exception('Missing quote ID');

			if (!$this->user)
				throw new Cms_Exception(__('You must be logged in to perform this action',true));

			$quote = Service_Quote::create()->apply_security($this->user)->find($quote_id);
			if (!$quote)
				throw new Cms_Exception(__('Sorry that request could not be found'));

			$request = $quote->request;
			$provider = $quote->provider;
			$is_provider = $request->user_id != $this->user->id;

			// Create rating
			$rating = Service_Rating::create();
			$rating->request = $request;
			$rating->provider = $is_provider ? null : $provider;
			$rating->user_from = $this->user;
			$rating->user_to = $is_provider ? $request->user : $provider->user;
			$rating->save(post('Rating'));

			// Automatically approve @todo should be optional
			$rating->approve();

			// Send notification
			Notify::trigger('service:new_rating', array('rating'=>$rating));

			$this->data['request'] = $request;
			$this->data['provider'] = $provider;
			$this->data['rating'] = $rating;
			$this->data['is_provider'] = $is_provider;
			$this->data['opp_user_name'] = $is_provider ? $request->user->name : $provider->business_name;
		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}
	}

	// Misc

	public function on_suggest_category()
	{
		try
		{
			$validation = new Phpr_Validation();
			
			if (!$this->user)
			{
				$validation->add('email', __('Email',true))->fn('trim')->required()->fn('mb_strtolower');
				$validation->add('name', __('Name',true))->fn('trim')->required();
			}
			
			$validation->add('category_name', __('Category Name'))->fn('trim')->required();

			if (!$validation->validate($_POST))
				$validation->throw_exception();

			$user = ($this->user)
				? $this->user
				: (object)array('name' => $validation->field_values['name'], 'email' => $validation->field_values['email']);

			// Send notification
			Notify::trigger('service:suggest_category', array('user'=>$user, 'category_name'=>$validation->field_values['category_name']));            

			if (post('flash'))
				Phpr::$session->flash['success'] = post('flash');

			$redirect = post('redirect');
			if ($redirect)
				Phpr::$response->redirect($redirect);
		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}
	}

	//
	// Service functions
	//

	// Returns a JSON collection of categorys
	public function on_search_categories()
	{
		$data = Service_Category::search_categories(post('search'), array('target_name'=>post('name')));
		echo json_encode($data);
	}

	//
	// View Request
	//

	public function request()
	{
		$request_url_name = $this->request_param(0);
		$request = $this->get_request($request_url_name, false, false);

		if (!$request)
			return $this->data['request'] = null;

		// Match logged in user's provider profile to this request
		$provider = Service_Provider::create_from_request_and_user($request, $this->user);

		$quote = null;
		if ($provider)
		{
			// Find exisiting quote
			$quote = Service_Quote::create()
				->apply_visibility()
				->where('provider_id=?', $provider->id)
				->where('request_id=?', $request->id)
				->find();
		}

		// Questions
		$questions = Service_Question::create()->where('request_id=?',$request->id)->find_all();

		// Override meta
		$this->page->title_name = $request->title;
		//$this->page->description = "";
		//$this->page->keywords = "";

		// Private request, has provider been invited?
		if ($request->type == Service_Request::type_private)
		{
			if (!$this->user)
				$request = null;
			else if ($request->user_id != $this->user->id && !$request->provider_has_link($provider, Service_Request::link_type_invited))
				$request = null;
		} 

		$this->data['questions'] = $questions;
		$this->data['provider'] = $provider;
		$this->data['request'] = $request;
		$this->data['quote'] = $quote;
	}

	//
	// Create Request
	//

	public function create_request()
	{
		$_POST = array_merge($_POST, post('Request', array()));

		$category_id = $this->request_param(0);
		$category = null;
		if ($category_id)
			$category = Service_Category::create()->find($category_id);

		$this->data['category'] = $category;

		// Process file uploads
		// 
		if (array_key_exists('request_images', $_FILES))
		{
			Service_Request_Manager::set_request_files(Service_Request::create(), $_POST, $_FILES, post('session_key'));
			die();
		}
	}

	// Preview
	public function on_review_request()
	{
		$_POST = array_merge($_POST, post('Request', array()));

		$request = Service_Request_Manager::validate_request($this->user, $_POST, $_FILES, post('session_key'), 'preview');
		$request->eval_custom_columns();
		
		$this->data['request'] = $request;
		$this->data['categories'] = $request->categories;
		$this->data['files'] = $request->files;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);

		return $request;
	}
	
	// Save
	public function on_create_request()
	{
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

		$request = Service_Request_Manager::create_request($this->user, $_POST, $_FILES, post('session_key'));

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
	// Manage Request
	//

	public function update_request()
	{
		$_POST = array_merge($_POST, post('Request', array()));

		$request_url_name = $this->request_param(0);
		$request = $this->get_request($request_url_name, true, false);

		if (!$request)
			return $this->data['request'] = null;

		// Process file uploads
		if (array_key_exists('request_images', $_FILES))
		{
			Service_Request_Manager::set_request_files($request, $_POST, $_FILES);
			$request->save(null, post('session_key'));
			die();
		}

		// Override meta
		$this->page->title_name = $request->title;

		$this->data['request'] = $request;
	}

	public function on_update_request()
	{
		$request = $this->get_request(post('request_id'), true, false);
		$request->save(post('Request'));
		$this->data['request'] = $request;
	}

public function request_provider()
	{
		$_POST = array_merge($_POST, post('Request', array()));
		#print_r($_POST);
		$request_url_name = $this->request_param(0);
		$request = $this->get_request($request_url_name, true, false);
		$providers = Bluebell_Provider::match_to_request2($request);
#		
		$this->page->title_name = $request->title;
		$this->data['providers'] = $providers;
		$this->data['request'] = $request;
	}
	public function on_describe_request()
	{
		$request = $this->get_request(post('request_id'), true, true);

		if (!$request)
			return;

		$request->add_extra_description(post('description'));
		$request->save();

		$this->data['request'] = $request;
	}

	public function on_delete_request_attachment()
	{
		$file_id = post('file_id');

		if (!$file_id)
			return;

		$request = $this->get_request(post('request_id'), true, true);
		$file = Db_File::create()->find($file_id);

		if (!$file || !$request)
			return;

		$request->files->delete($file);
		$request->save();
	}

	public function on_cancel_request()
	{
		if (!post('request_id'))
			throw new Cms_Exception('Missing ID');

		$request = $this->get_request(post('request_id'), true, true);

		$request->set_status(Service_Status::status_cancelled);
		$request->save();

		$this->data['request'] = $request;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	public function on_extend_request()
	{
		if (!post('request_id'))
			throw new Cms_Exception('Missing Request ID');

		$request = $this->get_request(post('request_id'), true, true);

		$request->expired_at = Phpr_DateTime::now()->add_days(post_array('Request', 'duration', c('request_default_length', 'Service')));
		$request->set_status(Service_Status::status_active);
		$request->save();

		$this->data['request'] = $request;

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	//
	// Ignore request
	// 

	public function on_ignore_request()
	{
		$request_id = post('request_id');
		if (!$request_id)
		   throw new Cms_Exception('Missing request ID');

		$request = Service_Request::create()->find($request_id);
		if (!$request)
		   throw new Cms_Exception('Request not found');

		if (!$this->user)
			throw new Cms_Exception(__('You must be logged in to perform this action',true));

		$provider = Service_Provider::create_from_request_and_user($request, $this->user);
		if (!$provider)
			throw new Cms_Exception('Please create a provider profile');

		$request->link_provider($provider, Service_Request::link_type_banned);

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	//
	// Provider
	//
	
	public function provider()
	{
		$provider = $this->get_provider($this->request_param(0), false, false);
		
		if (!$provider)
			return $this->data['provider'] = null;

		$this->page->title_name = $provider->business_name;

		//@todo Eek! BB specific...
		$this->page->description = __('%s in %s', array($provider->role_name, $provider->location_string));

		$this->data['portfolio'] = $provider->get_portfolio();
		$this->data['user'] = $this->user;
		$this->data['provider'] = $provider;
	}

	//
	// Membership
	//

		public function on_create_membership()
	{
		$_POST = array_merge($_POST, post('Membership', array()));
		$provider_id = $_POST['provider_id'];
		$plan_id = $_POST['Provider']['plan_id'];
		$provider = $this->get_provider($provider_id);
    $new_plan = Service_Plan::find_by_id($plan_id);
    $type = Payment_Invoice::type_membership;
		if(Service_Plan::change_plan($plan_id, $provider_id)){

    		try 
    		{
    		$invoice = Payment_Invoice::raise_invoice($provider->user);
    		$fee_description = 'Activation of Membership '.$new_plan->name;#($host->description) ? $host->description : $host->name;
    		$invoice_item = $invoice->add_line_item(1, $fee_description, $new_plan->price);#$this->calculate_fee($host, $quote));
            $invoice->set_invoice_type($type);
    		$invoice->save();
            
            // Create promise to add credits
    			$params = array($provider->id, $plan_id);
    			$promise = Payment_Fee_Promise::create_promise(Payment_Fee_Promise::mode_class_method, 'Service_Plan::apply_new_plan', $params, $provider->user);
    			$promise->invoice_id = $invoice->id;
    			$promise->invoice_item_id = $invoice_item->id;
    			$promise->save();
                
    			if ($redirect = post('redirect'))
    			     Phpr::$response->redirect(str_replace('%s', $invoice->hash, $redirect));
    		}
    		catch (Exception $ex)
    		{
    			throw new Cms_Exception($ex->getMessage());
    		}   
		
	   }
    }
	
	//
	// Provider Submit
	//
	
	public function create_provider()
	{
		if (!$this->user)
			throw new Exception(__('You must be logged in to create a service profile'));

		// Process file uploads
		Service_Provider_Manager::set_provider_images(Service_Provider::create(), $_POST, $_FILES);

		$this->data['provider'] = Service_Provider::create()->load_from_user($this->user);
	}

	public function on_create_provider()
	{
		try 
		{
			$_POST = array_merge($_POST, post('Provider', array()));

			if (!$this->user)
				throw new Exception(__('You must be logged in to create a service profile'));

			$provider = Service_Provider_Manager::create_provider($this->user, $_POST, $_FILES);

			if ($redirect = post('redirect'))
				Phpr::$response->redirect(sprintf($redirect, $provider->id));
		}
		catch (Exception $ex)
		{
			if (isset($provider))
				$provider->delete();
			
			throw new Cms_Exception($ex->getMessage());
		}
	}

	//
	// Provider Update
	//

	public function update_provider()
	{
		if (!$this->user->id)
			return $this->data['provider'] = $this->data['profiles'] = null;

		$provider = $this->get_provider($this->request_param(0), true, false);

		// Process file uploads
		Service_Provider_Manager::set_provider_images($provider, $_POST, $_FILES);

		$profiles = Service_Provider::create()->where('user_id=?', $this->user->id)->find_all();

		$this->data['provider'] = $provider;
		$this->data['profiles'] = $profiles;
	}

	public function on_update_provider()
	{
		$provider = $this->get_provider(post('provider_id'));
		$provider->save(post('Provider'));
		$this->data['provider'] = $provider;
	}

	public function on_update_provider_logo()
	{
		$provider = $this->get_provider(post('provider_id'));
		$logo_id = post('provider_logo');

		// Delete logo
		if (post('delete')||$logo_id)
		{
			foreach ($provider->logo as $file)
				$provider->logo->delete($file);
		}

		// Find orphaned logo
		if ($logo_id)
		{
			$logo = Db_File::create()->where('master_object_id is null')->find($logo_id);
			if ($logo)
				$provider->logo->add($logo);
		}

		$provider->save();
	}

	public function on_update_provider_photo()
	{
		$provider = $this->get_provider(post('provider_id'));
		$photo_id = post('provider_photo');

		// Delete logo
		if (post('delete')||$photo_id)
		{
			foreach ($provider->photo as $file)
				$provider->photo->delete($file);
		}

		// Find orphaned photo
		if ($photo_id)
		{
			$photo = Db_File::create()->where('master_object_id is null')->find($photo_id);
			if ($photo)
				$provider->photo->add($photo);
		}

		$provider->save();
	}

	public function on_update_provider_portfolio()
	{
		$provider = $this->get_provider(post('provider_id'));

		// Delete item
		$file_id = post('file_id');

		if (!$file_id)
			return;

		$file = $provider->portfolio->find($file_id);
		//$file = Db_File::create()->find($file_id);

		if (!$file)
			return;

		$provider->portfolio->delete($file);
		$provider->save();
	}

	public function on_refresh_provider_portfolio()
	{
		$provider = $this->get_provider(post('provider_id'));

		if (post('is_manage'))
			$this->data['is_manage'] = true;

		$this->data['provider'] = $provider;
		$this->data['images'] = $provider->get_portfolio();
	}

	public function on_delete_provider()
	{
		$provider = $this->get_provider(post('provider_id'));

		if (!$provider)
			throw new Exception(__('Something went wrong, please try the link again'));
				
		$provider->delete();

		if ($redirect = post('redirect'))
			Phpr::$response->redirect($redirect);
	}

	//
	// Provider Testimonials
	//
	
	public function testimonial()
	{
		$provider = $this->get_provider($this->request_param(1), false, false);
		$hash = $this->request_param(0);
		$testimonial = Service_Testimonial::create()->where('is_published is null AND hash=:hash AND provider_id=:id', array('hash'=>$hash, 'id'=>$provider->id))->find();

		if (!$testimonial)
			return $this->data['provider'] = null;

		$this->data['provider'] = $provider;
		$this->data['testimonial'] = $testimonial;
	}

	public function on_ask_testimonial()
	{
		try
		{
			$provider = $this->get_provider(post('provider_id'));

			$validation = new Phpr_Validation();
			$validation->add('email', __('Email',true))->fn('trim')->required(__('Please specify an email address'))->fn('mb_strtolower')->focus_id('testimonial_email');
			$validation->add('subject', __('Subject',true))->fn('trim')->required(__('Please specify a subject'))->focus_id('testimonial_subject');
			$validation->add('message', __('Message',true))->fn('trim')->required(__('Please specify message'))->focus_id('testimonial_message');
			if (!$validation->validate(post('Testimonial')))
				$validation->throw_exception();

			$testimonial = Service_Testimonial::create_testimonial($provider, $validation->field_values['email'], $validation->field_values['subject'], $validation->field_values['message']);
			
			Notify::trigger('service:testimonial_ask', array('testimonial'=>$testimonial));
		}
		catch (Exception $ex)
		{
			if (isset($testimonial))
				$testimonial->delete();
			
			throw new Cms_Exception($ex->getMessage());
		}
	}

	public function on_create_testimonial()
	{
		try
		{
			$provider = $this->get_provider(post('provider_id'), false);
			$hash = post('hash');

			if (!$provider||!$hash)
				throw new Exception(__('Something went wrong, please try the link again'));

			$testimonial = Service_Testimonial::create()->where('hash=:hash AND provider_id=:id', array('hash'=>$hash, 'id'=>$provider->id))->find();

			$validation = new Phpr_Validation();
			$validation->add('name', __('Name',true))->fn('trim')->required(__('Please specify your name'))->focus_id('testimonial_name');
			$validation->add('location', __('Location',true))->fn('trim')->required(__('Please specify your location'))->focus_id('testimonial_location');
			$validation->add('comment', __('Testimonial',true))->fn('trim')->required(__('Please specify a testimonial'))->focus_id('testimonial_comment');
			if (!$validation->validate(post('Testimonial')))
				$validation->throw_exception();

			$testimonial->is_published = true;
			$testimonial->save($validation->field_values);

			Notify::trigger('service:testimonial_complete', array('testimonial'=>$testimonial, 'provider'=>$provider));
		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}
	}

	public function on_delete_testimonial()
	{
		$provider = $this->get_provider(post('provider_id'));
		$testimonial_id = post('testimonial_id');

		if (!$provider||!$testimonial_id)
			throw new Exception(__('Something went wrong, please try the link again'));
		
		$testimonial = Service_Testimonial::create()->where('provider_id=?', $provider->id)->find($testimonial_id);

		if ($testimonial)
			$testimonial->delete();

		$this->data['provider'] = $provider;
	}

	//
	// Validation
	// 

	public function on_validate_category_name()
	{
		$valid = false;

		// Look for role_name value
		$category_name = post('role_name');
		if (!$category_name)
			$category_name = post_array('Provider', 'role_name');
		
		if (!$category_name)
			$category_name = post_array('Request', 'role_name');

		if (!$category_name)
			die("Could not find a category with that name");

		try
		{
			$valid = Service_Category::create()->where('name=?', $category_name)->find();
		}
		catch (Exception $ex)
		{
			$valid = false;
			echo $ex->getMessage();
		}

		if ($valid)
			echo "true";   
	}

	//
	// Internals
	//
	
	private function get_provider($provider_id = null, $owner_check = true, $throw_exception = true)
	{
		if (!strlen($provider_id) && $throw_exception)
			throw new Cms_Exception(__('Sorry that provider could not be found'));
		else if (!strlen($provider_id))
			return null;

		if ($owner_check && !$this->user && $throw_exception)
			throw new Cms_Exception(__('Sorry that provider could not be found'));
		else if ($owner_check && !$this->user)
			return null;

		$provider = Service_Provider::create();

		if ($owner_check)
			$provider->apply_owner($this->user);

		$provider->where('id=:name OR url_name=:name', array('name'=>$provider_id));
		$provider = $provider->find();

		if (!$provider && $throw_exception)
			throw new Phpr_ApplicationException(__('Sorry that provider could not be found'));

		return $provider;
	}

	private function get_request($request_id = null, $owner_check = true, $throw_exception = true)
	{
		if (!strlen($request_id) && $throw_exception)
			throw new Cms_Exception(__('Sorry that request could not be found'));
		else if (!strlen($request_id))
			return null;

		if ($owner_check && !$this->user && $throw_exception)
			throw new Cms_Exception(__('Sorry that request could not be found'));
		else if ($owner_check && !$this->user)
			return null;

		$request = Service_Request::create();

		if ($owner_check)
			$request->apply_owner($this->user);

		$request->where('id=:name OR url_name=:name', array('name'=>$request_id));
		$request = $request->find();

		if (!$request && $throw_exception)
			throw new Cms_Exception(__('Sorry that request could not be found'));

		return $request;
	}

	private function get_quote($quote_id = null, $security_check = true, $throw_exception = true)
	{
		if ((!$quote_id || !$this->user) && $throw_exception)
			throw new Cms_Exception(__('Sorry that quote could not be found'));
		else if (!$quote_id || !$this->user)
			return null;

		$quote = Service_Quote::create();

		if ($security_check)
			$quote = $quote->apply_security($this->user);

		$quote = $quote->find($quote_id);
		
		if (!$quote && $throw_exception)
			throw new Cms_Exception(__('Sorry that quote could not be found'));
		else if (!$quote)
			return null;

		return $quote;
	}

}