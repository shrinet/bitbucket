<?php

class Payment_Fixed_Charge_Action extends Payment_Fee_Action_Base
{
    public function get_info()
    {
        return array(
            'name' => 'Require Payment',
            'description' => 'Require up front payment of fixed amount.'
        );
    }

    public function build_config_form($host)
    {
        $host->add_field('fixed_amount', 'Fixed amount', 'full', db_float, 'Action')
            ->comment('Please specify an amount required to proceed', 'above')
            ->validation()->required('Please specify an amount');

        $host->add_field('pay_page', 'Payment Page', 'full', db_number, 'Action')->display_as(frm_dropdown)
            ->comment('Page the user will be redirected to send payment', 'above')->empty_option('<please select>')
            ->validation()->required('Please specify a payment page');
    }

    public function get_pay_page_options($host, $key_value = -1)
    {
        return Cms_Page::create()->get_name_list();
    }

    public function trigger($host, $options, $params=array())
    {
        // Generate invoice
        $invoice = Payment_Invoice::raise_invoice();

        // Add line item
        $fee_description = ($host->description) ? $host->description : $host->name;
        $invoice_item = $invoice->add_line_item(1, $fee_description, $host->fixed_amount);
        
        // Populate owner details
        $controller = Cms_Controller::get_instance();
        if (!$controller->user)
        {
            $invoice->billing_email = post_array('User', 'email');
        }
        else
            $invoice->copy_from_user($controller->user);

        $invoice->save();

        // Create promise for line item
        $promise = Payment_Fee_Promise::create_promise(Payment_Fee_Promise::mode_ajax_action, $options->handler, $params);
        $promise->invoice_id = $invoice->id;
        $promise->invoice_item_id = $invoice_item->id;
        $promise->fee_id = $host->id;
        $promise->save();

        // Redirect to pay page
        $pay_page = Cms_Page::create()->find($host->pay_page);
        Phpr::$response->redirect(root_url($pay_page->url.'/'.$invoice->hash));
    }

}