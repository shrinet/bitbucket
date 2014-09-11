<?php

class Payment_Fixed_Credit_Action extends Payment_Fee_Action_Base
{
    public function get_info()
    {
        return array(
            'name' => 'Require Credits',
            'description' => 'Require fixed amount of user credits.'
        );
    }

    public function build_config_form($host)
    {
        $host->add_field('fixed_amount', 'Credit amount', 'full', db_number, 'Action')
            ->comment('Please specify the credits required', 'above')
            ->validation()->required('Please specify the credits required');

        $host->add_field('buy_page', 'Credits Page', 'full', db_number, 'Action')->display_as(frm_dropdown)
            ->comment('Optional. When a user requires more credits, redirect them to buy credits', 'above')->empty_option('<do not redirect>')
            ->validation();
    }

    public function get_buy_page_options($host, $key_value = -1)
    {
        $page_obj = Cms_Page::create();
        $pages = $page_obj->get_name_list('payment:credits');
        
        if (empty($pages))
            $pages = $page_obj->get_name_list();

        return $pages;
    }

    public function trigger($host, $options, $params=array())
    {
        $controller = Cms_Controller::get_instance();
        $user = $controller->user;

        if (!$user)
            throw new Cms_Exception(__('You must be logged in to perform this action',true));

        $has_credits = $user->credits >= $host->fixed_amount;

        if (!$has_credits && $host->buy_page)
        {
            // Redirect to buy page
            $pay_page = Cms_Page::create()->find($host->buy_page);
            Phpr::$response->redirect(root_url($pay_page->url));            
        }
        
        if (!$has_credits)
            throw new Cms_Exception(__('Sorry, you require %s more credit(s) to perform this action', $host->fixed_amount-$user->credits, true));

        $user->credits -= $host->fixed_amount;
        $user->save();
    }

}