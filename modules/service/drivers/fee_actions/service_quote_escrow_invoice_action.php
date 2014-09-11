<?php

    class Service_Quote_Escrow_Action extends Payment_Fee_Action_Base
    {
        public function get_info()
        {
            return array(
                'name' => 'Service Quote Escrow Invoice',
                'description' => 'Generate Escrow invoice for Quote amount'
            );
        }

        // Returns true if action is applicable to the selected event
        public function is_applicable($host)
        {
            if(!$host->event_obj)
                return true;

            if($host->event_obj instanceof Service_Quote_Status_Event)
                return true;

            return false;
        }

        public function build_config_form($host)
        {
            $host->add_field('service_percentage', 'Service Percentage', 'left', db_float, 'Action')
                ->comment('Please specify a percentage of the quote value as service fee', 'above')
                ->validation()->required('Please specify an amount');

            $host->add_field('target_user', 'Target user', 'right', db_varchar, 'Action')
                ->comment('Please specify who the fee should be applied to.', 'above')
                ->validation()->required('You must specify a target user');
            $host->add_field('pay_page', 'Payment Page', 'full', db_number, 'Action')->display_as(frm_dropdown)
                ->comment('Page the user will be redirected to send payment', 'above')->empty_option('<please select>')
                ->validation()->required('Please specify a payment page');

            $form_field = $host->find_form_field('target_user')->display_as(frm_dropdown);
        }

        public function get_target_user_options($host, $key_value = -1)
        {
            return array(
                'active_user' => 'Logged in user',
                'provider' => 'Provider',
                'customer' => 'Customer',
            );
        }

        public function get_pay_page_options($host, $key_value = -1)
        {
            return Cms_Page::create()->get_name_list();
        }


        public function trigger($host, $options, $params=array())
        {
            $quote = $options->quote;
            $type = Payment_Invoice::type_escrow;
            // Who to charge the fee
            switch ($host->target_user) {
                case 'active_user':
                    $controller = Cms_Controller::get_instance();
                    $user = $controller->user;
                    break;

                case 'provider':
                    $user = $quote->user;
                    break;

                case 'customer':
                    $user = $quote->request->user;
                    break;
            }

            // Generate invoice
            $invoice = Payment_Invoice::raise_invoice($user);

            // User not found
            if (!$user)
                $invoice->billing_email = post_array('User', 'email');

            // Add line item
            $fee_description = ($host->description) ? $host->description : $host->name;
            $invoice_item = $invoice->add_line_item(1, $fee_description, $quote->price);
            $invoice->set_invoice_type($type);

            $invoice->save();

            $escrow = Payment_Escrow::create_escrow($quote, $invoice, $this->calculate_service_fee($host,$quote));
            $escrow->set_status(Payment_Escrow::status_unpaid);
            $escrow->save();

            $params = array($quote->id, Service_Quote_Status::status_accepted);
            $promise = Payment_Fee_Promise::create_promise(Payment_Fee_Promise::mode_class_method, 'Service_Quote_Manager::set_quote_status_escrow', $params, $quote->request->user);
            $promise->invoice_id = $invoice->id;
            $promise->invoice_item_id = $invoice_item->id;
            $promise->save();


            // Redirect to pay page
            $pay_page = Cms_Page::create()->find($host->pay_page);
            Phpr::$response->redirect(root_url($pay_page->url.'/'.$invoice->hash));
        }

        private function calculate_service_fee($host, $quote)
        {
            return $quote->price * ((int)$host->service_percentage/100);
        }



    }