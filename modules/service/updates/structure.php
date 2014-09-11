<?php

$table = Db_Structure::table('service_providers');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('user_id', db_number)->index();
	$table->column('business_name', db_varchar);
	$table->column('url_name', db_varchar)->index(); // @todo rename: slug
	$table->column('description', db_text);
	$table->column('url', db_varchar); // @todo rename: website_url
	$table->column('established', db_varchar); // @todo rename: year_established
	$table->column('phone', db_varchar);
	$table->column('mobile', db_varchar);
	$table->column('street_addr', db_varchar);
	$table->column('city', db_varchar, 100);
	$table->column('zip', db_varchar, 20);
	$table->column('country_id', db_number)->index();
	$table->column('state_id', db_number)->index();
	$table->column('rating', db_number);
	$table->column('rating_all', db_number);
	$table->column('rating_num', db_float);
	$table->column('rating_all_num', db_float);
	$table->column('stat_offers', db_number)->set_default('0');
	$table->column('stat_quotes', db_number)->set_default('0');
	$table->column('stat_quote_average', db_float, array(15,2))->set_default('0.00');
	$table->column('stat_wins', db_number)->set_default('0');
	$table->column('stat_earned', db_float, array(15,2))->set_default('0.00');
	$table->column('config_data', db_text);
	$table->column('latitude', db_float, array(10,6));
	$table->column('longitude', db_float, array(10,6));
	$table->footprints();

$table = Db_Structure::table('service_provider_groups');
	$table->primary_key('id');
	$table->column('name', db_varchar);
	$table->column('code', db_varchar)->index();

$table = Db_Structure::table('service_provider_groups_providers');
	$table->primary_keys('provider_id', 'provider_group_id');
	$table->column('sort_order', db_number);

$table = Db_Structure::table('service_requests');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('type', db_varchar, 20);
	$table->column('user_id', db_number)->index();
	$table->column('status_id', db_number)->index();
	$table->column('title', db_varchar);
	$table->column('url_name', db_varchar)->index();
	$table->column('description', db_text);
	$table->column('description_extra', db_text);
	$table->column('city', db_varchar, 100);
	$table->column('zip', db_varchar, 20);
	$table->column('country_id', db_number);
	$table->column('state_id', db_number);
	$table->column('latitude', db_float, array(10,6));
	$table->column('longitude', db_float, array(10,6));  
	$table->column('config_data', db_text);
	$table->column('expired_at', db_datetime);
	$table->footprints();

$table = Db_Structure::table('service_requests_providers');
	$table->primary_keys('request_id', 'provider_id');
	$table->column('type', db_varchar, 20);
	$table->column('created_at', db_datetime);

$table = Db_Structure::table('service_categories');
	$table->primary_key('id');
	$table->column('name', db_varchar)->index();
	$table->column('url_name', db_varchar, 100)->index();
	$table->column('description', db_text);
	$table->column('keywords', db_text);
	$table->column('code', db_varchar)->index();
	$table->column('parent_id', db_number);
	$table->column('is_hidden', db_bool);
	$table->footprints();

$table = Db_Structure::table('service_categories_requests');
	$table->primary_keys('category_id', 'request_id');

$table = Db_Structure::table('service_categories_providers');
	$table->primary_keys('category_id', 'provider_id');

$table = Db_Structure::table('service_categories_categories');
	$table->primary_keys('category_id', 'related_category_id');

$table = Db_Structure::table('service_statuses');
	$table->primary_key('id');
	$table->column('code', db_varchar, 30)->index();
	$table->column('name', db_varchar, 50);

$table = Db_Structure::table('service_quotes');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('user_id', db_number)->index();
	$table->column('status_id', db_number)->index();
	$table->column('request_id', db_number)->index();
	$table->column('provider_id', db_number)->index();
	$table->column('comment', db_text);
	$table->column('price', db_float, array(15,2))->set_default('0.00');
	$table->column('start_at', db_datetime);
	$table->column('duration', db_number);
	$table->column('config_data', db_text);
	$table->column('deleted_at', db_datetime);
	$table->footprints();

$table = Db_Structure::table('service_quote_statuses');
	$table->primary_key('id');
	$table->column('code', db_varchar, 30)->index();
	$table->column('name', db_varchar, 50);

$table = Db_Structure::table('service_testimonials');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('provider_id', db_number)->index();
	$table->column('is_published', db_bool);
	$table->column('hash', db_varchar, 50);
	$table->column('name', db_varchar);
	$table->column('email', db_varchar);
	$table->column('location', db_varchar);
	$table->column('comment', db_text);
	$table->column('invite_subject', db_varchar);
	$table->column('invite_message', db_text);
	$table->footprints();

$table = Db_Structure::table('service_ratings');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('rating', db_number);
	$table->column('comment', db_text);
	$table->column('user_from_id', db_number)->index();
	$table->column('user_to_id', db_number)->index();
	$table->column('provider_id', db_number)->index();
	$table->column('request_id', db_number)->index();
	$table->footprints();

$table = Db_Structure::table('service_questions');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('description', db_text);
	$table->column('is_public', db_bool);
	$table->column('provider_id', db_number)->index();
	$table->column('answer_id', db_number)->index();
	$table->column('request_id', db_number)->index();
	$table->footprints();

$table = Db_Structure::table('service_answers');
	$table->primary_key('id');
	$table->column('moderation_status', db_varchar, 20)->index();
	$table->column('description', db_text);
	$table->column('is_public', db_bool);
	$table->column('user_id', db_number)->index();
	$table->column('request_id', db_number)->index();
	$table->footprints();

$table = Db_Structure::table('service_skills');
	$table->primary_key('id');
	$table->column('name', db_varchar)->index();
	$table->column('url_name', db_varchar, 100)->index();
	$table->column('description', db_text);
	$table->column('code', db_varchar)->index();
	$table->column('is_hidden', db_bool);
	$table->footprints();

$table = Db_Structure::table('service_skills_requests');
	$table->primary_keys('skill_id', 'request_id');
