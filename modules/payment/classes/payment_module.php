<?php

class Payment_Module extends Core_Module_Base
{
	protected function set_module_info()
	{
		return new Core_Module_Detail(
			"Payment",
			"Payment gateways",
			"Responsiv Internet",
			"http://responsiv.com/"
		);
	}

	public function build_admin_menu($menu)
	{
		$top = $menu->add('payment', 'Payments', 'payment/invoices', 700)->icon('credit-card')->permission('manage_fees', 'manage_types', 'manage_invoices', 'manage_taxes');
		$top->add_child('fees', 'Fees', 'payment/fees', 100)->permission('manage_fees');
		$top->add_child('types', 'Gateways', 'payment/types', 100)->permission('manage_types');
		$top->add_child('invoices', 'Invoices', 'payment/invoices', 100)->permission('manage_invoices');
		$top->add_child('taxes', 'Tax Table', 'payment/taxes', 100)->permission('manage_taxes');
        $top->add_child('escrows', 'Escrow', 'payment/escrows', 100)->permission('manage_escrows');
	}

	public function build_admin_settings($settings)
	{        
		$settings->add('/payment/setup', 'Payment Settings', 'Set up currency settings', '/modules/payment/assets/images/payment_config.png', 200);
	}

	public function subscribe_events()
	{
		Phpr::$events->add_event('user:on_extend_users_table', $this, 'extend_users_table');
		Phpr::$events->add_event('user:on_extend_user_model', $this, 'extend_user_model');
		Phpr::$events->add_event('user:on_extend_user_form', $this, 'extend_user_form');
		Phpr::$events->add_event('user:on_user_created', $this, 'on_user_created');
	}

	public function subscribe_crontab()
	{
		return array('bind_invoices' => array('method' => 'cron_bind_invoices', 'interval' => 120)); // 2 Hours
		return array('clean_promises' => array('method' => 'cron_clean_promises', 'interval' => 720)); // Daily
	}

	public function subscribe_access_points($action = null)
	{
		$points = array();
		if (substr($action, 0, 8) == 'api_pay_')
		{
			$payment_types = Payment_Type_Manager::get_payment_type_class_names();
			foreach ($payment_types as $class_name)
			{
				$gateway = new $class_name();
				$gateway_points = $gateway->subscribe_access_points();

				if (!is_array($gateway_points))
					continue;

				foreach ($gateway_points as $url => $method)
				{
					$gateway_points[$url] = $class_name.'::'.$method;
				}

				$points = array_merge($points, $gateway_points);
			}
		}
		return $points;
	}    

	// Events
	// 

	public function cron_bind_invoices()
	{
		// TODO: Use this process to locate invoices without user_ids
		// match by billing_email
		return true;
	}

	public function cron_clean_promises()
	{
		// TODO: Locate deleted promises and old promises and remove from DB
		return true;
	}

	// Extend user
	//
	
	public function extend_users_table($table) 
	{
		$table->column('credits', db_number)->set_default(0);
	}

	public function extend_user_model($user, $context)
	{
		$user->define_column('credits', 'Credits')->default_invisible()->validation();
	}

	public function extend_user_form($user, $context)
	{
		$user->add_form_field('credits', 'left')->tab('User')->comment('Used as currency for Fee Payments');
	}

	public function on_user_created($user)
	{
		Db_Helper::query('update users set credits=0 where id=?', $user->id);
	}

	public function build_admin_permissions($host)
	{
		$host->add_permission_field($this, 'manage_fees', 'Manage fees', 'left')->display_as(frm_checkbox)->comment('Manage payment fees');
		$host->add_permission_field($this, 'manage_types', 'Manage types', 'right')->display_as(frm_checkbox)->comment('Manage payment types');
		$host->add_permission_field($this, 'manage_invoices', 'Manage invoices', 'left')->display_as(frm_checkbox)->comment('Manage payment invoices');
		$host->add_permission_field($this, 'manage_taxes', 'Manage taxes', 'right')->display_as(frm_checkbox)->comment('Manage payment taxes');
        $host->add_permission_field($this, 'manage_escrows', 'Manage Escrows', 'right')->display_as(frm_checkbox)->comment('Manage payment escrows');
	}

}

