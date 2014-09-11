<?php

class Payment_Fixed_Invoice_Action extends Payment_Fee_Action_Base
{
    public function get_info()
    {
        return array(
            'name' => 'Invoice a Fixed Amount',
            'description' => 'Invoice user for a fixed amount.'
        );
    }
    
    public function build_config_form($host)
    {
        $host->add_field('fixed_amount', 'Fixed amount', 'full', db_float, 'Action')
            ->comment('Please specify an amount to invoice this user', 'above')
            ->validation()->required('Please specify invoice amount');
    }

    public function trigger($host, $options, $params=array())
    {
        // Populate owner details
        $controller = Cms_Controller::get_instance();        

        // Generate invoice
        $invoice = Payment_Invoice::raise_invoice($controller->user);

        // User not logged in
        if (!$controller->user)
            $invoice->billing_email = post_array('User', 'email');

        // Add line item
        $fee_description = ($host->description) ? $host->description : $host->name;
        $invoice->add_line_item(1, $fee_description, $this->calculate_fee($quote));

        $invoice->save();
    }
}