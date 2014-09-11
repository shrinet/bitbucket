<?php

$table = Db_Structure::table('bluebell_provider_zip');
	$table->primary_key('provider_id')->index();
	$table->primary_key('zip', db_varchar, 20)->index();

$table = Db_Structure::table('bluebell_directory_cities');
	$table->primary_key('id');
	$table->column('name', db_varchar, 100);
	$table->column('url_name', db_varchar, 100);
	$table->column('zip', db_varchar, 20)->index();
	$table->column('country_id', db_number)->index();
	$table->column('state_id', db_number)->index();
	$table->column('is_seed', db_bool);
	$table->column('is_processed', db_bool);
	$table->add_key('zip_country_city', array('zip', 'state_id', 'country_id', 'url_name'))->unique();
