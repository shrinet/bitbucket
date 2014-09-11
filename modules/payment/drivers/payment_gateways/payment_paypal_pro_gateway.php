<?php

class Payment_PayPal_Pro_Gateway extends Payment_Type_Base
{
	public function get_info()
	{
		return array(
			'name'=>'PayPal Pro',
			'description'=>'PayPal Pro payment method, with payment form hosted on your server'
		);
	}

	public function build_config_ui($host, $context = null)
	{
		$host->add_field('test_mode', 'Sandbox Mode')->display_as(frm_onoffswitcher)->comment('Use the PayPal Sandbox Test Environment to test out Website Payments', 'above');

		if ($context !== 'preview')
		{
			$host->add_form_partial($host->get_partial_path('hint.htm'))->tab('Configuration');

			$host->add_field('api_signature', 'API Signature')->display_as(frm_text)->comment('You can find your API signature, user name and password on PayPal profile page in Account Information/API Access section', 'above', true)->validation()->fn('trim')->required('Please provide PayPal API signature.');
			$host->add_field('api_user_name', 'API User Name', 'left')->display_as(frm_text)->validation()->fn('trim')->required('Please provide PayPal API user name');
			$host->add_field('api_password', 'API Password', 'right')->display_as(frm_text)->validation()->fn('trim')->required('Please provide PayPal API password');
		}

		$host->add_field('paypal_action', 'PayPal Action', 'left')->display_as(frm_dropdown)->comment('Action PayPal should perform with buyers credit card', 'above');

		$host->add_field('invoice_status', 'Invoice Status', 'right')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment', 'above');
	}

	public function get_invoice_status_options($key_value = -1)
	{
		if ($key_value == -1)
			return Payment_Invoice_Status::create()->order('name')->find_all()->as_array('name', 'id');

		return Payment_Invoice_Status::create()->find($key_value)->name;
	}

	public function get_paypal_action_options($key_value = -1)
	{
		$options = array(
			'Sale'=>'Capture',
			'Authorization'=>'Authorization only'
		);

		if ($key_value == -1)
			return $options;

		return isset($options[$key_value]) ? $options[$key_value] : null;
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
		$host->invoice_status = Payment_Invoice_Status::get_status_paid()->id;
	}

	public function build_payment_form($host)
	{
		$host->add_field('CREDITCARDTYPE', 'Credit Card Type')->display_as(frm_dropdown)->comment('Please select a credit card type.', 'above')->validation()->fn('trim')->required();
		$host->add_field('FIRSTNAME', 'First Name', 'left')->display_as(frm_text)->comment('Cardholder first name', 'above')->validation()->fn('trim')->required('Please specify a cardholder first name');
		$host->add_field('LASTNAME', 'Last Name', 'right')->display_as(frm_text)->comment('Cardholder last name', 'above')->validation()->fn('trim')->required('Please specify a cardholder last name');
		$host->add_field('ACCT', 'Credit Card Number', 'left')->display_as(frm_text)->validation()->fn('trim')->required('Please specify a credit card number')->regexp('/^[0-9]+$/', 'Credit card number can contain only digits.');
		$host->add_field('CVV2', 'CVV2', 'right')->display_as(frm_text)->validation()->fn('trim')->required('Please specify Card Verification Number')->numeric();

		$host->add_field('EXPDATE_MONTH', 'Expiration Month', 'left')->display_as(frm_text)->display_as(frm_text)->validation()->fn('trim')->required('Please specify card expiration month')->numeric();
		$host->add_field('EXPDATE_YEAR', 'Expiration Year', 'right')->display_as(frm_text)->display_as(frm_text)->validation()->fn('trim')->required('Please specify card expiration year')->numeric();

		$host->add_field('ISSUENUMBER', 'Issue Number')->comment('Please specify the Issue Number or Start Date for Solo and Maestro cards', 'above')->display_as(frm_text)->display_as(frm_text)->validation()->fn('trim')->numeric();

		$host->add_field('STARTDATE_MONTH', 'Start Month', 'left')->display_as(frm_text)->display_as(frm_text)->validation()->fn('trim')->numeric();
		$host->add_field('STARTDATE_YEAR', 'Start Year', 'right')->display_as(frm_text)->display_as(frm_text)->validation()->fn('trim')->numeric();
	}

	public function get_CREDITCARDTYPE_options()
	{
		return array(
			'Visa'=>'Visa',
			'MasterCard'=>'Master Card',
			'Discover'=>'Discover',
			'Amex'=>'American Express',
			'Maestro'=>'Maestro',
			'Solo'=>'Solo'
		);
	}

	public function is_applicable($amount, $host)
	{
		$currency_converter = Payment_Currency::create();
		$settings = Payment_Config::create();

		return $currency_converter->convert($amount, $settings->currency_code, 'USD') <= 10000;
	}

	/*
	 * Payment processing
	 */

	// @todo DITCH THIS - see paypal standard
	private function format_form_fields(&$fields)
	{
		$result = array();
		foreach ($fields as $key=>$val)
			$result[] = urlencode($key)."=".urlencode($val);

		return implode('&', $result);
	}

	// @todo DITCH THIS - see paypal standard
	private function post_data($endpoint, $fields)
	{
		$errno = null;
		$errorstr = null;

		$fp = null;
		try
		{
			$fp = @fsockopen('ssl://'.$endpoint, 443, $errno, $errorstr, 60);
		}
		catch (Exception $ex) {}
		if (!$fp)
			throw new Phpr_SystemException("Error connecting to PayPal server. Error number: $errno, error: $errorstr");

		$poststring = $this->format_form_fields($fields);

		fputs($fp, "POST /nvp HTTP/1.1\r\n");
		fputs($fp, "Host: $endpoint\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $poststring . "\r\n\r\n");

		$response = null;
		while(!feof($fp))
			$response .= fgets($fp, 4096);

		return $response;
	}

	// @todo DITCH THIS - see paypal standard (maybe?)
	private function parse_response($response)
	{
		$matches = array();
		preg_match('/Content\-Length:\s([0-9]+)/i', $response, $matches);
		if (!count($matches))
			throw new Phpr_ApplicationException('Invalid PayPal response');

		$elements = substr($response, $matches[1]*-1);
		$elements = explode('&', $elements);

		$result = array();
		foreach ($elements as $element)
		{
			$element = explode('=', $element);
			if (isset($element[0]) && isset($element[1]))
				$result[$element[0]] = urldecode($element[1]);
		}

		return $result;
	}

	private function prepare_fields_log($fields)
	{
		unset($fields['PWD']);
		unset($fields['USER']);
		unset($fields['SIGNATURE']);
		unset($fields['VERSION']);
		unset($fields['METHOD']);
		unset($fields['CVV2']);
		$fields['ACCT'] = '...'.substr($fields['ACCT'], -4);

		return $fields;
	}

	protected function get_avs_status_text($status_code)
	{
		$status_code = strtoupper($status_code);

		if (!strlen($status_code))
			return 'AVS response code is empty';

		$status_names = array(
			'A' => 'Address only match (no ZIP)',
			'B' => 'Address only match (no ZIP)',
			'C' => 'No match',
			'D' => 'Address and Postal Code match',
			'E' => 'Not allowed for MOTO (Internet/Phone) transactions',
			'F' => 'Address and Postal Code match',
			'G' => 'Not applicable',
			'I' => 'Not applicable',
			'N' => 'No match',
			'P' => 'Postal Code only match (no Address)',
			'R' => 'Retry/not applicable',
			'S' => 'Service not Supported',
			'U' => 'Unavailable/Not applicable',
			'W' => 'Nine-digit ZIP code match (no Address)',
			'X' => 'Exact match',
			'Y' => 'Address and five-digit ZIP match',
			'Z' => 'Five-digit ZIP code match (no Address)',
			'0' => 'All the address information matched',
			'1' => 'None of the address information matched',
			'2' => 'Part of the address information matched',
			'3' => 'The merchant did not provide AVS information. Not processed.',
			'4' => 'Address not checked, or acquirer had no response',
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
			'M'=>'Match',
			'N'=>'No match',
			'P'=>'Not processed',
			'S'=>'Service not supported',
			'U'=>'Service not available',
			'X'=>'No response',
			'0'=>'Match',
			'1'=>'No match',
			'2'=>'The merchant has not implemented CVV2 code handling',
			'3'=>'Merchant has indicated that CVV2 is not present on card',
			'4'=>'Service not available'
		);

		if (array_key_exists($status_code, $status_names))
			return $status_names[$status_code];

		return 'Unknown CCV response code';
	}

	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		/*
		 * Validate input data
		 */

		$validation = new Phpr_Validation();
		$validation->add('CREDITCARDTYPE', 'Credit card type')->fn('trim')->required('Please specify a credit card type.');
		$validation->add('FIRSTNAME', 'Cardholder first name')->fn('trim')->required('Please specify a cardholder first name.');
		$validation->add('LASTNAME', 'Cardholder last name')->fn('trim')->required('Please specify a cardholder last name.');
		$validation->add('EXPDATE_MONTH', 'Expiration month')->fn('trim')->required('Please specify a card expiration month.')->regexp('/^[0-9]*$/', 'Credit card expiration month can contain only digits.');
		$validation->add('EXPDATE_YEAR', 'Expiration year')->fn('trim')->required('Please specify a card expiration year.')->regexp('/^[0-9]*$/', 'Credit card expiration year can contain only digits.');

		$validation->add('ISSUENUMBER', 'Issue Number')->fn('trim')->numeric();

		$validation->add('STARTDATE_MONTH', 'Start Month', 'left')->fn('trim')->numeric();
		$validation->add('STARTDATE_YEAR', 'Start Year', 'right')->fn('trim')->numeric();

		$validation->add('ACCT', 'Credit card number')->fn('trim')->required('Please specify a credit card number.')->regexp('/^[0-9]*$/', 'Please specify a valid credit card number. Credit card number can contain only digits.');
		$validation->add('CVV2', 'CVV2')->fn('trim')->required('Please specify CVV2 value.')->regexp('/^[0-9]*$/', 'Please specify a CVV2 number. CVV2 can contain only digits.');

		try
		{
			if (!$validation->validate($data))
				$validation->throw_exception();
		} catch (Exception $ex)
		{
			$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), array(), null);
			throw $ex;
		}

		/*
		 * Send request
		 */

		@set_time_limit(3600);
		$endpoint = $host->test_mode ? "api-3t.sandbox.paypal.com" : "api-3t.paypal.com";
		$fields = array();
		$response = null;
		$response_fields = array();

		try
		{
			$expMonth = $validation->field_values['EXPDATE_MONTH'] < 10 ? '0'.$validation->field_values['EXPDATE_MONTH'] : $validation->field_values['EXPDATE_MONTH'];

			if (strlen($validation->field_values['STARTDATE_MONTH']))
				$startMonth = $validation->field_values['STARTDATE_MONTH'] < 10 ? '0'.$validation->field_values['STARTDATE_MONTH'] : $validation->field_values['STARTDATE_MONTH'];
			else
				$startMonth = null;

			$userIp = Phpr::$request->get_user_ip();
			if ($userIp == '::1')
				$userIp = '192.168.0.1';

			$fields['PWD'] = $host->api_password;
			$fields['USER'] = $host->api_user_name;
			$fields['SIGNATURE'] = $host->api_signature;
			$fields['VERSION'] = '3.0';
			$fields['METHOD'] = 'DoDirectPayment';

			$fields['CREDITCARDTYPE'] = $validation->field_values['CREDITCARDTYPE'];
			$fields['ACCT'] = $validation->field_values['ACCT'];
			$fields['EXPDATE'] = $expMonth.$validation->field_values['EXPDATE_YEAR'];
			$fields['STARTDATE'] = $startMonth.$validation->field_values['STARTDATE_YEAR'];
			$fields['CVV2'] = $validation->field_values['CVV2'];
			$fields['AMT'] = $invoice->total;
			$fields['ISSUENUMBER'] = $validation->field_values['ISSUENUMBER'];
			$fields['CURRENCYCODE'] = Payment_Config::create()->currency_code;

			$fields['FIRSTNAME'] = $validation->field_values['FIRSTNAME'];
			$fields['LASTNAME'] = $validation->field_values['LASTNAME'];
			$fields['IPADDRESS'] = $userIp;
			$fields['STREET'] = $invoice->billing_street_addr;

			if ($invoice->billing_state)
				$fields['STATE'] = $invoice->billing_state->code;

			$fields['COUNTRY'] = $invoice->billing_country->name;
			$fields['CITY'] = $invoice->billing_city;
			$fields['ZIP'] = $invoice->billing_zip;
			$fields['COUNTRYCODE'] = $invoice->billing_country->code;
			$fields['PAYMENTACTION'] = $host->paypal_action;

			$fields['ITEMAMT'] = $invoice->subtotal;
			$fields['SHIPPINGAMT'] = $invoice->shipping_quote;

			$fields['TAXAMT'] = number_format($invoice->goods_tax + $invoice->shipping_tax, 2, '.', '');

			$item_index = 0;
			foreach ($invoice->items as $item)
			{
				$fields['L_NAME'.$item_index] = mb_substr($item->description, 0, 127);
				$fields['L_AMT'.$item_index] = number_format($item->price, 2, '.', '');
				$fields['L_QTY'.$item_index] = $item->quantity;
				$item_index++;
			}

			if (!ceil($invoice->subtotal) && $invoice->shipping_quote)
			{
				$fields['SHIPPINGAMT'] = '0.00';

				$fields['L_NAME'.$item_index] = 'Shipping';
				$fields['L_AMT'.$item_index] = number_format($invoice->shipping_quote, 2, '.', '');
				$fields['L_QTY'.$item_index] = 1;
				$item_index++;

				$fields['ITEMAMT'] = $invoice->shipping_quote;
			}

			// if ($invoice->discount)
			// {
			// 	$fields['L_NAME'.$item_index] = 'Discount';
			// 	$fields['L_AMT'.$item_index] = number_format(-1*$invoice->discount, 2, '.', '');
			// 	$fields['L_QTY'.$item_index] = 1;
			// 	$item_index++;
			// }

			$fields['SHIPTONAME'] = $invoice->shipping_first_name.' '.$invoice->shipping_last_name;
			$fields['SHIPTOSTREET'] = $invoice->shipping_street_addr;
			$fields['SHIPTOCITY'] = $invoice->shipping_city;
			$fields['SHIPTOCOUNTRYCODE'] = $invoice->shipping_country->code;

			if ($invoice->shipping_state)
				$fields['SHIPTOSTATE'] = $invoice->shipping_state->code;

			$fields['SHIPTOPHONENUM'] = $invoice->shipping_phone;
			$fields['SHIPTOZIP'] = $invoice->shipping_zip;

			$fields['INVNUM'] = $invoice->id;
			$fields['ButtonSource'] = 'ScriptsAhoy_Cart_DP';

			$response = $this->post_data($endpoint, $fields);

			/*
			 * Process result
			 */

			$response_fields = $this->parse_response($response);
			if (!isset($response_fields['ACK']))
				throw new Phpr_ApplicationException('Invalid PayPal response.');

			if ($response_fields['ACK'] !== 'Success' && $response_fields['ACK'] !== 'SuccessWithWarning')
			{
				for ($i=5; $i>=0; $i--)
				{
					if (isset($response_fields['L_LONGMESSAGE'.$i]))
						throw new Phpr_ApplicationException($response_fields['L_LONGMESSAGE'.$i]);
				}

				throw new Phpr_ApplicationException('Invalid PayPal response.');
			}

			/*
			 * Successful payment. Set invoice status and mark it as paid.
			 */

			$fields = $this->prepare_fields_log($fields);

			$this->log_payment_attempt(
				$invoice,
				'Successful payment',
				1,
				$fields,
				$response_fields,
				$response,
				$response_fields['CVV2MATCH'],
				$this->get_ccv_status_text($response_fields['CVV2MATCH']),
				$response_fields['AVSCODE'],
				$this->get_avs_status_text($response_fields['AVSCODE'])
			);

			Payment_Invoice_Log::create_record($host->invoice_status, $invoice);
			$invoice->mark_as_payment_processed();
		}
		catch (Exception $ex)
		{
			$fields = $this->prepare_fields_log($fields);

			$cvv_code = null;
			$cvv_message = null;
			$avs_code = null;
			$avs_message = null;

			if (array_key_exists('CVV2MATCH', $response_fields))
			{
				$cvv_code = $response_fields['CVV2MATCH'];
				$cvv_message = $this->get_ccv_status_text($response_fields['CVV2MATCH']);
				$avs_code = $response_fields['AVSCODE'];
				$avs_message = $this->get_avs_status_text($response_fields['AVSCODE']);
			}

			$this->log_payment_attempt(
				$invoice,
				$ex->getMessage(),
				0,
				$fields,
				$response_fields,
				$response,
				$cvv_code,
				$cvv_message,
				$avs_code,
				$avs_message
			);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}

	public function status_deletion_check($host, $status)
	{
		if ($host->invoice_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in PayPal Pro payment method.');
	}
}

