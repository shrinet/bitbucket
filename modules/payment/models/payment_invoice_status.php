<?php

class Payment_Invoice_Status extends Db_ActiveRecord
{
	public $table_name = 'payment_invoice_statuses';
	public $enabled = true;

	protected static $code_cache = array();

	const status_new = 'new';
	const status_paid = 'paid';

	public $belongs_to = array(
		// 'user_message_template'=>array('class_name'=>'Email_Template', 'foreign_key'=>'user_message_template_id')
	);

	protected $api_added_columns = array();

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name')->order('asc')->validation()->fn('trim')->required("Please specify status name.");
		$this->define_column('notify_user', 'Notify user')->validation();
		$this->define_column('notify_recipient', 'Notify staff')->validation();

		// $this->define_relation_column('user_message_template', 'user_message_template', 'User Message Template', db_varchar, '@code')->validation()->method('validate_message_template');
		$this->define_column('code', 'API Code')->validation()->fn('trim')->unique('The code "%s" already in use.');

		$this->defined_column_list = array();
		Phpr::$events->fire_event('payment:on_extend_invoice_status_model', $this, $context);
		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('name')->tab('Invoice Status');

		if ($this->code != self::status_new && $this->code != self::status_paid)
			$this->add_form_field('code')->tab('Invoice Status')->comment('You can use the API code for identifying the status in API calls.', 'above');

		$this->add_form_field('notify_user')->tab('Invoice Status')->comment('Notify user when order gets into this state.');

		// $this->add_form_field('user_message_template')->tab('Invoice Status')
		//     ->display_as(frm_dropdown)->empty_option('<please select template>')->css_class_name('checkbox_align')
		//     ->comment('Please select an email message template to send to user. To manage email templates open <a target="_blank" href="'.url('email/templates').'">Email Templates</a> page.', 'above', true);

		$this->add_form_field('notify_recipient')->tab('Invoice Status')->comment('Notify staff when an invoice reaches this status.');

		Phpr::$events->fire_event('payment:on_extend_invoice_status_form', $this, $context);
	}

	public function before_delete($id = null)
	{
		if ($this->code == self::status_new || $this->code == self::status_paid)
			throw new Phpr_ApplicationException("Invoice status New and Paid cannot be deleted.");

		Payment_Type::invoice_status_deletion_check($this);
	}

	// Validation
	//

	public function validate_message_template($name, $value)
	{
		if (!$value && $this->notify_user)
			$this->validation->set_error('Please select email message template', $name, true);

		return true;
	}

	// Getters
	//

	public static function list_all_statuses()
	{
		$result = self::create();
		return $result->order('name asc')->find_all();
	}

	public static function get_status_new()
	{
		return self::get_by_code(self::status_new);
	}

	public static function get_status_paid()
	{
		return self::get_by_code(self::status_paid);
	}

	public static function find_id_from_code($code)
	{
		$status = self::get_by_code($code);
		return ($status) ? $status->id : null;
	}
	
	public static function get_by_code($code)
	{
		if (array_key_exists($code, self::$code_cache))
			return self::$code_cache[$code];

		$status = self::create()->find_by_code($code);

		return self::$code_cache[$code] = $status;
	}

}

