<?php

class Payment_2Checkout_Gateway extends Payment_Type_Base
{

	public function get_info()
	{
		return array(
			'name'=>'2Checkout',
			'description'=>'2Checkout Payment Method using Credit Card or PayPal'
		);
	}

	public function build_config_ui($host, $context = null)
	{
		$host->add_field('demo_mode', 'Demo Mode')->tab('Configuration')->display_as(frm_onoffswitcher)->comment('2Checkout Demo Mode', 'above');
		$host->add_field('sid', 'Seller ID')->tab('Configuration')->display_as(frm_text)->comment('2Checkout Account Number.', 'above')->validation()->fn('trim')->required('Please provide your 2Checkout account number.');
		$host->add_field('secret_word', 'Secret Word')->tab('Configuration')->display_as(frm_text)->comment('2Checkout Secret Word.', 'above')->validation()->fn('trim')->required('Please provide your 2Checkout Secret Word.');
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
		return "https://www.2checkout.com/checkout/purchase";
	}

	public function get_hidden_fields($host, $invoice, $admin = false)
	{
		$result = array();

		/*
		 * Billing information
		 */

		$result['email'] = $invoice->billing_email;
		$result['card_holder_name'] = $invoice->billing_first_name.' '.$invoice->billing_last_name;

		$result['street_address'] = $invoice->billing_street_addr;
		$result['city'] = $invoice->billing_city;
		$result['country'] = $invoice->billing_country->code;

		if ($invoice->billing_state)
			$result['state'] = $invoice->billing_state->code;

		$result['zip'] = $invoice->billing_zip;
		$result['phone'] = $invoice->billing_phone;
		if ($invoice->shipping_country) {
			$result['ship_name'] = $invoice->billing_first_name.' '.$invoice->billing_last_name;
			$result['ship_street_address'] = $invoice->shipping_street_addr;
			$result['ship_city'] = $invoice->shipping_city;
			$result['ship_country'] = $invoice->shipping_country->code;

			if ($invoice->shipping_state)
				$result['ship_state'] = $invoice->shipping_state->code;

			$result['ship_zip'] = $invoice->shipping_zip;
		}

		/*
		 * Invoice items
		 */

		$item_index = 1;
		foreach ($invoice->items as $item)
		{
			$result['li_'.$item_index.'_type'] = 'product';
			$result['li_'.$item_index.'_name'] = $item->description;
			$result['li_'.$item_index.'_price'] = round($item->price, 2);
			$result['li_'.$item_index.'_quantity'] = $item->quantity;
			$item_index++;
		}

		/*
		 * Shipping
		 */

		$result['li_'.$item_index.'_type'] = 'shipping';
		$result['li_'.$item_index.'_name'] = 'Shipping Cost';
		$result['li_'.$item_index.'_price'] = $invoice->shipping_quote;

		$item_index++;
		if ($invoice->shipping_tax > 0)
		{
			$result['li_'.$item_index.'_type'] = 'tax';
			$result['li_'.$item_index.'_name'] = 'Shipping Tax';
			$result['li_'.$item_index.'_price'] = $invoice->shipping_tax;
		}

		$item_index++;
		if ($invoice->goods_tax > 0)
		{
			$result['li_'.$item_index.'_type'] = 'tax';
			$result['li_'.$item_index.'_name'] = 'Goods Tax';
			$result['li_'.$item_index.'_price'] = $invoice->goods_tax;
		}

		/*
		 * Payment setup
		 */

		if ($host->demo_mode)
		{
			$result['demo'] = "Y";
		}
		else
		{
			$result['demo'] = "N";
		}

		$result['mode'] = '2CO';
		$result['cart_invoice_id'] = $invoice->id;
		$result['merchant_order_id'] = $invoice->order_hash;
		$result['sid'] = $host->sid;
		$result['purchase_step'] = "payment-method";
		$result['currency_code'] = Payment_Config::create()->currency_code;

		$result['notify_url'] = Phpr::$request->get_root_url().root_url('api_pay_2checkout_ipn/'.$invoice->order_hash);

		if (!$admin)
		{
			$result['x_receipt_link_url'] = Phpr::$request->get_root_url().root_url('api_pay_2checkout_autoreturn/'.$invoice->order_hash);

			$cancel_page = Cms_Page::create()->find($host->cancel_page);
			if ($cancel_page)
			{
				$result['return_url'] = Phpr::$request->get_root_url().root_url($cancel_page->url);
				if ($cancel_page->action_reference == 'payment:pay')
					$result['return_url'] .= '/'.$invoice->hash;
				elseif ($cancel_page->action_reference == 'payment:invoice')
					$result['return_url'] .= '/'.$invoice->id;
			}
		} else
		{
			$result['x_receipt_link_url'] = Phpr::$request->get_root_url().root_url('/api_pay_2checkout_autoreturn/'.$invoice->order_hash.'/backend');
			$result['return_url'] = Phpr::$request->get_root_url().url('payment/pay/'.$invoice->id.'?'.uniqid());
		}

		foreach($result as $key=>$value)
		{
			$result[$key] = str_replace("\n", ' ', $value);
		}
		return $result;
	}

	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{
		/*
		 * We do not need any code here since payments are processed on 2Checkout server.
		 */
	}

	public function register_access_points()
	{
		return array(
			'api_pay_2checkout_autoreturn'=>'process_2checkout_autoreturn',
			'api_pay_2checkout_ipn'=>'process_2checkout_ipn'
		);
	}

	protected function get_cancel_page($host)
	{
		$cancel_page = $host->cancel_page;
		$page_info = Cms_PageReference::get_page_info($host, 'cancel_page', $host->cancel_page);
		if (is_object($page_info))
			$cancel_page = $page_info->page_id;

		if (!$cancel_page)
			return null;

		return Cms_Page::create()->find($cancel_page);
	}

	public function process_2checkout_ipn($params)
	{

	}

	public function process_2checkout_autoreturn($params)
	{
		$fields = $_REQUEST;

		try
		{
			$invoice = null;

			$response = null;

			/*
			 * Find order and load 2Checkout settings
			 */

			$invoice_hash = array_key_exists(0, $params) ? $params[0] : null;
			if (!$invoice_hash)
				throw new Phpr_ApplicationException('Invoice not found');

			$invoice = Payment_Invoice::create()->find_by_order_hash($invoice_hash);
			if (!$invoice)
				throw new Phpr_ApplicationException('Invoice not found.');

			if (!$invoice->payment_method)
				throw new Phpr_ApplicationException('Payment method not found.');

			$invoice->payment_method->init_form_fields();
			$payment_type_obj = $invoice->payment_method->get_paymenttype_object();

			if (!($payment_type_obj instanceof Payment_2Checkout_Gateway))
				throw new Phpr_ApplicationException('Invalid payment method.');

			$is_admin = array_key_exists(1, $params) ? $params[1] == 'backend' : false;

			/*
			 * Validate returned MD5 Hash
			 */

			if (!$invoice->payment_processed(false))
			{
				$transaction = $fields['order_number'];
				if (!$transaction)
					throw new Phpr_ApplicationException('Invalid transaction value');

				if ($invoice->payment_method->demo_mode)
				{
					$invoice_number = 1;
				}
				else
				{
					$invoice_number = $fields['order_number'];
				}

				$compare_hash = strtoupper(md5($invoice->payment_method->secret_word . $invoice->payment_method->sid . $invoice_number . $fields['total']));
				if ($compare_hash != $fields['key'])
					throw new Phpr_ApplicationException('MD5 Hash Failed to Validate.');

				/*
				 * Mark order as paid
				 */

					if ($fields['cart_invoice_id'] != $invoice->id)
						throw new Phpr_ApplicationException('Invalid invoice number.');

					if ($fields['total'] != strval($this->get_2checkout_total($invoice)))
						throw new Phpr_ApplicationException('Invalid order total - order total received is '.$fields['total']);

					if ($invoice->set_payment_processed())
					{
						Payment_Invoice_Log::create_record($invoice->payment_method->order_status, $invoice);
						$this->log_payment_attempt($invoice, 'Successful payment', 1, array(), Phpr::$request->get_fields, $response);
						$transaction_id = Phpr::$request->get_field('order_number');
						if(strlen($transaction_id))
							$this->update_transaction_status($invoice->payment_method, $invoice, $transaction_id, 'Processed', 'processed');
					}
			}

			if (!$is_admin)
			{
				$return_page = $invoice->payment_method->receipt_page;
				if ($return_page)
					Phpr::$response->redirect(root_url($return_page->url.'/'.$invoice->order_hash).'?utm_nooverride=1');
				else
					throw new Phpr_ApplicationException('2Checkout Receipt page is not found.');
			}
			else
			{
				Phpr::$response->redirect(url('/shop/orders/payment_accepted/'.$invoice->id.'?utm_nooverride=1&nocache'.uniqid()));
			}
		}
		catch (Exception $ex)
		{
			if ($invoice)
				$this->log_payment_attempt($invoice, $ex->getMessage(), 0, array(), Phpr::$request->get_fields, $response);

			throw new Phpr_ApplicationException($ex->getMessage());
		}
	}

	public function page_deletion_check($host, $page)
	{
		if ($host->cancel_page == $page->id)
			throw new Phpr_ApplicationException('Page cannot be deleted because it is used in 2Checkout payment method as a cancel page.');
	}

	public function status_deletion_check($host, $status)
	{
		if ($host->order_status == $status->id)
			throw new Phpr_ApplicationException('Status cannot be deleted because it is used in 2Checkout payment method.');
	}

	private function get_2checkout_total($invoice)
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
