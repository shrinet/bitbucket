<?php

class Payment_Authorize_Dpm_Gateway extends Payment_Type_Base 

{
	protected static $sdk_initialized = false;

	const TEST_URL = "https://test.authorize.net/gateway/transact.dll";
	const LIVE_URL = "https://secure.authorize.net/gateway/transact.dll";

	public function get_info() 
	{
		return array(); // Disabled
		return array(
			'name' => 'Authorize.Net (DPM)',
			'description' => 'Authorize.net Direct Post Method (DPM) method with payment form hosted on your server'
		);
	}
 
	public function build_config_ui($host, $context = null) 
	{
		$host->add_field('test_mode', 'Create Test Transactions')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Mark all transactions as test transactions. You can create test transactions in the live environment.', 'above');
		
		$host->add_field('use_test_server', 'Use Test Server')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Connect to Authorize.Net test server (test.authorize.net). Use this option of you have Authorize.Net developer test account.', 'above');
		
		if($context !== 'preview') 
		{
			$host->add_form_partial($host->get_partial_path('relay_response_hint.htm'))->tab('Configuration');

			$host->add_field('api_login', 'API Login ID', 'left')->tab('Configuration')->display_as(frm_text)->comment('The merchant API Login ID is provided in the Authorize.Net Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide API Login ID.');
			$host->add_field('api_transaction_key', 'Transaction Key', 'right')->tab('Configuration')->display_as(frm_text)->comment('The merchant Transaction Key is provided in the Authorize.Net Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide Transaction Key.');
		}

		$host->add_field('transaction_type', 'Transaction Type', 'left')->tab('Configuration')->display_as(frm_dropdown)->comment('The type of credit card transaction you want to perform.', 'above');
		
		$host->add_field('invoice_status', 'Invoice Status', 'right')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment.', 'above');

		if($context !== 'preview')
		
		{
			$host->add_field('md5_hash_value', 'MD5 Hash Value')->tab('Configuration')->display_as(frm_password)->comment('The MD5 Hash value is a random value that you configure in the Merchant Interface.', 'above', true)->validation()->fn('trim');

			$host->add_form_partial($host->get_partial_path('md5_hint.htm'))->tab('Configuration');
		}
	}

	public function get_invoice_status_options($current_key_value = -1)
	{
		if ($current_key_value == -1)
			return Payment_Invoice_Status::create()->order('name')->find_all()->as_array('name', 'id');

		return Payment_Invoice_Status::create()->find($current_key_value)->name;
	}

	public function get_transaction_type_options($current_key_value = -1) 
	{
		$options = array(
			'AUTH_CAPTURE' => 'Authorization and Capture',
			'AUTH_ONLY' => 'Authorization Only'
		);
		
		if($current_key_value == -1)
			return $options;

		return isset($options[$current_key_value]) ? $options[$current_key_value] : null;
	}
	
	public function init_config_data($host) 
	{
		$host->test_mode = true;
		$host->use_test_server = true;
		$host->invoice_status = Payment_Invoice_Status::get_status_paid()->id;
	}

	public function validate_config_on_save($host) 
	{
		$hash_value = trim($host->md5_hash_value);
		
		if(!strlen($hash_value)) 
		{
			if(!isset($host->fetched_data['md5_hash_value']) || !strlen($host->fetched_data['md5_hash_value']))
				$host->validation->set_error('Please enter MD5 Hash value', 'md5_hash_value', true);

			$host->md5_hash_value = $host->fetched_data['md5_hash_value'];
		}
	}

	public function get_form_action($host) 
	{
		return $host->use_test_server ? self::TEST_URL : self::LIVE_URL;
	}

	public function process_payment_form($data, $host, $invoice, $back_end = false) 
	{
		/*
		 * We do not need any code here since payments are processed on Authorize.Net server.
		 */
	}
	
	public function generate_fingerprint($api_login_id, $sequence, $timestamp, $amount, $transaction_key) 
	{
		if(function_exists('hash_hmac'))
			return hash_hmac('md5', $api_login_id . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $transaction_key); 

		return bin2hex(mhash(MHASH_MD5, $api_login_id . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $transaction_key));
	}

	public function get_hidden_fields($host, $invoice, $admin = false) 
	{
		$result = array();
		$settings = Payment_Config::create();
		$currency_converter = Payment_Currency::create();
		$amount = number_format($currency_converter->convert($invoice->total, $settings->currency_code, 'USD'), 2);
		$timestamp = time();
		$sequence = $invoice->id + $timestamp - 1251679000;
		$hash = $this->generate_fingerprint($host->api_login, $sequence, $timestamp, $amount, $host->api_transaction_key);
		$type = $host->transaction_type;
		$relay_url = root_url('/api_pay_authorize_relay_response/' . ($admin ? 'admin/' : ''), true);

		$fields['x_amount'] = (string)$amount;
		$fields['x_backend'] = $admin ? 'true' : 'false';
		$fields['x_relay_response'] = 'true';
		$fields['x_relay_url'] = $relay_url;
		$fields['x_login'] = $host->api_login;
		$fields['x_type'] = $host->transaction_type;
		
		if($host->test_mode)
			$fields['x_test_request'] = 'TRUE';
		
		$fields['x_fp_sequence'] = $sequence;
		$fields['x_fp_hash'] = $hash;
		$fields['x_fp_timestamp'] = $timestamp;
		//$fields['x_currency_code'] = $settings->currency_code;
		
		$fields['x_amount'] = $amount;
		$fields['x_description'] = 'Invoice #' . $invoice->id;
		$fields['x_tax'] = $invoice->goods_tax + $invoice->shipping_tax;
		$fields['x_email'] = $invoice->billing_email;
		
		$fields['x_first_name'] = $invoice->billing_first_name;
		$fields['x_last_name'] = $invoice->billing_last_name;
		$fields['x_address'] = $invoice->billing_street_addr;
		
		if($invoice->billing_state)
			$fields['x_state'] = $invoice->billing_state->code;
		
		$fields['x_zip'] = $invoice->billing_zip;
		$fields['x_country'] = $invoice->billing_country->name;
		$fields['x_city'] = $invoice->billing_city;
		
		$fields['x_phone'] = $invoice->billing_phone;
		$fields['x_company'] = $invoice->billing_company;
		
		$fields['x_invoice_num'] = $invoice->id;
		
		$user_ip = Phpr::$request->get_user_ip();
		
		if($user_ip == '::1')
			$user_ip = '192.168.1.254';
			
		$fields['x_customer_ip'] = $user_ip;
		
		$fields['x_ship_to_first_name'] = $invoice->shipping_first_name;
		$fields['x_ship_to_last_name'] = $invoice->shipping_last_name;
		
		if($invoice->shipping_company)
			$fields['x_ship_to_company'] = $invoice->shipping_company;
			
		$fields['x_ship_to_address'] = $invoice->shipping_street_addr;
		$fields['x_ship_to_city'] = $invoice->shipping_city;
		
		if($invoice->shipping_state)
			$fields['x_ship_to_state'] = $invoice->shipping_state->code;
			
		$fields['x_ship_to_zip'] = $invoice->shipping_zip;
		$fields['x_ship_to_country'] = $invoice->shipping_country->name;
		
		foreach ($fields as &$field)
			$field = Phpr_String::asciify($field, true);
		
		return $fields;
	}

	public function subscribe_access_points() 
	{
		return array(
			'api_pay_authorize_relay_response' => 'process_payment_relay_response'
		);
	}
	
	protected function get_status_name($status_id)
	{
		$status_id = strtoupper($status_id);
		
		$names = array(
			'AUTHORIZEDPENDINGCAPTURE'   => 'Authorized, pending capture',
			'CAPTUREDPENDINGSETTLEMENT'  => 'Captured, pending settlement',
			'COMMUNICATIONERROR'         => 'Communication error',
			'REFUNDSETTLEDSUCCESSFULLY'  => 'Refund, settled successfully',
			'REFUNDPENDINGSETTLEMENT'    => 'Refund, pending settlement',
			'APPROVEDREVIEW'             => 'Approved review',
			'DECLINED'                   => 'Declined',
			'COULDNOTVOID'               => 'Could not void',
			'EXPIRED'                    => 'Expired',
			'GENERALERROR'               => 'General error',
			'PENDINGFINALSETTLEMENT'     => 'Pending final settlement',
			'PENDINGSETTLEMENT'          => 'Pending settlement',
			'FAILEDREVIEW'               => 'Failed review',
			'SETTLEDSUCCESSFULLY'        => 'Settled successfully',
			'SETTLEMENTERROR'            => 'Settlement error',
			'UNDERREVIEW'                => 'Under review',
			'UPDATINGSETTLEMENT'         => 'Updating settlement',
			'VOIDED'                     => 'Voided',
			'FDSPENDINGREVIEW'           => 'FDS, pending review',
			'FDSAUTHORIZEDPENDINGREVIEW' => 'FDS authorized, pending review',
			'RETURNEDITEM'               => 'Returned item',
			'CHARGEBACK'                 => 'Chargeback',
			'CHARGEBACKREVERSAL'         => 'Chargeback reversal',
			'AUTHORIZEDPENDINGRELEASE'   => 'Authorized, pending release',
			'AUTH_CAPTURE'               => 'Authorization and Capture',
			'AUTH_ONLY'                  => 'Authorization',
			'CAPTURE_ONLY'               => 'Capture',
			'CREDIT'                     => 'Credit',
			'PRIOR_AUTH_CAPTURE'         => 'Prior Authorization and Capture',
			'VOID'                       => 'Void'
		);
		
		if (array_key_exists($status_id, $names))
			return $names[$status_id];
			
		return 'Unknown';
	}

	protected function get_avs_status_text($status_code)
	{
		$status_code = strtoupper($status_code);
		
		if (!strlen($status_code))
			return 'AVS response code is empty';
		
		$status_names = array(
			'A' => 'Address (Street) matches, ZIP does not',
			'B' => 'Address information not provided for AVS check',
			'E' => 'AVS error',
			'G' => 'Non-U.S. Card Issuing Bank',
			'N' => 'No Match on Address (Street) or ZIP',
			'P' => 'AVS not applicable for this transaction',
			'R' => 'Retry – System unavailable or timed out',
			'S' => 'Service not supported by issuer',
			'U' => 'Address information is unavailable',
			'W' => 'Nine digit ZIP matches, Address (Street) does not',
			'X' => 'Address (Street) and nine digit ZIP match',
			'Y' => 'Address (Street) and five digit ZIP match',
			'Z' => 'Five digit ZIP matches, Address (Street) does not'
		);
		
		if (array_key_exists($status_code, $status_names))
			return $status_names[$status_code];

		return 'Unknown AVS response code';
	}
	
	protected function get_ccv_status_text($status_code)
	{
		$status_code = strtoupper($status_code);
		
		if (!strlen($status_code))
			return 'CCV response code is empty';

		$status_names = array(
			'M' => 'Match',
			'N' => 'No Match',
			'P' => 'Not Processed',
			'S' => 'Should have been present',
			'U' => 'Issuer unable to process request'
		);
		
		if (array_key_exists($status_code, $status_names))
			return $status_names[$status_code];

		return 'Unknown CCV response code';
	}
	
	public function relay_error($message) 
	{
		die(print($message));
	}
	
	public function process_payment_relay_response($params) 
	{
		$fields = $_POST;
		$invoice = null;
		
		try 
		{
			if(post('x_response_code') !== '1')
				throw new Phpr_ApplicationException(post('x_response_reason_text'));
			
			// find order and load payment method settings
			$invoice_hash = post('x_invoice_num');
			if(!$invoice_hash)
				throw new Phpr_ApplicationException('Invoice not found');

			$invoice = Payment_Invoice::create()->find($invoice_hash);
			if(!$invoice)
				throw new Phpr_ApplicationException('Invoice not found');

			if(!$invoice->payment_method)
				throw new Phpr_ApplicationException('Payment method not found');

			$invoice->payment_method->init_form_fields();
			$payment_type_obj = $invoice->payment_method->get_paymenttype_object();
		
			if(!($payment_type_obj instanceof Payment_Authorize_Dpm_Gateway))
				throw new Phpr_ApplicationException('Invalid payment method.');

			$is_admin = post('x_admin') === 'true';

			// validate the transaction
			$hash = strtoupper(md5($invoice->payment_method->md5_hash_value . $invoice->payment_method->api_login . post('x_trans_id') . post('x_amount')));

			if($hash != post('x_MD5_Hash'))
				throw new Phpr_ApplicationException('Invalid transaction.');
			
			/*
			 * Mark order as paid
			 */
			
			if($invoice->mark_as_payment_processed())
			{
				Payment_Invoice_Log::create_record($invoice->payment_method->invoice_status, $invoice);

				$this->log_payment_attempt(
					$invoice, 
					'Successful payment', 
					1, 
					array(), 
					$fields, 
					null, 
					post('x_cvv2_resp_code'),
					$this->get_ccv_status_text(post('x_cvv2_resp_code')),
					post('x_avs_code'), 
					$this->get_avs_status_text(post('x_avs_code'))
				);
				
				/*
				 * Log transaction create/change
				 */

				$this->update_transaction_status($invoice->payment_method, $invoice, post('x_trans_id'), $this->get_status_name(post('x_type')), post('x_type'));
			}
			
			if(!$is_admin) 
			{
				$return_page = $invoice->payment_method->receipt_page;
				
				if($return_page)
					$redirect_url = root_url($return_page->url . '/' . $invoice->order_hash . '?utm_nooverride=1', true);
				else 
					throw new Phpr_ApplicationException($this->get_info()->name . ' return page is not found');
			} 
			else 
			{
				$redirect_url = root_url('/', true) . url('/payment/orders/payment_accepted/' . $invoice->id . '?utm_nooverride=1&nocache' . uniqid());
			}
			
			die(include(PATH_APP . '/modules/payment/payment_types/payment_authorize_dpm_gateway/relay_response.php'));
		}
		catch (Exception $ex) 
		{
			if($invoice) 
			{
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), $fields, null);
			}
			
			$this->relay_error($ex->getMessage());
		}
	}

	public function status_deletion_check($host, $status) 
	{
		if($host->invoice_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in ' . $this->get_info()->name . ' payment method.');
	}
	
	protected function init_sdk($host_obj)
	{
		if (self::$sdk_initialized)
			return;
			
		self::$sdk_initialized = true;
		
		define('AUTHORIZENET_SANDBOX', $host_obj->use_test_server ? true : false);
		define('AUTHORIZENET_API_LOGIN_ID', $host_obj->api_login);
		define('AUTHORIZENET_TRANSACTION_KEY', $host_obj->api_transaction_key);
		
		$path = dirname(__FILE__).'/'.strtolower(get_class($this));

		require_once $path.'/lib/shared/AuthorizeNetRequest.php';
		require_once $path.'/lib/shared/AuthorizeNetTypes.php';
		require_once $path.'/lib/shared/AuthorizeNetXMLResponse.php';
		require_once $path.'/lib/shared/AuthorizeNetResponse.php';
		require_once $path.'/lib/AuthorizeNetAIM.php';
		require_once $path.'/lib/AuthorizeNetCIM.php';
		require_once $path.'/lib/AuthorizeNetTD.php';
	}

	/*
	 * Transaction management methods
	 */
	
	/**
	 * This method should return TRUE if the payment gateway supports requesting a status of a specific transaction
	 */
	public function supports_transaction_status_query()
	{
		return true;
	}

	public function list_available_transaction_transitions($host_obj, $transaction_id, $transaction_status_code)
	{
		$transaction_status_code = strtoupper($transaction_status_code);

		switch ($transaction_status_code)
		
		{
			case 'AUTH_ONLY' :
			case 'AUTHORIZEDPENDINGCAPTURE' :
				return array(
					'prior_auth_capture' => 'Prior Authorization and Capture',
					'void' => 'Void'
				);
			break;
			case 'AUTH_CAPTURE' :
				return array(
					'credit' => 'Credit (refund)',
					'void' => 'Void'
				);
			break;
			case 'SETTLEDSUCCESSFULLY' :
				return array(
					'credit' => 'Credit (refund)'
				);
			break;
			case 'AUTHORIZEDPENDINGCAPTURE' :
			case 'CAPTUREDPENDINGSETTLEMENT' :
			case 'REFUNDPENDINGSETTLEMENT' :
			case 'APPROVEDREVIEW' :
			case 'PENDINGFINALSETTLEMENT' :
			case 'PENDINGSETTLEMENT' :
			case 'AUTH_CAPTURE' :
			case 'AUTH_ONLY' :
			case 'CAPTURE_ONLY' :
			case 'PRIOR_AUTH_CAPTURE' :
				return array(
					'void' => 'Void'
				);
			break;
		}
		
		return array();
	}
	
	public function set_transaction_status($host_obj, $invoice, $transaction_id, $transaction_status_code, $new_transaction_status_code)
	{
		$this->init_sdk($host_obj);
		

		$td_request = new AuthorizeNetTD();
		$td_request->VERIFY_PEER = false;
		
		$transaction_details = $td_request->getTransactionDetails($transaction_id);
		
		if (!$transaction_details->xml)
			throw new Phpr_ApplicationException('Error requesting transaction status: cannot load data from the gateway.');
		
		if ($transaction_details->is_error())
			throw new Phpr_ApplicationException($transaction_details->getErrorMessage());
		
		$aim_request = new AuthorizeNetAIM();
		$aim_request->VERIFY_PEER = false;
		$aim_request->setFields(array('trans_id' => $transaction_id));
		
		$override_status_name = false;

		switch ($new_transaction_status_code)
		
		{
			case 'prior_auth_capture' : 
				$submitResult = $aim_request->priorAuthCapture();
			break;
			case 'void' : 
				
				$submitResult = $aim_request->void();
			break;
			case 'credit' : 
				$aim_request->setFields(array(
					'card_num' => substr((string)$transaction_details->xml->transaction->payment->creditCard->cardNumber, -4),
					'amount' => (string)$transaction_details->xml->transaction->authAmount
				));
				$submitResult = $aim_request->credit();

				$override_status_name = 'Refund requested';
			break;
			default:
				throw new Phpr_ApplicationException('Unknown transaction status code: '.$new_transaction_status_code);
		}

		if (!$submitResult->approved)
		
		{
			$error_str = $submitResult->error_message;

			if ($error_str)
				throw new Phpr_ApplicationException($error_str);
			else
				throw new Phpr_ApplicationException('Error updating transaction status.');
		} 
		else 
		{
			$result = $this->request_transaction_status($host_obj, $transaction_id);
			if ($override_status_name)
				$result->transaction_status_name = $override_status_name;

			return $result;
		}
	}

	public function request_transaction_status($host_obj, $transaction_id)
	{
		$this->init_sdk($host_obj);

		$request = new AuthorizeNetTD();
		$request->VERIFY_PEER = false;
		
		$response = $request->getTransactionDetails($transaction_id);
		if ($response->is_error())
			throw new Phpr_ApplicationException($response->getErrorMessage());
			
		if (!$response->xml)
			throw new Phpr_ApplicationException('Error requesting transaction status: cannot load data from the gateway.');

		$status = (string)($response->xml->transaction->transactionStatus);

		return new Payment_Type_Log(
			$status,
			$this->get_status_name($status)
		);
	}
}
