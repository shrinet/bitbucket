<?php
	class Service_Provider_Membership{
		
		public static function apply_membership($provider, $plan)
		{
			
		}
		
		public static function renew_membership($provider)
        {
            $plan_id = $provider->plan_id;
            $provider_id = $provider->id;
    		#$provider = $this->get_provider($provider_id);
            $new_plan = Service_Plan::find_by_id($plan_id);
            $type = Payment_Invoice::type_membership;
    		
        		try 
        		{
        		$invoice = Payment_Invoice::raise_invoice($provider->user);
        		$fee_description = 'Renewal of Membership '.$new_plan->name;#($host->description) ? $host->description : $host->name;
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
