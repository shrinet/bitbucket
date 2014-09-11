<?php

class Payment_Invoice extends Db_ActiveRecord
{
	public $table_name = 'payment_invoices';
	
	const type_membership = 'membership';
    const type_escrow = 'escrow';
	const type_other = 'other';


	public $belongs_to = array(
		'user'=>array('class_name'=>'User', 'foreign_key'=>'user_id'),
		'status'=>array('class_name'=>'Payment_Invoice_Status', 'foreign_key'=>'status_id'),
		'payment_type'=>array('class_name'=>'Payment_Type', 'foreign_key'=>'payment_type_id'),
		'billing_country'=>array('class_name'=>'Location_Country', 'foreign_key'=>'billing_country_id'),
		'billing_state'=>array('class_name'=>'Location_State', 'foreign_key'=>'billing_state_id'),
	);

	public $has_many = array(
		'items'=>array('class_name'=>'Payment_Invoice_Item', 'foreign_key'=>'invoice_id', 'delete'=>true, 'order'=>'payment_invoice_items.id'),
		//'status_log'=>array('class_name'=>'Payment_Invoice_Log', 'foreign_key'=>'invoice_id', 'order'=>'payment_invoice_log.created_at desc', 'delete'=>true),
		//'payment_log'=>array('class_name'=>'Payment_Type_Log', 'foreign_key'=>'invoice_id', 'order'=>'payment_log.created_at desc', 'delete'=>true),
	);

	public $calculated_columns = array(
		'billing_name' => "trim(concat(ifnull(billing_first_name, ''), ' ', ifnull(billing_last_name, ' ')))"
	);

	protected $added_line_items = array();

	public function define_columns($context = null)
	{
		//$this->define_multi_relation_column('status_log', 'status_log', 'Status Log', "@status_id")->invisible();
		$this->define_multi_relation_column('items', 'items', 'Items', "@id")->invisible()->validation();

		$this->define_column('id', '#')->order('desc');

		$this->define_relation_column('user', 'user', 'User', db_varchar, "concat(@first_name, ' ', @last_name, ' (', @email, ')')")->default_invisible();
		$this->define_column('user_ip', 'User IP')->default_invisible();
		$this->define_column('type', 'Type')->default_invisible()->validation();
		$this->define_column('billing_first_name', 'First Name')->default_invisible()->validation()->fn('trim')->required();
		$this->define_column('billing_last_name', 'Last Name')->default_invisible()->validation()->fn('trim')->required();
		$this->define_column('billing_email', 'Email')->validation()->fn('trim')->required()->email();
		$this->define_column('billing_phone', 'Phone')->default_invisible()->validation()->fn('trim');
		$this->define_column('billing_company', 'Company')->default_invisible()->validation()->fn('trim');
		$this->define_column('billing_street_addr', 'Street Address')->default_invisible()->validation()->fn('trim')->required();
		$this->define_column('billing_city', 'City')->default_invisible()->validation()->fn('trim')->required();
		$this->define_column('billing_zip', 'Zip/Postal Code')->default_invisible()->validation()->fn('trim')->required();
		$this->define_relation_column('billing_country', 'billing_country', 'Country ', db_varchar, '@name')->default_invisible()->validation()->required();
		$this->define_relation_column('billing_state', 'billing_state', 'State ', db_varchar, '@name')->default_invisible();

		$this->define_column('total', 'Total')->currency(true);
		$this->define_column('discount', 'Discount')->currency(true)->default_invisible();
		$this->define_column('subtotal', 'Subtotal')->currency(true);
		$this->define_column('tax', 'Sales Tax')->currency(true)->default_invisible();
		$this->define_column('tax_discount', 'Tax Discount')->currency(true)->default_invisible();
		$this->define_column('tax_exempt', 'Tax Exempt')->default_invisible();

		$this->define_column('payment_processed', 'Payment Processed')->default_invisible()->date_format('%x %H:%M');
		$this->define_relation_column('payment_type', 'payment_type', 'Payment Gateway', db_varchar, '@name')->default_invisible()->validation()->required('Please select payment gateway.');

		$this->define_relation_column('status', 'status', 'Status', db_varchar, '@name');
		$this->define_column('status_updated_at', 'Status Updated')->default_invisible()->date_format('%x %H:%M');

		$this->define_column('sent_at', 'Sent Date')->date_format('%x %H:%M');
		$this->define_column('due_at', 'Due Date')->date_format('%x %H:%M')->default_invisible();
		$this->define_column('deleted_at', 'Deleted')->invisible()->date_format('%x %H:%M');
	}

	public function define_form_fields($context = null)
	{
		if ($context != "preview")
		{
			$this->add_form_field('user','left')
				->display_as(frm_record_finder, array(
					'sorting'=>'first_name, last_name, email',
					'list_columns'=>'first_name,last_name,email,guest,created_at',
					'search_prompt'=>'Find user by name or email',
					'form_title'=>'Find User',
					'display_name_field'=>'name',
					'display_description_field'=>'email',
					'prompt'=>'Click the Find button to find a user'))->tab('Billing Details');
		}

		//$this->add_form_field('items')->tab('Invoice');

		$this->add_form_field('sent_at', 'left')->tab('Invoice Details')->no_form();
		$this->add_form_field('status', 'right')->tab('Invoice Details')->preview_no_relation()->no_form();
		$this->add_form_field('type','left')->tab('Invoice Details')->display_as(frm_radio);
		$this->add_form_field('user_ip')->tab('Invoice Details')->no_form();

		$this->add_form_field('billing_first_name', 'left')->tab('Billing Details');
		$this->add_form_field('billing_last_name', 'right')->tab('Billing Details');
		$this->add_form_field('billing_email')->tab('Billing Details');
		$this->add_form_field('billing_company', 'left')->tab('Billing Details');
		$this->add_form_field('billing_phone', 'right')->tab('Billing Details');
		$this->add_form_field('billing_country', 'left')->tab('Billing Details');
		$this->add_form_field('billing_state', 'right')->tab('Billing Details');
		$this->add_form_field('billing_street_addr')->tab('Billing Details')->nl2br(true);
		$this->add_form_field('billing_city', 'left')->tab('Billing Details');
		$this->add_form_field('billing_zip', 'right')->tab('Billing Details');

		$this->add_form_field('total', 'left')->tab('Invoice Details')->no_form();
		$this->add_form_field('tax', 'right')->tab('Invoice Details')->no_form();
		$this->add_form_field('subtotal', 'left')->tab('Invoice Details')->no_form();
		$this->add_form_field('discount', 'right')->tab('Invoice Details')->no_form();

		if ($context != "preview")
		{
			$this->add_form_field('payment_type', 'left')->tab('Invoice Details');
			$this->add_form_field('tax_exempt', 'right')->tab('Invoice Details')->comment('Tick this checkbox if the tax should not be applied to this invoice');

			// Line Items
			$this->add_form_field('items')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('description', 'quantity', 'total'),
				'no_data_message' => 'This invoice has no items',
				'control_panel' => 'item_control_panel',
				'is_editable' => true,
				'form_title' => 'Line Item',
				'form_context' => 'create'
			))->tab('Invoice Details');  

		}
		else
		{
			if ($this->tax_exempt)
				$this->add_form_field('tax_exempt')->tab('Invoice Details');
		}
	}

	// Events
	// 

	public function before_save($session_key = null) 
	{
		$this->set_defaults();

		// Calc totals
		// 
		$items = ($this->is_new_record()) ? new Db_Data_Collection($this->added_line_items) : $this->items;
		$this->calculate_totals($items);
	}

	public function before_create($session_key = null)
	{
		if (!$this->sent_at)
			$this->sent_at = Phpr_DateTime::now();
		
		$this->user_ip = Phpr::$request->get_user_ip();
		$this->hash = $this->create_hash();
		while (Db_Helper::scalar('select count(*) from payment_invoices where hash=:hash', array('hash'=>$this->hash)))
		{
			$this->hash = $this->create_hash();
		}
	}

	public function after_create_saved()
	{
		foreach ($this->added_line_items as $item)
		{
			$item->invoice_id = $this->id;
			$item->save();
		}

		$payment_type = $this->payment_type;
		if ($payment_type)
		{
			$payment_type->init_form_fields();
			$payment_type->invoice_after_create($payment_type, $this);
		}

		// Send notification
		Notify::trigger('payment:new_invoice', array('invoice'=>$this));
	}
	
	public function get_type_options($key_value = -1)
	{
		$options = array(
			self::type_other => 'Other',
			self::type_membership => 'Membership',
			self::type_escrow => 'Escrow'
		);

		if ($key_value == -1)
			return $options;
		else if (array_key_exists($key_value, $options))
			return $options[$key_value];
		else
			return '???';
	}

	public function before_update($session_key = null) 
	{
		Phpr::$events->fire_event('payment:on_invoice_before_update', $this, $session_key);
	}
	
	public function after_fetch()
	{
		if (!$this->payment_type)
			$this->set_payment_type(Payment_Type::get_default());
	}

	// Options
	//

	public function get_billing_state_options($key_value = -1)
	{
		return Location_State::get_name_list($this->billing_country_id);
	}

	public function get_billing_country_options($key_value = -1)
	{
		return Location_Country::get_name_list();
	}

	// Service methods
	// 

	public static function raise_invoice($user=null, $payment_type=null, $options=array())
	{
		$invoice = self::create();
		
		if (!$payment_type)
			$payment_type = Payment_Type::get_default();

		$invoice->set_payment_type($payment_type);

		if ($user)
			$invoice->copy_from_user($user);

		$invoice->status_updated_at = Phpr_DateTime::now();

		return $invoice;
	}
    
    // Where invoice categorised
	public function apply_type()
	{
		$this->where('type=:other OR type=:membership OR type=:escrow', array(
			'other' => self::type_other,
            'escrow' => self::type_escrow,
			'membership' => self::type_membership
		));
		return $this;
	}
    
    public function set_invoice_type($invoice_type)
    {
        if(!$invoice_type)
            return $this;
        $this->type = $invoice_type;
        return $this;    
    }

	public function set_payment_type($payment_type)
	{
		if (!$payment_type)
			return $this;
		
		$this->payment_type = $payment_type;
		$this->payment_type_id = $payment_type->id;
		return $this;
	}

	public function set_defaults()
	{
		if (!$this->status_id) $this->set_status(Payment_Invoice_Status::status_new);
		if (!$this->billing_country_id) $this->billing_country_id = Location_Config::create()->default_country;
		if (!$this->billing_state_id) $this->billing_state_id = Location_Config::create()->default_state;
	}

	public function set_status($status_code)
	{
		$this->status_updated_at = Phpr_DateTime::now();
		$this->status_id = Payment_Invoice_Status::find_id_from_code($status_code);
	}

	public function add_line_item($quantity, $description, $price)
	{
		$item = Payment_Invoice_Item::create();
		$item->description = $description;
		$item->quantity = $quantity;
		$item->price = $price;
		$item->invoice = $this;
		$item->calculate_totals();

		if ($this->is_new_record())
			$this->added_line_items[] = $item;
		else
		{
			$item->invoice_id = $this->id;
			$item->save();
		}
		return $item;
	}


	public function copy_from_user($user)
	{
		$this->user_id = $user->id;
		$this->billing_first_name = $user->first_name;
		$this->billing_last_name = $user->last_name;
		$this->billing_email = $user->email;
		$this->billing_company = $user->company;
		$this->billing_phone = $user->phone;
		$this->billing_country = $user->country;
		$this->billing_state = $user->state;
		$this->billing_street_addr = $user->street_addr;
		$this->billing_city = $user->city;
		$this->billing_zip = $user->zip;
	}


	public function set_notify_vars(&$template, $prefix='')
	{
		$invoice_items = array();
		foreach ($this->items as $item)
		{
			$invoice_items[] = array(
				'item_description' => $item->description,
				'item_quantity'    => $item->quantity,
				'item_total'       => format_currency($item->total),
				'item_discount'    => format_currency($item->discount),
				'item_subtotal'    => format_currency($item->subtotal),
			);
		}

		$tax_items = array();
		foreach ($this->list_item_taxes() as $tax)
		{
			$tax_items[] = array(
				'tax_name'  => $tax->name,
				'tax_total' => format_currency($tax->total),
			);
		}

		$template->set_vars(array(
			$prefix.'id'                  => $this->id,
			$prefix.'billing_first_name'  => $this->billing_first_name,
			$prefix.'billing_last_name'   => $this->billing_last_name,
			$prefix.'billing_email'       => $this->billing_email,
			$prefix.'billing_company'     => $this->billing_company,
			$prefix.'billing_phone'       => $this->billing_phone,
			$prefix.'billing_country'     => ($this->billing_country) ? $this->billing_country->name : null,
			$prefix.'billing_state'       => ($this->billing_state) ? $this->billing_state->name : null,
			$prefix.'billing_street_addr' => $this->billing_street_addr,
			$prefix.'billing_city'        => $this->billing_city,
			$prefix.'billing_zip'         => $this->billing_zip,
			$prefix.'total'               => format_currency($this->total),
			$prefix.'tax'                 => format_currency($this->tax),
			$prefix.'subtotal'            => format_currency($this->subtotal),
			$prefix.'discount'            => format_currency($this->discount),
			$prefix.'sent_at'             => Phpr_DateTime::format_safe($this->sent_at, '%x'),
			$prefix.'due_at'              => Phpr_DateTime::format_safe($this->due_at, '%x'),
			$prefix.'deleted_at'          => Phpr_DateTime::format_safe($this->deleted_at, '%x'),
			$prefix.'payment_processed'   => Phpr_DateTime::format_safe($this->payment_processed, '%x'),
			$prefix.'status_updated_at'   => Phpr_DateTime::format_safe($this->status_updated_at  , '%x'),
			$prefix.'items'               => $invoice_items,
			$prefix.'tax_items'           => $tax_items,
			$prefix.'link'                => $this->get_pay_url(null, true),
			$prefix.'url'                 => $this->get_pay_url(null, true),
		));
	}

	// Filters
	// 

	public function apply_hash_or_id($id)
	{
		$this->where('payment_invoices.hash=:id or payment_invoices.id=:id', array('id'=>$id));
		return $this;
	}

	// Getters
	// 

	public function get_receipt_url($page=null, $add_hostname=false)
	{
		if (!$page) 
			$page = Cms_Page::get_url_from_action('payment:invoice');

		if (!$page)
			return false;

		return root_url($page.'/'.$this->hash, $add_hostname);
	}

	public function get_pay_url($page=null, $add_hostname=false)
	{
		if (!$page) 
			$page = Cms_Page::get_url_from_action('payment:pay');

		if (!$page)
			return false;

		return root_url($page.'/'.$this->hash, $add_hostname);
	}

	public function get_location_info()
	{
		$this->set_defaults();
		$location = array(
			'street_addr' => $this->billing_street_addr,
			'city' => $this->billing_city,
			'zip' => $this->billing_zip,
			'state_id' => $this->billing_state_id,
			'country_id' => $this->billing_country_id,
		);
		return (object)$location;
	}

	// Payment processing
	// 

	// Returns true if this action was successful
	public function mark_as_payment_processed()
	{
		$is_paid = $this->is_payment_processed(true);
		if (!$is_paid)
		{
			$now = $this->payment_processed = Phpr_DateTime::now();

			// Instant update here in case a simultaneous request causes invalid data
			Db_Helper::query('update payment_invoices set payment_processed=:payment_processed where id=:id', array('id'=>$this->id, 'payment_processed'=>$now));

			$this->save();
		}
		return !$is_paid;
	}

	public function is_payment_processed($reset_cache = false)
	{
		if ($reset_cache)
			return Db_Helper::scalar('select payment_processed from payment_invoices where id=:id', array('id'=>$this->id));
			
		return $this->payment_processed;
	}

	// Tax methods
	// 

	public function list_item_taxes()
	{
		$result = array();

		if (!strlen($this->tax_data))
			return $result;
			
		try
		{
			$taxes = unserialize($this->tax_data);
			foreach ($taxes as $tax_name=>$tax_info)
			{
				if ($tax_info->total > 0)
					$this->add_tax_item($result, $tax_name, $tax_info->total, 0, 'Sales tax');
			}
		}
		catch (Exception $ex) 
		{
			return $result;
		}
		
		return $result;
	}

	protected function add_tax_item(&$list, $name, $amount, $discount, $default_name = 'tax')
	{
		if (!$name)
			$name = $default_name;
		
		if (!array_key_exists($name, $list))
		{
			$tax_info = array('name'=>$name, 'amount'=>0, 'discount'=>0, 'total'=>0);
			$list[$name] = (object)$tax_info;
		}
		
		$list[$name]->amount += $amount;
		$list[$name]->discount += $discount;
		$list[$name]->total += ($amount - $discount);
	}

	public function set_sales_taxes($taxes)
	{
		if (!is_array($taxes))
			$taxes = array();
		
		$taxes_to_save = $taxes;

		foreach ($taxes_to_save as $tax_name=>&$tax_info)
		{
			$tax_info->total = round($tax_info->total, 2);
		}
		$this->tax_data = serialize($taxes_to_save);
	}

	// Helpers
	// 

	protected function create_hash()
	{
		return md5(uniqid('invoice', microtime()));
	}

	public function calculate_totals($items=null)
	{
		if (!$items)
			$items = $this->items;

		// Discount and subtotal
		$discount = 0;
		$subtotal = 0;
		foreach ($items as $item)
		{
			$subtotal += $item->subtotal;
			$discount += $item->discount;
		}

		// Calculate tax
		$tax_info = Payment_Tax::calculate_taxes($items, $this->get_location_info(), true);
		$this->set_sales_taxes($tax_info->taxes);
		$tax = $tax_info->tax_total;

		// Grand total
		$this->discount = $discount;
		$this->subtotal = $subtotal;
		$this->tax = $tax;
		$this->total = ($subtotal - $discount) + $tax;

		return $this->total;
	}
}
