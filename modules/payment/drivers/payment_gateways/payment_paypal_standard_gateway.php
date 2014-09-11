<?php

class Payment_PayPal_Standard_Gateway extends Payment_Type_Base
{
	public function get_info()
	{
		return array(
			'name'=>'PayPal Standard',
			'description'=>'PayPal Standard payment method with payment form hosted on PayPal server'
		);
	}

	public function build_config_ui($host, $context = null)
	{
		$host->add_field('test_mode', 'Sandbox Mode')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('Use the PayPal Sandbox Test Environment to try out Website Payments. You should be logged into the PayPal Sandbox to work in test mode.', 'above');
		$host->add_field('business_email', 'Business Email')->tab('Configuration')->display_as(frm_text)->comment('PayPal business account email address.', 'above')->validation()->fn('trim')->required('Please provide PayPal business account email address.')->email('Please provide valid email address in Business Email field.');

		if ($context !== 'preview')
		{
			$host->add_form_partial($host->get_partial_path('hint.htm'))->tab('Configuration');
			$host->add_field('pdt_token', 'PDT Token')->tab('Configuration')->display_as(frm_text)->comment('PayPal Payment Data Transfer token.', 'above')->validation()->fn('trim')->required('Please provide PayPal Payment Data Transfer token.');
		}

		$host->add_field('cancel_page', 'Cancel Page', 'left')->tab('Configuration')->display_as(frm_dropdown)->comment('Page to which the user’s browser is redirected if payment is cancelled.', 'above');
		$host->add_field('invoice_status', 'Invoice Status', 'right')->tab('Configuration')->display_as(frm_dropdown)->comment('Select status to assign the invoice in case of successful payment.', 'above');
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

	public function get_invoice_status_options($key_value = -1)
	{
		if ($key_value == -1)
			return Payment_Invoice_Status::create()->order('name')->find_all()->as_array('name', 'id');

		return Payment_Invoice_Status::create()->find($key_value)->name;
	}

	public function get_form_action($host)
	{
		if ($host->test_mode)
			return "https://www.sandbox.paypal.com/cgi-bin/webscr";
		else
			return "https://www.paypal.com/cgi-bin/webscr";
	}

	public function get_hidden_fields($host, $invoice, $admin = false)
	{
		$result = array();

		// Billing information
		//

		$result['first_name'] = $invoice->billing_first_name;
		$result['last_name'] = $invoice->billing_last_name;

		$result['address1'] = $invoice->billing_street_addr;
		$result['city'] = $invoice->billing_city;
		
		if ($invoice->billing_country)
			$result['country'] = $invoice->billing_country->code;

		if ($invoice->billing_state)
			$result['state'] = $invoice->billing_state->code;

		$result['zip'] = $invoice->billing_zip;
		$result['night_phone_a'] = $invoice->billing_phone;

		// Invoice items
		//

		$item_index = 1;
		foreach ($invoice->items as $item)
		{
			$result['item_name_'.$item_index] = $item->description;
			$result['amount_'.$item_index] = round($item->price, 2);
			$result['quantity_'.$item_index] = $item->quantity;
			$item_index++;
		}

		// Payment setup
		//

		$result['no_shipping'] = 1;
		$result['cmd'] = '_cart';
		$result['upload'] = 1;
		$result['tax_cart'] = number_format($invoice->tax, 2, '.', '');
		$result['invoice'] = $invoice->id;
		$result['business'] = $host->business_email;
		$result['currency_code'] = Payment_Config::create()->currency_code;
		$result['tax'] = number_format($invoice->tax, 2, '.', '');

		$result['notify_url'] = Phpr::$request->get_root_url().root_url('api_pay_paypal_ipn/'.$invoice->hash);

		if (!$admin)
		{
			$result['return'] = Phpr::$request->get_root_url().root_url('api_pay_paypal_autoreturn/'.$invoice->hash);

			$cancel_page = Cms_Page::create()->find($host->cancel_page);
			if ($cancel_page)
			{
				$result['cancel_return'] = Phpr::$request->get_root_url().root_url($cancel_page->url);
				if ($cancel_page->action_reference == 'payment:pay')
					$result['cancel_return'] .= '/'.$invoice->hash;
				elseif ($cancel_page->action_reference == 'payment:invoice')
					$result['cancel_return'] .= '/'.$invoice->id;
			}
		} 
		else
		{
			$result['return'] = Phpr::$request->get_root_url().root_url('api_pay_paypal_autoreturn/'.$invoice->hash.'/admin');
			$result['cancel_return'] = Phpr::$request->get_root_url().url('payment/pay/'.$invoice->id.'?'.uniqid());
		}

		$result['bn'] = 'PHPRoad.Framework.2.0';
		$result['charset'] = 'utf-8';

		foreach ($result as $key=>$value)
		{
			$result[$key] = str_replace("\n", ' ', $value);
		}
		return $result;
	}

	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		/*
		 * We do not need any code here since payments are processed on PayPal server.
		 */
	}

	public function subscribe_access_points()
	{
		return array(
			'api_pay_paypal_autoreturn'=>'process_paypal_autoreturn',
			'api_pay_paypal_ipn'=>'process_paypal_ipn'
		);
	}

	public function process_paypal_ipn($params)
	{
		try
		{
			$invoice = null;

			// Find invoice and load paypal settings
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
			
			if ($invoice->payment_type->class_name != 'Payment_PayPal_Standard_Gateway')
				throw new Phpr_ApplicationException('Invalid payment method');

			$endpoint = $invoice->payment_type->test_mode ?
				"www.sandbox.paypal.com/cgi-bin/webscr" :
				"www.paypal.com/cgi-bin/webscr";

			$fields = $_POST;
			if ($invoice->payment_type->test_mode)
			{
				foreach ($fields as $key => $value)
				{
					// Replace every \n that isn't part of \r\n with \r\n 
					// to prevent an invalid response from PayPal
					$fields[$key] = preg_replace("~(?<!\r)\n~","\r\n",$value);
				}
			}
			$fields['cmd'] = '_notify-validate';

			$response = $this->post_data($endpoint, $fields);

			if (!$invoice->is_payment_processed(true))
			{
				if (post('mc_gross') != $this->get_paypal_total($invoice))
					$this->log_payment_attempt($invoice, 'Invalid invoice total received in IPN: '.format_currency(post('mc_gross')), 0, array(), $_POST, $response);
				else
				{
					if (strpos($response, 'VERIFIED') !== false)
					{
						if ($invoice->mark_as_payment_processed())
						{
							$this->log_payment_attempt($invoice, 'Successful payment', 1, array(), $_POST, $response);
							Payment_Invoice_Log::create_record($invoice->payment_type->invoice_status, $invoice);
						}
					} else
						$this->log_payment_attempt($invoice, 'Invalid payment notification', 0, array(), $_POST, $response);
				}
			}
		}
		catch (Exception $ex)
		{
			if ($invoice)
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), $_POST, null);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}

	public function process_paypal_autoreturn($params)
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
			
			if ($invoice->payment_type->class_name != 'Payment_PayPal_Standard_Gateway')
				throw new Phpr_ApplicationException('Invalid payment method');

			// Send PayPal PDT request
			//

			if (!$invoice->is_payment_processed(true))
			{
				$transaction = Phpr::$request->get_field('tx');
				if (!$transaction)
					throw new Phpr_ApplicationException('Invalid transaction value');

				$endpoint = $invoice->payment_type->test_mode ?
					"www.sandbox.paypal.com/cgi-bin/webscr" :
					"www.paypal.com/cgi-bin/webscr";

				$fields = array(
					'cmd'=>'_notify-synch',
					'tx'=>$transaction,
					'at'=>$invoice->payment_type->pdt_token
				);

				$response = $this->post_data($endpoint, $fields);

				// Mark invoice as paid
				//

				if (strpos($response, 'SUCCESS') !== false)
				{
					$matches = array();

					if (!preg_match('/^invoice=([0-9]*)/m', $response, $matches))
						throw new Phpr_ApplicationException('Invalid response');

					if ($matches[1] != $invoice->id)
						throw new Phpr_ApplicationException('Invalid invoice number');

					if (!preg_match('/^mc_gross=([0-9\.]+)/m', $response, $matches))
						throw new Phpr_ApplicationException('Invalid response');

					if ($matches[1] != $this->get_paypal_total($invoice))
						throw new Phpr_ApplicationException('Invalid invoice total - invoice total received is '.$matches[1]);

					if ($invoice->mark_as_payment_processed())
					{
						$this->log_payment_attempt($invoice, 'Successful payment', 1, array(), Phpr::$request->get_fields, $response);
						Payment_Invoice_Log::create_record($invoice->payment_type->invoice_status, $invoice);
					}
				}
			}

			$google_tracking_code = 'utm_nooverride=1';
			$return_page = $invoice->get_receipt_url();
			if ($return_page)
				Phpr::$response->redirect($return_page.'?'.$google_tracking_code);
			else
				throw new Phpr_ApplicationException('PayPal Standard Receipt page is not found');

		}
		catch (Exception $ex)
		{
			if ($invoice)
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), Phpr::$request->get_fields, $response);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}

	private function post_data($endpoint, $fields)
	{
		$net = Net_Request::create('https://'.$endpoint);
		$net->disable_redirects();
		$net->set_timeout(30);
		$net->set_post($fields);

		$response = $net->send();
		$result = $response->data;
		return $result;
	}

	public function page_deletion_check($host, $page)
	{
		if ($host->cancel_page == $page->id)
			throw new Phpr_ApplicationException('Page cannot be deleted because it is used in PayPal Standard payment method as a cancel page');
	}

	public function status_deletion_check($host, $status)
	{
		if ($host->invoice_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in PayPal Standard payment method');
	}

	// Used to determine the invoice total as seen by PayPal
	private function get_paypal_total($invoice)
	{
		$invoice_total = 0;

		// Add up individual invoice items
		foreach ($invoice->items as $item)
		{
			$item_price = round($item->price, 2);
			$invoice_total = $invoice_total + ($item->quantity * $item_price);
		}

		// Invoice items tax
		$item_tax = round($invoice->tax, 2);
		$invoice_total = $invoice_total + $item_tax;

		return $invoice_total;
	}
	
}

