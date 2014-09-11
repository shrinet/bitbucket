<?php

class Payment_Invoice_Log extends Db_ActiveRecord
{
	public $implement = 'Db_AutoFootprints';
	public $auto_footprints_visible = false;

	public $table_name = 'payment_invoice_log';

	public $belongs_to = array(
		'status'=>array('class_name'=>'Payment_Invoice_Status', 'foreign_key'=>'status_id'),
		'invoice'=>array('class_name'=>'Payment_Invoice', 'foreign_key'=>'invoice_id')
	);

	public static function create_record($status_id, $invoice, $comment = null) 
	{ 
		// Nothing to do
		if ($invoice->status_id == $status_id)
			return false;

		// Extensibility
		$previous_status = $invoice->status_id;
		$result = Phpr::$events->fire_event('payment:on_invoice_before_update', $invoice, $status_id, $previous_status);
		
		if ($result === false)
			return false;

		// Create record
		$record = self::create();
		$record->status_id = $status_id;
		$record->invoice_id = $invoice->id;
		$record->comment = $comment;
		$record->save();

		// Update invoice status
		Db_Helper::query('update payment_invoices set status_id=:status_id, status_updated_at=:now where id=:id', array(
			'status_id'=>$status_id,
			'now'=>Phpr_Date::user_date(Phpr_DateTime::now()),
			'id'=>$invoice->id
		));

		$status_paid = Payment_Invoice_Status::get_status_paid();

		if (!$status_paid)
			return trace_log('Unable to find payment status with paid code');

		// @todo Send email notifications
		
		if ($status_id == $status_paid->id)
		{
			// Resolve any promises kept by this invoice
			// this may redirect so place last
			Payment_Fee_Promise::resolve_from_invoice($invoice);
		}
	}
}