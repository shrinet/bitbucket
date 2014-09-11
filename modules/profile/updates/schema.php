<?php

$table = Db_Structure::table('profile_friends');
	$table->primary_key('id');
	$table->column('follower_id', db_number)->index();
	$table->column('leader_id', db_number)->index();
	$table->column('mutual', db_bool);
	$table->footprints(false);
	$table->column('deleted_at', db_datetime);
	$table->save();

$table = Db_Structure::table('profile_wall');
	$table->primary_key('id');
	$table->column('sender_id', db_number)->index();
	$table->column('user_id', db_number)->index();
	$table->column('message', db_text);
	$table->footprints(false);
	$table->save();
