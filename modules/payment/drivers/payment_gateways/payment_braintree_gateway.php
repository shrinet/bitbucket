<?php

class Payment_Braintree_Gateway extends Payment_Type_Base
{
	protected static $sdk_initialized = false;
	
	public function get_info()
	{
		return array(
			'name'=>'BrainTree',
			'description'=>'Braintree with payment form hosted on your server'
		);
	}
	
	public function build_config_ui($host, $context = null)
	{
		#$host->add_field('test_mode', 'Create Test Transactions')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Mark all transactions as test transactions. You can create test transactions in the live environment.', 'above');
		
		$host->add_field('use_test_server', 'Use Test Server')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Connect to Braintree. Use this option of you have Braintree developer test account.', 'above');

		if ($context !== 'preview')
		{
			$host->add_field('merchant_id', 'Merchant ID', 'left')->tab('Configuration')->display_as(frm_text)->comment('The Merchant ID is provided in the Braintree Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide Merchant ID.');
			$host->add_field('public_key', 'Public Key', 'right')->tab('Configuration')->display_as(frm_text)->comment('The Public Key is provided in the Braintree Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide Public Key.');
			$host->add_field('private_key', 'Private Key', 'right')->tab('Configuration')->display_as(frm_text)->comment('The Private Key is provided in the Braintree Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide Private Key.');
		}

		$host->add_field('transaction_type', 'Transaction Type', 'left')->tab('Configuration')->display_as(frm_dropdown)->comment('The type of credit card transaction you want to perform.', 'above');
		$host->add_field('invoice_status', 'Invoice Status', 'right')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment.', 'above');

	}

	public function get_transaction_type_options($current_key_value = -1)
	{
		$options = array(
			'AUTH_CAPTURE'=>'Authorization and Capture',
			'AUTH_ONLY'=>'Authorization Only'
		);
		
		if ($current_key_value == -1)
			return $options;

		return isset($options[$current_key_value]) ? $options[$current_key_value] : null;
	}

	public function get_invoice_status_options($current_key_value = -1)
	{
		if ($current_key_value == -1)
			return Payment_Invoice_Status::create()->order('name')->find_all()->as_array('name', 'id');

		return Payment_Invoice_Status::create()->find($current_key_value)->name;
	}
	
	private function buildParams($data,$invoice){
		$settings = Payment_Config::create();
		$currency_converter = Payment_Currency::create();
		$params = array();
		$params = array(
            'orderId'   => 'Invoice #'.$invoice->id,
            'amount'    => $currency_converter->convert($invoice->total, $settings->currency_code, 'USD'),
            'customer'  => array(
                    'firstName' => $invoice->billing_first_name,
                    'lastName'  => $invoice->billing_last_name,
                    'company'   => $invoice->billing_company,
                    'phone'     => $invoice->billing_phone,
                    'email'     => $invoice->billing_email,
                )
		);
		
		return $params;
	}
	
	private function buildcreateParams($user)
    {
        $params = array();
        $state = Location_State::find_by_id($user->state_id);

        $params = array(
            'individual' => array(
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dateOfBirth' => $user->dob,
                'ssn' => $user->ssn,
                'address' => array(
                    'streetAddress' => $user->street_addr,
                    'locality' => $user->city,
                    'region' => $state->code,
                    'postalCode' => $user->zip)
            ),
            'funding' => array(
                'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_EMAIL,
                'email' => $user->funding_email,
                'mobilePhone' => $user->mobile,
                #'accountNumber' => '1123581321',
                #'routingNumber' => '071101307'
            ),
            'tosAccepted' => true,
            'masterMerchantAccountId' => 'WomBids_marketplace',
        );

        return $params;

    }

    public function add_customer($user, $host)
    {
        $path = dirname(__FILE__).'/'.strtolower(get_class($this));

        require_once $path.'/lib/Braintree.php';

        Braintree_Configuration::environment($host->use_test_server ? 'sandbox' : 'production');
        Braintree_Configuration::merchantId($host->merchant_id);
        Braintree_Configuration::publicKey($host->public_key);
        Braintree_Configuration::privateKey($host->private_key);
        $createParams = $this->buildcreateParams($user);
        try{
            $result = Braintree_MerchantAccount::create($createParams);
        }
        catch (Exception $ex){
            throw $ex;
        }

        print_r($result);
        if($result->success)
        {
            User::update_user_merchantDetails($user,$result->merchantAccount->id,$result->merchantAccount->status);

        } else{
            var_dump($result->message);
        }
    }
/**
	 * Processes payment using passed data
	 * @param array $data Posted payment form data
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 * @param $back_end Determines whether the function is called from the administration area
	 */	
	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		#var_dump($invoice);
		$validation = $this->init_validation_obj();

		try
		{
			if (!$validation->validate($data))
				$validation->throw_exception();
		} 
		catch (Exception $ex)
		{
			$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), array(), null);
			throw $ex;
		}		// We do not need any code here since payments are processed on Authorize.Net server.
/*		define('AUTHORIZENET_SANDBOX', $host->use_test_server ? true : false);
		define('AUTHORIZENET_API_LOGIN_ID', $host->api_login);
		define('AUTHORIZENET_TRANSACTION_KEY', $host->api_transaction_key);
	*/	
		$path = dirname(__FILE__).'/'.strtolower(get_class($this));

		require_once $path.'/lib/Braintree.php';
		
		Braintree_Configuration::environment($host->use_test_server ? 'sandbox' : 'production');
		Braintree_Configuration::merchantId($host->merchant_id);
		Braintree_Configuration::publicKey($host->public_key);
		Braintree_Configuration::privateKey($host->private_key);
		$transactionParams = $this->buildParams($validation,$invoice);
		$transactionParams['options']['storeInVaultOnSuccess'] = true;
		$transactionParams['options']['submitForSettlement'] = true;
		$transactionParams['creditCard'] = array(
                    'cardholderName'    => $validation->field_values['FIRSTNAME'] . ' ' . $validation->field_values['LASTNAME'],
                    'number'            => $validation->field_values['ACCT'],
                    'cvv'               => $validation->field_values['CVV2'],
                    'expirationDate'    => $validation->field_values['EXPDATE_MONTH'] . '/' . $validation->field_values['EXPDATE_YEAR']
                );
		try {
                $result = Braintree_Transaction::sale($transactionParams);
            } catch (Exception $e) {
                throw new Phpr_ApplicationException($e);
            }		
		
		if ($result->success) {
		    echo("Success! Transaction ID: " . $result->transaction->id);
			if (!$invoice->is_payment_processed(true))
			{
			if ($invoice->mark_as_payment_processed())
					{
						Payment_Invoice_Log::create_record($invoice->payment_type->invoice_status, $invoice);
						#$this->log_payment_attempt($invoice, 'Successful payment', 1, array(), $result, $result->transaction->id);
						$transaction_id = $result->transaction->id;
						#if(strlen($transaction_id))
							#$this->update_transaction_status($invoice->payment_method, $invoice, $transaction_id, 'Processed', 'processed');
					}
		} else if ($result->transaction) {
		    echo("Error: " . $result->message);
		    echo("<br/>");
		    echo("Code: " . $result->transaction->processorResponseCode);
		} else {
		    echo("Validation errors:<br/>");
		    foreach (($result->errors->deepAll()) as $error) {
		        echo("- " . $error->message . "<br/>");
		    }
		}
		}
		$google_tracking_code = 'utm_nooverride=1';
			$return_page = $invoice->get_receipt_url();
			if ($return_page)
				Phpr::$response->redirect($return_page.'?'.$google_tracking_code);
		$response_fields = $this->parse_response($result);
		#var_dump($response_fields);
		
	}
	
	public function request_release($escrow,$host)
    {
        $this->init_sdk($host);
        $result1 = Braintree_Transaction::holdInEscrow($escrow->transaction);
        $result = Braintree_Transaction::releaseFromEscrow($escrow->transaction);
        print_r($result1);
        print_r($result);
        Phpr::$trace_log->write($result);

    }
	
	public function braintree_response($host, $response)
    {
				$this->init_sdk($host);
				$this->log_braintree_action($response);
				
				Phpr::$trace_log->write($response);
				if(isset($response["bt_signature"]) && isset($response["bt_payload"]))
				{
						$webhookNotification = Braintree_WebhookNotification::parse(
								$response["bt_signature"], $response["bt_payload"]
						);

						$vars = array('merchandId' => $webhookNotification->merchantAccount->id,
													'status' => $webhookNotification->merchantAccount->status);
						User::update_merchantAccount($vars);
						
						$this->log_braintree_action($message);
				}
        
    }
	
	private function parse_response($response)
	{
	return explode("|", $response);
	}
	
	protected function init_sdk($host)
	{
		if (self::$sdk_initialized)
			return;
			
		self::$sdk_initialized = true;
		
		$path = dirname(__FILE__).'/'.strtolower(get_class($this));

		require_once $path.'/lib/Braintree.php';
        Braintree_Configuration::environment($host->use_test_server ? 'sandbox' : 'production');
        Braintree_Configuration::merchantId($host->merchant_id);
        Braintree_Configuration::publicKey($host->public_key);
        Braintree_Configuration::privateKey($host->private_key);
		
	}
	
	protected function init_validation_obj()
	{
		$validation = new Phpr_Validation();
		$validation->add('FIRSTNAME', 'Cardholder first name')->fn('trim')->required('Please specify a cardholder first name.');
		$validation->add('LASTNAME', 'Cardholder last name')->fn('trim')->required('Please specify a cardholder last name.');
		$validation->add('EXPDATE_MONTH', 'Expiration month')->fn('trim')->required('Please specify a card expiration month.')->regexp('/^[0-9]*$/', 'Credit card expiration month can contain only digits.');
		$validation->add('EXPDATE_YEAR', 'Expiration year')->fn('trim')->required('Please specify a card expiration year.')->regexp('/^[0-9]*$/', 'Credit card expiration year can contain only digits.');

		$validation->add('ACCT', 'Credit card number')->fn('trim')->required('Please specify a credit card number.')->regexp('/^[0-9]*$/', 'Please specify a valid credit card number. Credit card number can contain only digits.')->min_length(13, "Invalid credit card number")->max_length(16, "Invalid credit card number");
		$validation->add('CVV2', 'CVV2')->fn('trim')->required('Please specify CVV2 value.')->regexp('/^[0-9]*$/', 'Please specify a CVV2 number. CVV2 can contain only digits.')->min_length(3, "Invalid credit card code (CVV2)")->max_length(4, "Invalid credit card code (CVV2)");

		return $validation;
	}
}