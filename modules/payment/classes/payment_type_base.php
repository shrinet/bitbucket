<?php

/**
 * Represents the generic payment type.
 * All other payment types must be derived from this class
 */
class Payment_Type_Base extends Phpr_Extension
{
	/**
	 * Returns information about the payment type
	 * Must return array: 
	 * 
	 * array(
	 *      'name'=>'Authorize.net',
	 *      'description'=>'Authorize.net simple integration method with hosted payment form'
	 * )
	 * 
	 * Use custom_payment_form key to specify a name of a partial to use for building a admin
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
			'name' => 'Unknown',
			'description' => 'Unknown payment gateway'
		);
	}

	public function get_name() 
	{
		$info = $this->get_info();
		return (isset($info['name'])) ? $info['name'] : 'Unknown';
	}

	public function get_description() 
	{
		$info = $this->get_info();
		return (isset($info['description'])) ? $info['description'] : 'Unknown payment gateway';
	}

	/**
	 * Builds the payment type administration user interface
	 * For drop-down and radio fields you should also add methods returning
	 * options. For example, of you want to have Sizes drop-down:
	 * public function get_sizes_options();
	 * This method should return array with keys corresponding your option identifiers
	 * and values corresponding its titles.
	 *
	 * @param $host ActiveRecord object to add fields to
	 * @param string $context Form context. In preview mode its value is 'preview'
	 */
	public function build_config_ui($host, $context = null) { }

	/**
	 * Validates configuration data before it is saved to database
	 * Use host object field_error method to report about errors in data:
	 * $host->field_error('max_weight', 'Max weight should not be less than Min weight');
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function validate_config_on_save($host) { }

	/**
	 * Validates configuration data after it is loaded from database
	 * Use host object to access fields previously added with build_config_ui method.
	 * You can alter field values if you need
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function validate_config_on_load($host) { }

	/**
	 * Initializes configuration data when the payment method is first created
	 * Use host object to access and set fields previously added with build_config_ui method.
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function init_config_data($host) { }

	/**
	 * Builds the admin payment form
	 * For drop-down and radio fields you should also add methods returning
	 * options. For example, of you want to have Sizes drop-down:
	 * public function get_sizes_options();
	 * This method should return array with keys corresponding your option identifiers
	 * and values corresponding its titles.
	 *
	 * @param $host ActiveRecord object to add fields to
	 */
	public function build_payment_form($host) { }

	/**
	 * This function is called before an invoice status deletion.
	 * Use this method to check whether the payment method
	 * references an invoice status. If so, throw Phpr_ApplicationException
	 * with explanation why the status cannot be deleted.
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param Payment_InvoiceStatus $status Specifies a status to be deleted
	 */
	public function status_deletion_check($host, $status) { }

	/**
	 * Returns true if the payment type is applicable for a specified invoice amount
	 * @param float $amount Specifies an invoice amount
	 * @param $host ActiveRecord object to add fields to
	 * @return true
	 */
	public function is_applicable($amount, $host)
	{
		return true;
	}

	/**
	 * Processes payment using passed data
	 * @param array $data Posted payment form data
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 * @param $back_end Determines whether the function is called from the administration area
	 */
	public function process_payment_form($data, $host, $invoice, $back_end = false) { }

	/**
	 * This method is called than an invoice with this payment method is created
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 */
	public function invoice_after_create($host, $invoice) { }

	/**
	 * Adds a log record to the invoice payment attempts log
	 * @param mixed $invoice Invoice object the payment attempt is belongs to
	 * @param string $message Log message
	 * @param bool $is_successful Indicates that the attempt was successful
	 * @param array $request_array An array containing data posted to the payment gateway
	 * @param array $response_array An array containing data received from the payment gateway
	 * @param string $response_text Raw gateway response text
	 * @param string $ccv_response_code Card code verification response code
	 * @param string $ccv_response_text Card code verification response text
	 * @param string $avs_response_code Address verification response code
	 * @param string $avs_response_text Address verification response text
	 */
	protected function log_payment_attempt($invoice, $message, $is_successful, $request_array, $response_array, $response_text, $ccv_response_code = null, $ccv_response_text = null, $avs_response_code = null, $avs_response_text = null)
	{
		$info = $this->get_info();

		$record = Payment_Type_Log::create();
		$record->message = $message;
		$record->invoice_id = $invoice->id;
		$record->payment_type_name = $info['name'];
		$record->is_success = $is_successful;
		
		$record->raw_response = $response_text;
		$record->request_data = $request_array;
		$record->response_data = $response_array;

		$record->ccv_response_code = $ccv_response_code;
		$record->ccv_response_text = $ccv_response_text;
		$record->avs_response_code = $avs_response_code;
		$record->avs_response_text = $avs_response_text;

		$record->save();
	}
	
	protected function log_braintree_action( $response)
    {
        $record = Payment_Server_Log::create();
        $record->response_data = $response;
        $record->save();
    }

	

	/**
	 * Registers a hidden page with specific URL. Use this method for cases when you
	 * need to have a hidden landing page for a specific payment gateway. For example,
	 * PayPal needs a landing page for the auto-return feature.
	 * Important! Payment module access point names should have the api_ prefix.
	 * @return array Returns an array containing page URLs and methods to call for each URL:
	 * return array('api_pay_paypal_autoreturn'=>'process_paypal_autoreturn'). The processing methods must be declared
	 * in the payment type class. Processing methods must accept one parameter - an array of URL segments
	 * following the access point. For example, if URL is /api_pay_paypal_autoreturn/1234 an array with single
	 * value '1234' will be passed to process_paypal_autoreturn method
	 */
	public function subscribe_access_points()
	{
		return array();
	}

	/**
	 * This method returns true for non-offline payment types
	 */
	public function has_payment_form()
	{
		$info = $this->get_info();
		return array_key_exists('offline', $info) && $info['offline'] ? false : true;
	}

	/**
	 * This method is called before the payment form is rendered
	 * @param $host ActiveRecord object containing configuration fields values
	 */
	public function before_display_payment_form($host) { }

	/**
	 * This method should return FALSE to suppress the New Invoice Notification for new invoices
	 * with this payment method assigned
	 * @param $host ActiveRecord object containing configuration fields values
	 * @param $invoice Invoice object
	 */
	public function allow_new_invoice_notification($host, $invoice)
	{
		return true;
	}
}

