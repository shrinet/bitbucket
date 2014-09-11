<?php

$table = Db_Structure::table('payment_types');
	$table->primary_key('id');
	$table->column('name', db_varchar);
	$table->column('description', db_text);
	$table->column('class_name', db_varchar, 100);
	$table->column('is_enabled', db_bool);
	$table->column('is_default', db_bool);
	$table->column('config_data', db_text);
	$table->column('code', db_varchar, 100)->index();

$table = Db_Structure::table('payment_type_countries');
	$table->primary_keys('payment_type_id', 'location_country_id');

$table = Db_Structure::table('payment_invoices');
	$table->primary_key('id');
	$table->column('user_id', db_number)->index();
	$table->column('user_ip', db_varchar, 15);
	
	$table->column('billing_first_name', db_varchar, 100);
	$table->column('billing_last_name', db_varchar, 100);
	$table->column('billing_email', db_varchar, 50);
	$table->column('billing_phone', db_varchar, 100);
	$table->column('billing_company', db_varchar, 100);
	$table->column('billing_street_addr', db_varchar);
	$table->column('billing_city', db_varchar, 100);
	$table->column('billing_zip', db_varchar, 20);
	$table->column('billing_state_id', db_number);
	$table->column('billing_country_id', db_number);
	
	$table->column('total', db_float, array(15, 2))->set_default('0.00');
	$table->column('subtotal', db_float, array(15, 2))->set_default('0.00');
	$table->column('discount', db_number)->set_default('0');
	$table->column('tax', db_float, array(15, 2))->set_default('0.00');
	$table->column('tax_discount', db_float, array(15, 2))->set_default('0.00');
	$table->column('tax_exempt', db_bool);
	$table->column('tax_data', db_text);

	$table->column('payment_type_id', db_number)->index();
	$table->column('payment_processed', db_datetime);
	
	$table->column('status_id', db_number)->index();
	$table->column('status_updated_at', db_datetime);

	$table->column('deleted_at', db_datetime);
	$table->column('sent_at', db_datetime);
	$table->column('due_at', db_datetime);
	$table->column('hash', db_varchar, 40)->index();

$table = Db_Structure::table('payment_invoice_items');
	$table->primary_key('id');
	$table->column('invoice_id', db_number)->index();
	$table->column('description', db_varchar);
	$table->column('quantity', db_number);
	$table->column('price', db_float, array(15, 2))->set_default('0.00');
	$table->column('total', db_float, array(15, 2))->set_default('0.00');
	$table->column('discount', db_number)->set_default('0');
	$table->column('subtotal', db_float, array(15, 2))->set_default('0.00');
	$table->column('tax_exempt', db_bool);
	$table->column('tax', db_float, array(15, 2))->set_default('0.00');
	$table->column('tax_discount', db_float, array(15, 2))->set_default('0.00');
	$table->column('sort_order', db_number);

$table = Db_Structure::table('payment_invoice_statuses');
	$table->primary_key('id');
	$table->column('code', db_varchar, 30)->index();
	$table->column('name', db_varchar);
	$table->column('notify_user', db_bool);
	$table->column('enabled', db_bool);
	$table->column('notify_recipient', db_bool);
	$table->column('user_message_template_id', db_number);

$table = Db_Structure::table('payment_fees');
	$table->primary_key('id');
	$table->column('date_start', db_date);
	$table->column('date_end', db_date);
	$table->column('name', db_varchar);
	$table->column('description', db_text);
	$table->column('enabled', db_bool);
	$table->column('sort_order', db_number);
	$table->column('event_class_name', db_varchar, 100);
	$table->column('action_class_name', db_varchar, 100);
	$table->column('config_data', db_text);

$table = Db_Structure::table('payment_fees_user_groups');
	$table->primary_keys('payment_fee_id', 'user_group_id');

$table = Db_Structure::table('payment_fee_promises');
	$table->primary_key('id');
	$table->column('command_name', db_varchar, 100);
	$table->column('param_data', db_text);
	$table->column('mode', db_varchar, 40);
	$table->column('user_id', db_number)->index();
	$table->column('fee_id', db_number)->index();
	$table->column('invoice_id', db_number)->index();
	$table->column('invoice_item_id', db_number)->index();
	$table->column('hash', db_varchar, 40)->index();
	$table->column('created_at', db_datetime);
	$table->column('deleted_at', db_datetime);

$table = Db_Structure::table('payment_taxes');
	$table->primary_key('id');
	$table->column('name', db_varchar);
	$table->column('description', db_text);
	$table->column('rates', 'mediumtext');
	$table->column('code', db_varchar, 30);

$table = Db_Structure::table('payment_invoice_log');
	$table->primary_key('id');
	$table->column('invoice_id', db_number)->index();
	$table->column('status_id', db_number)->index();
	$table->column('comment', db_text);
	$table->column('created_user_id', db_number)->index();
	$table->column('created_at', db_datetime);

$table = Db_Structure::table('payment_type_log');
	$table->primary_key('id');
	$table->column('invoice_id', db_number)->index();
	$table->column('payment_type_name', db_varchar);
	$table->column('is_success', db_bool);
	$table->column('message', db_varchar);
	$table->column('raw_response', db_text);
	$table->column('request_data', db_text);
	$table->column('response_data', db_text);

	$table->column('ccv_response_code', db_varchar, 20);
	$table->column('ccv_response_text', db_varchar);
	$table->column('avs_response_code', db_varchar, 20);
	$table->column('avs_response_text', db_varchar);

	$table->column('created_at', db_datetime)->index();
	$table->column('created_user_id', db_number)->index();
