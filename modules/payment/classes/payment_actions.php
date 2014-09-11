<?php

class Payment_Actions extends Cms_Action_Base
{

	public function pay()
	{
		$this->data['invoice'] = $invoice = $this->_pay_find_invoice();

		if (!$invoice)
			return;
		
		$this->data['payment_type'] = $invoice->payment_type;
		// @deprecated
		$this->data['payment_type_obj'] = $invoice->payment_type;
	}
	
	public static function add_customer($user)
    {
        $payment_type = Payment_Type::get_default();
        $payment_type_obj = $payment_type->get_paymenttype_object();
        $payment_type_obj->add_customer($user, $payment_type);

    }

    public static function braintree_response()
    {
       $payment_type = Payment_Type::get_default();
       $payment_type_obj = $payment_type->get_paymenttype_object();
       $response = $payment_type_obj->braintree_response($payment_type,$_GET);
			 return $response;

    }

	public function on_pay($invoice = null)
    {
     	if (!$invoice)
        	$invoice = $this->_pay_find_invoice();
                                 
        if (!$invoice)
        	return;
                                 
       $payment_method_obj = $invoice->payment_type->get_paymenttype_object();
       $payment_method_obj->process_payment_form($_POST, $invoice->payment_type, $invoice);
 
       $return_page = $invoice->payment_type->receipt_page;
       if ($return_page)
       	Phpr::$response->redirect(root_url($return_page->url.'/'.$invoice->invoice_hash));
      }
	
	public function on_custom_event($param1, $param2, $param3)
{   
    // Parameters: first parameter second parameter third parameter
    echo 'Parameters: '.$param1.' '.$param2.' '.$param3;
}
	public function invoice()
	{
		$invoice = $this->_pay_find_invoice(false);
		
		if ($invoice->user_id != $this->user->id)
			$invoice = null;

		$this->data['invoice'] = $invoice;
	}

	public function on_update_payment_type()
	{
		$this->data['invoice'] = $invoice = $this->_pay_find_invoice();
		if (!$invoice)
			return;
		
		$invoice->payment_type_id = post('payment_type');
		$invoice->save();
		
		$invoice->payment_type = Payment_Type::create()->find($invoice->payment_type_id);
		$invoice->payment_type->init_form_fields();
		$this->data['payment_type'] = $invoice->payment_type;

		// @deprecated
		$this->data['payment_type_obj'] = $invoice->payment_type;
	}

	// Credits
	// 

	public function credits()
	{
		
	}

	public function on_buy_credits()
	{
		if (!$this->user)
			return;

		try 
		{
			$credit_num = post('credits', 1);
			$credit_cost = Payment_Credits::get_cost_for_credits($credit_num);
			$single_credit_cost = ($credit_cost / $credit_num);

			if (!$credit_cost)
				throw new Cms_Exception('Invalid credit amount');

			// Create invoice
			$invoice = Payment_Invoice::raise_invoice($this->user);
			$invoice_item = $invoice->add_line_item($credit_num, post('item_name', 'Credits'), $single_credit_cost);            
			$invoice->save();

			// Create promise to add credits
			$params = array($this->user->id, $credit_num);
			$promise = Payment_Fee_Promise::create_promise(Payment_Fee_Promise::mode_class_method, 'Payment_Credits::add_credits', $params, $this->user);
			$promise->invoice_id = $invoice->id;
			$promise->invoice_item_id = $invoice_item->id;
			$promise->save();

			if ($redirect = post('redirect'))
				Phpr::$response->redirect(str_replace('%s', $invoice->hash, $redirect));
		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}          
	}

	// Internal helpers
	// 

	private function _pay_find_invoice($hash_only=true)
	{
		$hash = trim($this->request_param(0));
		if (!strlen($hash))
			return null;
			
		$invoice = Payment_Invoice::create();

		if ($hash_only)
			$invoice = $invoice->find_by_hash($hash);
		else
			$invoice = $invoice->apply_hash_or_id($hash)->find();

		if (!$invoice)
			return null;

		if ($invoice->payment_type)
			$invoice->payment_type->init_form_fields();
		
		return $invoice;
	}
	
	public function on_request_release()
    {
        if (!post('escrow_id'))
            throw new Cms_Exception('Missing ID');
        $escrow = Payment_Escrow::create()->find(post('escrow_id'));
        if (!$escrow)
            throw new Cms_Exception(__('Sorry that request could not be found'));

        $is_provider = ($this->user->id == $escrow->provider_id);
        if(!$escrow->is_requested && $is_provider)
        {
            $escrow->set_requested();
            $escrow->save();
            return;
        }else{
            $escrow->request_release();
            $escrow->save;
        }

        return;
    }

    public static function request_release($escrow)
    {
        $payment_type = Payment_Type::get_default();
        $payment_type_obj = $payment_type->get_paymenttype_object();
        $payment_type_obj->request_release($escrow, $payment_type);

    }


}