<?php

class Service_Quote_Manager 
{

	public static function set_quote_status($quote, $new_status)
	{
		$previous_status = $quote->status;
		$quote->set_status($new_status);

		// Fee check
		module_exists('payment') && Payment_Fee::trigger_event('Service_Quote_Status_Event', array(
			'handler'=>'service:on_update_quote_status', 
			'previous_status'=>$previous_status,
			'quote'=>$quote
		));

		$quote->save();
	}

	public static function accept_quote($quote)
	{
		$previous_status = $quote->status;

		if (!$quote->set_status(Service_Quote_Status::status_accepted))
			throw new Cms_Exception('Unable to change quote status to accepted, aborting...');
		
		// Minimum requirements
		if (!$quote->request || !$quote->provider)
			throw new Cms_Exception('This quote is missing a provider and a request, aborting...');

		$request = $quote->request;
		$provider = $quote->provider;

		// Fee check
		module_exists('payment') && Payment_Fee::trigger_event('Service_Quote_Status_Event', array(
			'handler' => 'service:on_update_quote_status', 
			'previous_status' => $previous_status,
			'quote' => $quote
		));

		$quote->save();

		// Send notification to winner
		Notify::trigger('service:job_won', array('request'=>$request, 'provider'=>$provider));

		// Find other providers and notify them about losing
		$other_providers = Service_Provider::create()->apply_request_touch($request);
		$other_providers->where('service_providers.user_id!=?', $provider->user_id);
		$other_providers = $other_providers->find_all();

		foreach ($other_providers as $other_provider)
		{
			Notify::trigger('service:job_lost', array('request'=>$request, 'provider'=>$other_provider));
		}
	}

}