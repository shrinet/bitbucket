<?php

class Payment_Moneybookers_Gateway extends Payment_Type_Base
{
	/**
	 * Returns information about the payment type
	 * Must return array: array(
	 *      'name'=>'Authorize.net',
	 *      'custom_payment_form'=>false,
	 *      'offline'=>false,
	 *      'pay_offline_message'=>null
	 * ).
	 * Use custom_paymen_form key to specify a name of a partial to use for building a admin
	 * payment form. Usually it is needed for forms which ACTION refer outside web services,
	 * like PayPal Standard. Otherwise override build_payment_form method to build admin payment
	 * forms.
	 * If the payment type provides a front-end partial (containing the payment form),
	 * it should be called in following way: payment:name, in lower case, e.g. payment:authorize.net
	 *
	 * Set index 'offline' to true to specify that the payments of this type cannot be processed online
	 * and thus they have no payment form. You may specify a message to display on the payment page
	 * for offline payment type, using 'pay_offline_message' index.
	 *
	 * @return array
	 */
	public function get_info()
	{
		return array(
			'name' => 'Moneybookers',
			'description' => 'Moneybookers payment method with payment form hosted on Moneybookers server'
		);
	}

	/**
	 * Builds the payment type administration user interface
	 * For drop-down and radio fields you should also add methods returning
	 * options. For example, if you want to have Sizes drop-down:
	 * public function get_sizes_options();
	 * This method should return array with keys corresponding your option identifiers
	 * and values corresponding its titles.
	 *
	 * @param $host ActiveRecord object to add fields to
	 * @param string $context Form context. In preview mode its value is 'preview'
	 */
	public function build_config_ui($host, $context = null)
	{
		$host->add_field('recipient_description', 'Business name')->tab('Configuration')->display_as(frm_text)->comment('Enter the name of your business as you want it shown on the gateway.', 'above')->validation()->fn('trim');
		$host->add_field('business_email', 'Business Email')->tab('Configuration')->display_as(frm_text)->comment('Enter yours business account email address.', 'above')->validation()->fn('trim')->required('Please provide Moneybookers business account email address.')->email('Please provide valid email address in Business Email field.');                

		if ($context !== 'preview')
		{
			$host->add_form_partial($host->get_partial_path('hint.htm'))->tab('Configuration');
			$host->add_field('secret_word', 'Secret Word')->tab('Configuration')->display_as(frm_text)->comment('Enter your Secret Word as mentioned above.', 'above')->validation()->fn('trim')->required('Please provide your Moneybookers Secret Word.');         
		}

		$host->add_field('cancel_page', 'Cancel Page', 'left')->tab('Configuration')->display_as(frm_dropdown)->comment('Page to which the user’s browser is redirected if payment is cancelled.', 'above');
		$host->add_field('invoice_status', 'Invoice Status', 'right')->tab('Configuration')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment.', 'above');
	}

	public function get_invoice_status_options($key_value = -1)
	{
		if ($key_value == -1)
			return Payment_Invoice_Status::create()->order('name')->find_all()->as_array('name', 'id');

		return Payment_Invoice_Status::create()->find($key_value)->name;
	}

	/**
	 * Validates configuration data before it is saved to database
	 * Use host object field_error method to report about errors in data:
	 * $host->field_error('max_weight', 'Max weight should not be less than Min weight');
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function validate_config_on_save($host)
	{

	}

	/**
	 * Validates configuration data after it is loaded from database
	 * Use host object to access fields previously added with build_config_ui method.
	 * You can alter field values if you need
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function validate_config_on_load($host)
	{
	}

	/**
	 * Initializes configuration data when the payment method is first created
	 * Use host object to access and set fields previously added with build_config_ui method.
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function init_config_data($host)
	{

	}

	public function get_cancel_page_options($key_value = -1)
	{
		$page = Cms_Page::create();
		$theme = Cms_Theme::get_edit_theme();
		if ($theme)
			$page->where('theme_id=?', $theme->code);

		if ($key_value == -1)
			return $page->order('title')->find_all()->as_array('title', 'id');

		$page = $page->find($key_value);
		if ($page)
			return $page->title;
		else
			return "";
	}

	public function get_form_action($host)
	{
		return "https://www.moneybookers.com/app/payment.pl";
	}

	public function get_hidden_fields($host, $invoice, $admin = false)
	{
		$result = array();

		// Billing information
		//

		$result['firstname'] = $invoice->billing_first_name;
		$result['lastname'] = $invoice->billing_last_name;

		$result['address'] = $invoice->billing_street_addr;
		$result['city'] = $invoice->billing_city;
		
		if ($invoice->billing_country)
			$result['country'] = $invoice->billing_country->code;

		if ($invoice->billing_state)
			$result['state'] = $invoice->billing_state->code;

		$result['postal_code'] = $invoice->billing_zip;
		$result['phone_number'] = $invoice->billing_phone;

		// Invoice items
		//

		$item_index = 2;
		foreach ($invoice->items as $item)
		{
			$result['amount'.$item_index.'description'] = $item->description;
			$result['amount'.$item_index] = round($item->price, 2);
			$item_index++;
		}

		// Payment setup
		//

		$result['amount'] = $invoice->total;
		$result['transaction_id'] = $invoice->id;
		$result['pay_to_email'] = $host->business_email;
		$result['currency'] = Payment_Config::create()->currency_code;
		$result['language'] = Core_Config::create()->get_locale_language();
		
		// @todo
		$result['confirmation_note'] = "Samplemerchant wishes you pleasure reading your new book!";
		// @todo (Dimensions 200 x 50)
		$result['logo_url'] = "gateway/moneybookers/monlogoenvrai"; 

		$result['merchant_fields'] = "field1";      
		$result['field1'] = $invoice->id;

		$result['status_url'] = Phpr::$request->getRootUrl().root_url('/api_pay_moneybookers_status_url/'.$invoice->hash);
		if (!$admin)
		{
			$result['return_url'] = Phpr::$request->getRootUrl().root_url('/api_pay_moneybookers_return_url/'.$invoice->hash);

			$cancel_page = Cms_Page::create()->find($host->cancel_page);
			if ($cancel_page)
			{
				$result['cancel_url'] = Phpr::$request->getRootUrl().root_url($cancel_page->url);
				if ($cancel_page->action_reference == 'payment:pay')
					$result['cancel_url'] .= '/'.$invoice->hash;
				elseif ($cancel_page->action_reference == 'payment:invoice')
					$result['cancel_url'] .= '/'.$invoice->id;
			}
		} 
		else
		{
			$result['return_url'] = Phpr::$request->getRootUrl().root_url('/api_pay_moneybookers_return_url/'.$invoice->hash.'/admin');
			$result['cancel_url'] = Phpr::$request->getRootUrl().url('payment/pay/'.$invoice->id.'?'.uniqid());
		}

		$result['bn'] = 'PHPRoad.Framework.2.0';
		$result['charset'] = 'utf-8';

		foreach ($result as $key=>$value)
		{
			$result[$key] = str_replace("\n", ' ', $value);
		}
		return $result;
	}

	/**
	 * Processes payment using passed data
	 * @param array $data Posted payment form data
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 */
	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		/*
		 * We do not need any code here since payments are processed on PayPal server.
		 */
	}

	/**
	 * Registers a hidden page with specific URL. Use this method for cases when you
	 * need to have a hidden landing page for a specific payment gateway. For example,
	 * PayPal needs a landing page for the auto-return feature.
	 * Important! Payment module access point names should have the api_pay_ prefix.
	 * @return array Returns an array containing page URLs and methods to call for each URL:
	 * return array('api_pay_paypal_autoreturn'=>'process_paypal_autoreturn'). The processing methods must be declared
	 * in the payment type class. Processing methods must accept one parameter - an array of URL segments
	 * following the access point. For example, if URL is /api_pay_paypal_autoreturn/1234 an array with single
	 * value '1234' will be passed to process_paypal_autoreturn method
	 */
	public function subscribe_access_points()
	{
		return array(
			'api_pay_moneybookers_return_url'=>'process_moneybookers_return_url',
			'api_pay_moneybookers_status_url'=>'process_moneybookers_status_url'
		);
	}

	public function process_moneybookers_status_url($params)
	{
		try
		{
			$invoice = null;

			// Find invoice and load Moneybookers settings
			//

			sleep(5);

			$hash = array_key_exists(0, $params) ? $params[0] : null;
			if (!$hash)
				throw new Phpr_ApplicationException('Invoice not found');

			$invoice = Payment_Invoice::create()->find_by_hash($hash);
			if (!$invoice)
				throw new Phpr_ApplicationException('Invoice not found');

			if (!$invoice->payment_type)
				throw new Phpr_ApplicationException('Payment method not found');

			$invoice->payment_type->init_form_fields();

			if ($invoice->payment_type->class_name != 'Payment_Moneybookers_Gateway')
				throw new Phpr_ApplicationException('Invalid payment method');

			// Validate the Moneybookers signature
			$field_string = $_POST['merchant_id']
				.$_POST['transaction_id']
				.strtoupper(md5($invoice->payment_type->secret_word))
				.$_POST['mb_amount']
				.$_POST['mb_currency']
				.$_POST['status'];

				// Ensure the signature is valid, the status code == 2,
				// and that the money is going to you
				if (strtoupper(md5($field_string)) == $_POST['md5sig']
					&& $_POST['status'] == 2
					&& $_POST['pay_to_email'] == $host->business_email)
				{
					// Valid transaction
					if ($invoice->mark_as_payment_processed())
						{
							$this->log_payment_attempt($invoice, 'Successful payment', 1, array(), $_POST, $response);
							Payment_Invoice_Log::create_record($invoice->payment_type->invoice_status, $invoice);
						}
				}
				else
				{
					// Invalid transaction. Abort
					$this->log_payment_attempt($invoice, 'Invalid payment notification', 0, array(), $_POST, $response);
				}
		}
		catch (Exception $ex)
		{
			if ($invoice)
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), $_POST, null);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}   

	public function process_moneybookers_return_url($params)
	{
		try
		{
			// Internals
			$invoice = null;
			$response = null;

			// Find invoice and load paypal settings
			//

			$hash = array_key_exists(0, $params) ? $params[0] : null;
			if (!$hash)
				throw new Phpr_ApplicationException('Invoice not found');

			$invoice = Payment_Invoice::create()->find_by_hash($hash);
			if (!$invoice)
				throw new Phpr_ApplicationException('Invoice not found');

			if (!$invoice->payment_type)
				throw new Phpr_ApplicationException('Payment method not found');

			$invoice->payment_type->init_form_fields();

			if ($invoice->payment_type->class_name != 'Payment_Moneybookers_Gateway')
				throw new Phpr_ApplicationException('Invalid payment method');

			$google_tracking_code = 'utm_nooverride=1';
			$return_page = $invoice->get_receipt_url();
			if ($return_page)
				Phpr::$response->redirect($return_page.'?'.$google_tracking_code);
			else
				throw new Phpr_ApplicationException('Moneybookers Receipt page is not found');

		}
		catch (Exception $ex)
		{
			if ($invoice)
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), Phpr::$request->get_fields, $response);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}

	/**
	 * This function is called before a CMS page deletion.
	 * Use this method to check whether the payment method
	 * references a page. If so, throw Phpr_ApplicationException
	 * with explanation why the page cannot be deleted.
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param Cms_Page $page Specifies a page to be deleted
	 */
	public function page_deletion_check($host, $page)
	{
		if ($host->cancel_page == $page->id)
			throw new Phpr_ApplicationException('Page cannot be deleted because it is used in Moneybookers payment method as a cancel page');
	}

	/**
	 * This function is called before an invoice status deletion.
	 * Use this method to check whether the payment method
	 * references an invoice status. If so, throw Phpr_ApplicationException
	 * with explanation why the status cannot be deleted.
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param Payment_InvoiceStatus $status Specifies a status to be deleted
	 */
	public function status_deletion_check($host, $status)
	{
		if ($host->invoice_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in Moneybookers payment method');
	}
	
}

