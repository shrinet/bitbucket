<?php

class Payment_Invoice_Item extends Db_ActiveRecord
{
	public $table_name = 'payment_invoice_items';

	// NB: We only use a single tax class (id: 1) for the moment
	public $tax_class_id = 1;

	public $belongs_to = array(
		'invoice'=>array('class_name'=>'Payment_Invoice', 'foreign_key'=>'invoice_id'),
	);

	public function define_columns($context = null)
	{
		$this->define_relation_column('invoice', 'invoice', 'Invoice', db_varchar, '@id');
		$this->define_column('description', 'Description')->validation()->required();
		$this->define_column('tax_exempt', 'Tax Exempt');
		$this->define_column('quantity', 'Quantity')->validation()->required();

		// Single item price
		$this->define_column('price', 'Price')->currency(true);
		
		// Discount percentage
		$this->define_column('discount', 'Discount %')->currency(true)->default_invisible();

		// Quantified item price
		$this->define_column('subtotal', 'Subtotal')->currency(true);

		// Tax amount
		$this->define_column('tax', 'Tax');

		// Grande total (with tax)
		$this->define_column('total', 'Total')->currency(true);
	}

	public function define_form_fields($context = null)
	{       
		$this->add_form_field('description', 'left');
		$this->add_form_field('quantity', 'right');
		$this->add_form_field('total', 'left');
		$this->add_form_field('discount', 'right');
		$this->add_form_field('subtotal', 'left')->no_form();
		$this->add_form_field('tax_exempt')->comment('Tick this checkbox if the tax should not be applied to this invoice');
	}
	
	public function before_save($session_key = null)
	{
		$this->calculate_totals();
	}

	public function after_create()
	{
		Db_Helper::query('update payment_invoice_items set sort_order=:sort_order where id=:id', array(
			'sort_order'=>$this->id,
			'id'=>$this->id
		));
		$this->sort_order = $this->id;
	}

	public function calculate_totals()
	{
		if (!$this->invoice)
			return;

		$discount_amount = $this->price * $this->discount;
		$this->subtotal = ($this->price - $discount_amount) * $this->quantity;

		if (!$this->tax_exempt)
		{
			$this->tax = Payment_Tax::get_total_tax($this->tax_class_id, $this->subtotal, $this->invoice->get_location_info());
		}

		$this->total = $this->subtotal + $this->tax;
	}

}