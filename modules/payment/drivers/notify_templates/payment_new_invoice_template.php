<?php

class Payment_New_Invoice_Template extends Notify_Template_Base
{
    public $required_params = array('invoice');

    public function get_info()
    {
        return array(
            'name'=> 'New Customer Invoice',
            'description' => 'Sent to a user when an invoice is created for them.',
            'code' => 'payment:new_invoice'
        );
    }

    public function get_subject()
    {
        return 'Customer Invoice';
    }

    public function get_content()
    {
        return file_get_contents($this->get_partial_path('content.htm'));
    }

    public function prepare_template($template, $params=array())
    {
        extract($params);

        $invoice->set_notify_vars($template, 'invoice_');
        $template->set_vars(array());

        $user = ($invoice->user) 
            ? $invoice->user 
            : Notify::create_recipient($invoice->billing_name, $invoice->billing_email, $invoice->billing_phone);

        $template->add_recipient($user);
    }
}
