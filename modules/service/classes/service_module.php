<?php

class Service_Module extends Core_Module_Base
{

	protected function set_module_info()
	{
		return new Core_Module_Detail(
			"Service",
			"Services Module",
			"Scripts Ahoy!",
			"http://scriptsahoy.com/"
		);
	}

	public function build_admin_menu($menu)
	{
		$top = $menu->add('service', 'Service', 'service/requests', 150)->icon('bell-alt')->permission(array('manage_requests', 'manage_providers', 'manage_categories'));
		$top->add_child('requests', 'Requests', 'service/requests', 100)->permission('manage_requests');
		$top->add_child('providers', 'Providers', 'service/providers', 100)->permission('manage_providers');
		$top->add_child('categories', 'Categories', 'service/categories', 200)->permission('manage_categories');
		$top->add_child('skills', 'Skills', 'service/skills', 200)->permission('manage_skills');
	}

	public function build_admin_settings($settings)
	{
		$settings->add('/service/setup', 'Service Settings', 'Service related settings', '/modules/service/assets/images/services_config.png', 300);
	}

	public function subscribe_events()
	{
		Phpr::$events->add_event('user:on_extend_user_model', $this, 'extend_user_model');
		Phpr::$events->add_event('service:on_after_update_provider', $this, 'user_banking_detail');
	}
	
	public function user_banking_detail($provider)
    {
        $user = $provider->user;

        Payment_Actions::add_customer($user);
    }

	// Events
	//

	public function extend_user_model($user)
	{
		// @todo fix: This relationship is NOT belongs_to, it is has_many
		//$user->add_relation('belongs_to', 'provider', array('class_name'=>'Service_Provider', 'primary_key'=>'user_id', 'foreign_key'=>'id'));
		//$user->define_relation_column('provider', 'provider', 'Provider ', db_varchar, 'users.first_name')->invisible();
	}

	// Crontab
	// 
	
	public function subscribe_crontab()
	{
		return array('expire_requests' => array('method'=>'cron_expire_requests', 'interval'=>5));
		return array('expire_plan' => array('method' => 'cron_expire_plan', 'interval' => 2));
	}

	public function cron_expire_requests()
	{
		$requests = Service_Request::create()
			->where('expired_at<=?', Phpr_DateTime::now()->to_sql_datetime())
			->apply_status(Service_Status::status_active);

		foreach ($requests->find_all() as $request)
		{
			Service_Request_Manager::expire_request($request);
		}
		
		return true;
	}
	 public function subscribe_access_points()
    {
        return array('braintree_hook'=>'get_braintree_hook');
    }

    public function get_braintree_hook($params)
    {
        #print_r($_GET);
        $response = Payment_Actions::braintree_response($_GET);
        echo $response;
    }
	
	public function cron_expire_plan()
    {
        $providers = Service_Provider::create()
            ->where('end_date<=?', Phpr_DateTime::now()->to_sql_datetime());
        foreach ($providers->find_all() as $provider )
        {
            Service_Provider_Manager::expire_provider($provider);
        }    
    }

	public function build_admin_permissions($host)
	{
		$host->add_permission_field($this, 'manage_requests', 'Manage requests', 'left')->display_as(frm_checkbox)->comment('Manage service requests');
		$host->add_permission_field($this, 'manage_providers', 'Manage providers', 'right')->display_as(frm_checkbox)->comment('Manage service providers');
		$host->add_permission_field($this, 'manage_categories', 'Manage categories', 'left')->display_as(frm_checkbox)->comment('Manage service categories');
	}

	public function build_user_preferences($host)
	{
		$host->add_form_section(null, 'General Notifications')->tab('Service');
		$host->add_form_section('Email')->tab('Service');
		$host->add_preference_field($this, 'email_job_booked', 'Job booked', true, 'left')->display_as(frm_checkbox)->comment("Request has been marked as booked");
		$host->add_preference_field($this, 'email_booking_reminder', 'Booking reminder', true, 'right')->display_as(frm_checkbox)->comment("Appointment reminder for scheduled request");
		$host->add_preference_field($this, 'email_job_complete', 'Job completed', true, 'left')->display_as(frm_checkbox)->comment("Request has been marked as completed");
		$host->add_preference_field($this, 'email_rating_submit', 'Rating submitted', true, 'right')->display_as(frm_checkbox)->comment("Other person has left a rating / review");
		$host->add_preference_field($this, 'email_job_cancel', 'Job cancelled', true, 'left')->display_as(frm_checkbox)->comment("Request has been marked as cancelled");

		$host->add_form_section('SMS')->tab('Service');
		$host->add_preference_field($this, 'sms_booking_reminder', 'Booking reminder', false, 'left')->display_as(frm_checkbox)->comment("Appointment reminder for scheduled request");
		$host->add_preference_field($this, 'sms_job_complete', 'Job completed', false, 'right')->display_as(frm_checkbox)->comment("Request has been marked as completed");
		$host->add_preference_field($this, 'sms_job_cancel', 'Job completed', true, 'left')->display_as(frm_checkbox)->comment("Request has been marked as cancelled");

		$host->add_form_section(null, 'Customer Notifications')->tab('Service');
		$host->add_form_section('Email')->tab('Service');
		$host->add_preference_field($this, 'email_request_submit', 'New request received', true, 'left')->display_as(frm_checkbox)->comment('This user submitted a new service request');
		$host->add_preference_field($this, 'email_request_question', 'Question about request', true, 'right')->display_as(frm_checkbox)->comment("Someone asks a question about user's request");
		$host->add_preference_field($this, 'email_request_quote', 'New quote for request', true, 'left')->display_as(frm_checkbox)->comment("A quote is submitted for user's request");
		$host->add_preference_field($this, 'email_request_expired', 'Request has expired', true, 'right')->display_as(frm_checkbox)->comment("Request has expired and it's time to select a provider");
		$host->add_preference_field($this, 'email_request_reminder', 'Reminder to select provider', true, 'left')->display_as(frm_checkbox)->comment("Request has 12 hours remaining to select a provider");

		$host->add_form_section('SMS')->tab('Service');
		$host->add_preference_field($this, 'sms_request_question', 'Question about request', false, 'left')->display_as(frm_checkbox)->comment("Someone asks a question about user's request");
		$host->add_preference_field($this, 'sms_request_expired', 'Request has expired', true, 'right')->display_as(frm_checkbox)->comment("Request has expired and it's time to select a provider");

		$host->add_form_section(null, 'Provider Notifications')->tab('Service');
		$host->add_form_section('Email')->tab('Service');
		$host->add_preference_field($this, 'email_job_offer', 'New job offer', true, 'left')->display_as(frm_checkbox)->comment('A request submitted that matches this provider');
		$host->add_preference_field($this, 'email_job_answer', 'Answer to provider question', true, 'right')->display_as(frm_checkbox)->comment("Customer has answered provider's question");
		$host->add_preference_field($this, 'email_job_answer_other', 'Answer to another provider question', true, 'left')->display_as(frm_checkbox)->comment("Customer has answered another provider's question");

		$host->add_form_section('SMS')->tab('Service');
		$host->add_preference_field($this, 'sms_job_offer', 'New job offer', true, 'left')->display_as(frm_checkbox)->comment('A request submitted that matches this provider');
		$host->add_preference_field($this, 'sms_job_answer', 'Answer to provider question', false, 'right')->display_as(frm_checkbox)->comment("Customer has answered provider's question");
		$host->add_preference_field($this, 'sms_job_answer_other', 'Answer to another provider question', false, 'left')->display_as(frm_checkbox)->comment("Customer has answered another provider's question");
	}

}
