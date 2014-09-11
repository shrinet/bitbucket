<?php

class Payment_Offline_Gateway extends Payment_Type_Base
{
	public function get_info()
	{
		return array(
			'name'=>'Offline Payment',
			'description'=>'Use this payment method for creating payment forms with offline payment processing',
		);
	}

	public function build_config_ui($host, $context = null)
	{
		$host->add_field('pre_invoice_status', 'Invoice Start Status')
			->display_as(frm_dropdown)->comment('Select a status to assign the invoice if this payment method was selected during checkout.', 'above')
			->empty_option('<default invoice status/do not change the invoice status>');
			
		$content_field = $host->add_field('payment_instructions', 'Payment Instructions', 'full', db_text)->display_as(frm_html)->size('huge');
		$content_field->html_plugins .= ',save,fullscreen,inlinepopups';
		$content_field->html_buttons1 = 'save,separator,'.$content_field->html_buttons1.',separator,fullscreen';
		$content_field->save_callback('save_code');
		$content_field->html_full_width = true;
	}

	public function get_pre_invoice_status_options($current_key_value = -1)
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
	}

	public function build_payment_form($host)
	{
	}

	// Payment processing
	// 
	
	public function get_payment_instructions($host, $invoice)
	{
		return $host->payment_instructions;
	}

	public function process_payment_form($data, $host, $invoice, $back_end = false)
	{

	}

	public function before_display_payment_form($host)
	{
	}

	public function invoice_after_create($host, $invoice)
	{
		if (!$host->pre_invoice_status)
			return;

		$status_new = Payment_InvoiceStatus::get_status_new();
		if ($status_new && $status_new->id == $host->pre_invoice_status)
			return;

		$invoice = Payment_Invoice::create()->find($invoice->id);

		Payment_Invoice_Log::create_record($host->pre_invoice_status, $invoice, null);
	}

	public function allow_new_invoice_notification($host, $invoice)
	{
		return !$host->suppress_new_notification;
	}
}

