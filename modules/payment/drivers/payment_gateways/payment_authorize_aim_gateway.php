<?php

class Payment_Authorize_Aim_Gateway extends Payment_Type_Base
{
	protected static $sdk_initialized = false;
	
	public function get_info()
	{
		return array(); // Disabled
		return array(
			'name'=>'Authorize.Net (AIM)',
			'description'=>'Authorize.net Advanced Integration Method (AIM) with payment form hosted on your server'
		);
	}

	public function build_config_ui($host, $context = null)
	{
		$host->add_field('test_mode', 'Create Test Transactions')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Mark all transactions as test transactions. You can create test transactions in the live environment. <strong>Important!</strong> Test transactions are not supported by the Authorize.Net customer profiles. Use a test server, or put your live account into the test mode if you want to test payments with stored credit cards.', 'above', true);
		$host->add_field('use_test_server', 'Use Test Server')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Connect to Authorize.Net test server (test.authorize.net). Use this option if you have Authorize.Net developer test account.', 'above');

		if ($context !== 'preview')
		{
			$host->add_field('api_login', 'API Login ID', 'left')->tab('Configuration')->display_as(frm_text)->comment('The merchant API Login ID is provided in the Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide API Login ID.');
			$host->add_field('api_transaction_key', 'Transaction Key', 'right')->tab('Configuration')->display_as(frm_text)->comment('The merchant Transaction Key is provided in the Merchant Interface.', 'above')->validation()->fn('trim')->required('Please provide Transaction Key.');
		}

		$host->add_field('transaction_type', 'Transaction Type', 'left')->tab('Configuration')->display_as(frm_dropdown)->comment('The type of credit card transaction you want to perform.', 'above');
		$host->add_field('invoice_status', 'Invoice Status', 'right')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment.', 'above');

		$host->add_field('skip_itemized_data', 'Do not submit itemized order information')->tab('Configuration')->display_as(frm_checkbox)->comment('Enable this option if you don\'t want to submit itemized order information with a transaction. Please note that Authorize.Net allows up to 30 line items per transaction. This feature is automatically enabled for all orders which have more than 30 unique items.', 'above');
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

	public function validate_config_on_save($host)
	{
		
	}

	public function validate_config_on_load($host)
	{
	}

	public function init_config_data($host)
	{
		$host->test_mode = 1;
		$host->use_test_server = 1;
		$host->invoice_status = Payment_Invoice_Status::get_status_paid()->id;
	}

	public function build_payment_form($host)
	{
		$host->add_field('FIRSTNAME', 'First Name', 'left')->display_as(frm_text)->comment('Cardholder first name', 'above')->validation()->fn('trim')->required('Please specify a cardholder first name');
		$host->add_field('LASTNAME', 'Last Name', 'right')->display_as(frm_text)->comment('Cardholder last name', 'above')->validation()->fn('trim')->required('Please specify a cardholder last name');
		$host->add_field('ACCT', 'Credit Card Number', 'left')->display_as(frm_text)->validation()->fn('trim')->required('Please specify a credit card number')->regexp('/^[0-9]+$/', 'Credit card number can contain only digits.');
		$host->add_field('CVV2', 'CVV2', 'right')->display_as(frm_text)->validation()->fn('trim')->required('Please specify Card Verification Number')->numeric();

		$host->add_field('EXPDATE_MONTH', 'Expiration Month', 'left')->display_as(frm_text)->validation()->fn('trim')->required('Please specify card expiration month')->numeric();
		$host->add_field('EXPDATE_YEAR', 'Expiration Year', 'right')->display_as(frm_text)->validation()->fn('trim')->required('Please specify card expiration year')->numeric();

		$host->add_field('create_customer_profile', 'Save credit card')->display_as(frm_checkbox)->comment('The credit card information will be saved on Authorize.Net server. You and the customer can use the saved credit card data for future payments.');
	}

	// Payment processing
	// 

	private function format_form_fields(&$fields)
	{
		$result = array();
		foreach($fields as $key=>$val)
			$result[] = urlencode($key)."=".urlencode($val); 
		
		return implode('&', $result);
	}
	
	private function post_data($endpoint, $fields)
	{
		$poststring = array();

		foreach($fields as $key=>$val)
		{
			if ($key != 'x_line_item')
				$poststring[] = urlencode($key)."=".urlencode(Phpr_String::asciify($val, true)); 
			else
			{
				foreach ($val as $item)
					$poststring[] = urlencode($key)."=".urlencode(mb_convert_encoding($item, 'HTML-ENTITIES', 'UTF-8'));
			}
		}

		$poststring = implode('&', $poststring);
		$url = "https://".$endpoint;

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		
		if (curl_errno($ch))
			throw new Phpr_ApplicationException( "Error connecting the payment gateway: ".curl_error($ch) );
		else
			curl_close($ch);
			
		return $response;
	}

	private function parse_response($response)
	{
		return explode("|", $response);
	}

	private function prepare_fields_log($fields)
	{
		unset($fields['x_login']);
		unset($fields['x_tran_key']);
		unset($fields['x_card_code']);

		if (isset($fields['x_line_item']))
		{
			foreach ($fields['x_line_item'] as $index=>$line_item)
				$fields['x_line_item_'.$index] = $line_item;

			unset($fields['x_line_item']);
		}

		$fields['x_card_num'] = '...'.substr($fields['x_card_num'], -4);

		return $fields;
	}
	
	protected function get_status_name($status_id)
	{
		$status_id = strtoupper($status_id);
		
		$names = array(
			'AUTHORIZEDPENDINGCAPTURE'  => 'Authorized, pending capture',
			'CAPTUREDPENDINGSETTLEMENT' => 'Captured, pending settlement',
			'COMMUNICATIONERROR'        => 'Communication error',
			'REFUNDSETTLEDSUCCESSFULLY' => 'Refund, settled successfully',
			'REFUNDPENDINGSETTLEMENT'   => 'Refund, pending settlement',
			'APPROVEDREVIEW'            => 'Approved review',
			'DECLINED'                  => 'Declined',
			'COULDNOTVOID'              => 'Could not void',
			'EXPIRED'                   => 'Expired',
			'GENERALERROR'              => 'General error',
			'PENDINGFINALSETTLEMENT'    => 'Pending final settlement',
			'PENDINGSETTLEMENT'         => 'Pending settlement',
			'FAILEDREVIEW'              => 'Failed review',
			'SETTLEDSUCCESSFULLY'       => 'Settled successfully',
			'SETTLEMENTERROR'           => 'Settlement error',
			'UNDERREVIEW'               => 'Under review',
			'UPDATINGSETTLEMENT'        => 'Updating settlement',
			'VOIDED'                    => 'Voided',
			'FDSPENDINGREVIEW'          => 'FDS, pending review',
			'FDSAUTHORIZEDPENDINGREVIEW'=> 'FDS authorized, pending review',
			'RETURNEDITEM'              => 'Returned item',
			'CHARGEBACK'                => 'Chargeback',
			'CHARGEBACKREVERSAL'        => 'Chargeback reversal',
			'AUTHORIZEDPENDINGRELEASE'  => 'Authorized, pending release',
			'AUTH_CAPTURE'              => 'Authorization and Capture',
			'AUTH_ONLY'                 => 'Authorization',
			'CAPTURE_ONLY'              => 'Capture',
			'CREDIT'                    => 'Credit',
			'PRIOR_AUTH_CAPTURE'        => 'Prior Authorization and Capture',
			'VOID'                      => 'Void'
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

	protected function prepare_exp_date($validation, $profile_mode = false)
	{
		$expMonth = $validation->field_values['EXPDATE_MONTH'] < 10 ? '0'.$validation->field_values['EXPDATE_MONTH'] : $validation->field_values['EXPDATE_MONTH'];
		$expYear = $validation->field_values['EXPDATE_YEAR'];
		if (!$profile_mode)
			$expYear = $expYear > 2000 ? $expYear - 2000 : $expYear;

		if ($expYear < 10)
			$expYear = '0'.$expYear;

		return $profile_mode ? $expYear.'-'.$expMonth : $expMonth.'-'.$expYear;
	}
	
	protected function get_response_field(&$response, $index)
	{
		if (array_key_exists($index, $response))
			return $response[$index];
			
		return null;
	}

	/**
	 * Processes payment using passed data
	 * @param array $data Posted payment form data
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 */
	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		// Validate input data
		//
		
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
		}
			
		// Send request
		// 
		
		@set_time_limit(3600);
		
		$endpoint = $host->use_test_server ? "test.authorize.net/gateway/transact.dll" : "secure.authorize.net/gateway/transact.dll";

		$fields = array();
		$response = null;
		$response_fields = array();

		$settings = Payment_Config::create();
		$currency_converter = Payment_Currency::create();

		try
		{
			$userIp = Phpr::$request->get_user_ip();
			if ($userIp == '::1')
				$userIp = '192.168.1.254';

			$fields['x_login'] = $host->api_login;
			$fields['x_tran_key'] = $host->api_transaction_key;
			$fields['x_version'] = '3.1';
			
			if ($host->test_mode)
				$fields['x_test_request'] = 'TRUE';

			$fields['x_delim_data'] = 'TRUE';
			$fields['x_delim_char'] = '|';
			$fields['x_relay_response'] = 'FALSE';
			$fields['x_type'] = $host->transaction_type;
			$fields['x_method'] = 'CC';
			
			$fields['x_card_num'] = $validation->field_values['ACCT'];
			$fields['x_card_code'] = $validation->field_values['CVV2'];
			$fields['x_exp_date'] = $this->prepare_exp_date($validation);

			$fields['x_amount'] = $currency_converter->convert($invoice->total, $settings->currency_code, 'USD');
			$fields['x_description'] = 'Invoice #'.$invoice->id;
			$fields['x_tax'] = $currency_converter->convert($invoice->goods_tax + $invoice->shipping_tax, $settings->currency_code, 'USD');
			
			$fields['x_email'] = $invoice->billing_email;
			
			$fields['x_first_name'] = $validation->field_values['FIRSTNAME'];
			$fields['x_last_name'] = $validation->field_values['LASTNAME'];
			$fields['x_address'] = $invoice->billing_street_addr;
			
			if ($invoice->billing_state)
				$fields['x_state'] = $invoice->billing_state->code;
				
			$fields['x_zip'] = $invoice->billing_zip;
			$fields['x_country'] = $invoice->billing_country->name;
			$fields['x_city'] = $invoice->billing_city;
			
			$fields['x_phone'] = $invoice->billing_phone;
			$fields['x_company'] = $invoice->billing_company;
			
			$fields['x_invoice_num'] = $invoice->id;
			$fields['x_customer_ip'] = $userIp;
			$fields['x_customer_id'] = $invoice->customer->id;
			
			$fields['x_ship_to_first_name'] = $invoice->shipping_first_name;
			$fields['x_ship_to_last_name'] = $invoice->shipping_last_name;
			
			if ($invoice->shipping_company)
				$fields['x_ship_to_company'] = $invoice->shipping_company;
				
			$fields['x_ship_to_address'] = $invoice->shipping_street_addr;
			$fields['x_ship_to_city'] = $invoice->shipping_city;
			
			if ($invoice->shipping_state)
				$fields['x_ship_to_state'] = $invoice->shipping_state->code;
				
			$fields['x_ship_to_zip'] = $invoice->shipping_zip;
			$fields['x_ship_to_country'] = $invoice->shipping_country->name;
			
			if (!$host->skip_itemized_data && $invoice->items->count <= 29)
			{
				$fields['x_line_item'] = array();
				$item_index = 0;
				foreach ($invoice->items as $item)
				{
					$item_array = array();

					$product_name = str_replace("\n", "", $item->description);

					$item_array[] = Phpr_Html::str_trim($item->product->sku ? $item->product->sku : $item->product->id, 28);
					$item_array[] = Phpr_Html::str_trim($item->product->name, 28);
					$item_array[] = Phpr_Html::str_trim($product_name, 252);
					$item_array[] = $item->quantity;
					$item_array[] = $currency_converter->convert($item->price, $settings->currency_code, 'USD');

					$item_array[] = $item->tax > 0 ? 'Y' : 'N';

					$fields['x_line_item'][] = implode('<|>', $item_array);
				}

				// Add "shipping cost product"
				//
			
				if ((float)$invoice->shipping_quote)
				{
					$item_array = array();
					$item_array[] = 'Shipping';
					$item_array[] = 'Shipping';
					$item_array[] = Phpr_Html::str_trim('Shipping - '.$invoice->shipping_method->name, 252);
					$item_array[] = 1;
					$item_array[] = $currency_converter->convert($invoice->shipping_quote, $settings->currency_code, 'USD');
				
					$item_array[] = $item->shipping_tax > 0 ? 'Y' : 'N';
				
					$fields['x_line_item'][] = implode('<|>', $item_array);
				}
			}

			$response = $this->post_data($endpoint, $fields);

			// Process result
			//
	
			$response_fields = $this->parse_response($response);
			if (!array_key_exists(0, $response_fields))
				throw new Phpr_ApplicationException('Invalid Authorize.Net response.');

			if ($response_fields[0] != 1)
				throw new Phpr_ApplicationException($response_fields[3]);
	
			// Successful payment. Set order status and mark it as paid.
			//

			$this->log_payment_attempt(
				$invoice, 
				'Successful payment', 
				1, 
				$this->prepare_fields_log($fields), 
				$response_fields, 
				$response,
				$response_fields[38],
				$this->get_ccv_status_text($response_fields[38]),
				$response_fields[5], 
				$this->get_avs_status_text($response_fields[5])
			);
			
			// Log transaction create/change
			$this->update_transaction_status($host, $invoice, $response_fields[6], $this->get_status_name($response_fields[11]), $response_fields[11]);

			// Change order status
			Payment_Invoice_Log::create_record($host->invoice_status, $invoice);

			// Mark order as paid
			$invoice->mark_as_payment_processed();
		}
		catch (Exception $ex)
		{
			$fields = $this->prepare_fields_log($fields);
			
			$ccv_code = $this->get_response_field($response_fields, 38);
			$avs_code = $this->get_response_field($response_fields, 5);
			
			$this->log_payment_attempt(
				$invoice, 
				$ex->getMessage(), 
				0, 
				$fields, 
				$response_fields, 
				$response,
				$ccv_code,
				$this->get_ccv_status_text($ccv_code),
				$avs_code, 
				$this->get_avs_status_text($avs_code)
			);
			
			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}
	
	public function status_deletion_check($host, $status)
	{
		if ($host->invoice_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in Authorize.Net AIM payment method.');
	}
	
	protected function init_sdk($host)
	{
		if (self::$sdk_initialized)
			return;
			
		self::$sdk_initialized = true;
		
		define('AUTHORIZENET_SANDBOX', $host->use_test_server ? true : false);
		define('AUTHORIZENET_API_LOGIN_ID', $host->api_login);
		define('AUTHORIZENET_TRANSACTION_KEY', $host->api_transaction_key);
		
		$path = dirname(__FILE__).'/'.strtolower(get_class($this));

		require_once $path.'/lib/shared/AuthorizeNetRequest.php';
		require_once $path.'/lib/shared/AuthorizeNetTypes.php';
		require_once $path.'/lib/shared/AuthorizeNetXMLResponse.php';
		require_once $path.'/lib/shared/AuthorizeNetResponse.php';
		require_once $path.'/lib/AuthorizeNetAIM.php';
		require_once $path.'/lib/AuthorizeNetCIM.php';
		require_once $path.'/lib/AuthorizeNetTD.php';
	}
	
	// Transaction management methods
	//
	
	public function supports_transaction_status_query()
	{
		return true;
	}

	public function list_available_transaction_transitions($host, $transaction_id, $transaction_status_code)
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

	public function set_transaction_status($host, $invoice, $transaction_id, $transaction_status_code, $new_transaction_status_code)
	{
		$this->init_sdk($host);
		

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
		} else {
			$result = $this->request_transaction_status($host, $transaction_id);
			if ($override_status_name)
				$result->transaction_status_name = $override_status_name;

			return $result;
		}
	}
	
	public function request_transaction_status($host, $transaction_id)
	{
		$this->init_sdk($host);

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

