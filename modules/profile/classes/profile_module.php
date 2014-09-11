<?php

class Profile_Module extends Core_Module_Base
{
	public static $_user;

	protected function set_module_info()
	{
		return new Core_Module_Detail(
			"User Profile",
			"Adds more robust feature to the User module",
			"PHPRoad",
			"http://phproad.com/"
		);
	}

	public function subscribe_events() 
	{
		Phpr::$events->add_event('user:on_extend_users_table', $this, 'extend_users_table');
		Phpr::$events->add_event('user:on_extend_user_model', $this, 'extend_user_model');
		Phpr::$events->add_event('user:on_extend_user_form', $this, 'extend_user_form');
		Phpr::$events->add_event('user:on_get_user_field_options', $this, 'get_user_field_options');
	}
	
	public function extend_users_table($table) 
	{
		$table->column('gender', db_varchar, 1);
		$table->column('dob', db_date);
		$table->column('phone', db_varchar, 100);
		$table->column('mobile', db_varchar, 100);
		$table->column('company', db_varchar, 100);
		$table->column('position', db_varchar, 100);
		$table->column('street_addr', db_varchar);
		$table->column('city', db_varchar, 100);
		$table->column('zip', db_varchar, 20);
		$table->column('state_id', db_number)->index();
		$table->column('country_id', db_number)->index();
	}
	
	public function extend_user_model($user, $context) 
	{
		$user->add_custom_columns(array(
			'total_friends' => db_number,
			'location_string' => db_number,
			'age' => db_number
		));

		$user->add_calculated_columns(array(
			'total_following' => array('sql'=>"select count(*) from profile_friends where profile_friends.follower_id=users.id and profile_friends.deleted_at is null", 'type'=>db_number),
			'total_followers' => array('sql'=>"select count(*) from profile_friends where profile_friends.leader_id=users.id and profile_friends.deleted_at is null", 'type'=>db_number),
			'total_mutual' =>  array('sql'=>"select round(count(*)/2) from profile_friends where (profile_friends.follower_id=users.id or profile_friends.leader_id=users.id) and profile_friends.mutual = 1", 'type'=>db_number),
			'address_string' => "trim(concat(
					if(users.street_addr is not null, concat(users.street_addr,', '), ' '),
					ifnull(users.city, ' '), ' ',
					ifnull(state_calculated_join.name, ' '), ' ',
					if(users.zip is not null, concat(users.zip, ', '), ' '), ' ',
					ifnull(country_calculated_join.name, ' ')
				))"
		));

		$user->add_relation('has_and_belongs_to_many', 'following', array('class_name'=>'User',  'join_primary_key' => 'follower_id', 'foreign_key' => 'leader_id', 'join_table'=>'profile_friends', 'order'=>'created_at', 'conditions'=>"profile_friends.deleted_at is null"));
		$user->add_relation('has_and_belongs_to_many', 'followers', array('class_name'=>'User',  'join_primary_key' => 'leader_id', 'foreign_key' => 'follower_id', 'join_table'=>'profile_friends', 'order'=>'created_at', 'conditions'=>"profile_friends.deleted_at is null"));

		$user->add_relation('belongs_to', 'country', array('class_name'=>'Location_Country', 'foreign_key'=>'country_id'));
		$user->add_relation('belongs_to', 'state', array('class_name'=>'Location_State', 'foreign_key'=>'state_id'));

		$user->define_column('phone', 'Phone Number')->default_invisible()->list_title('Phone')->validation()->fn('trim');
		$user->define_column('mobile', 'Mobile Number')->default_invisible()->list_title('Mobile')->validation()->fn('trim');
		$user->define_relation_column('country', 'country', 'Country ', db_varchar, '@name')->list_title('Country')->default_invisible()->validation();
		$user->define_relation_column('state', 'state', 'State ', db_varchar, '@name')->list_title('State')->default_invisible();
		$user->define_column('street_addr', 'Street Address')->default_invisible()->list_title('Address')->validation()->fn('trim');
		$user->define_column('city', 'City')->default_invisible()->list_title('City')->validation()->fn('trim');
		$user->define_column('zip', 'Zip/Postal Code')->default_invisible()->list_title('Zip/Postal Code')->validation()->fn('trim');

		$user->define_column('dob', 'Date of Birth')->default_invisible()->list_title('DOB')->validation()->fn('trim');
		$user->define_column('gender', 'Gender')->default_invisible()->list_title('Gender')->validation()->fn('trim');

		$user->add_relation('has_many', 'avatar', array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='User' and field='avatar'", 'order'=>'sort_order, id', 'delete'=>true));
		$user->define_multi_relation_column('avatar', 'avatar', 'Avatar', '@name')->invisible();
	}

	public function extend_user_form($user, $context) 
	{
		$user->add_form_field('dob','left')->tab('User');
		$user->add_form_field('gender','right')->tab('User')->display_as(frm_radio);

		$user->add_form_field('phone', 'left')->tab('Contact Details');
		$user->add_form_field('mobile', 'right')->tab('Contact Details');

		$user->add_form_field('street_addr')->tab('Contact Details')->nl2br(true);
		$user->add_form_field('city', 'left')->tab('Contact Details');
		$user->add_form_field('zip', 'right')->tab('Contact Details');
		$user->add_form_field('country', 'left')->tab('Contact Details')->empty_option('<please select>')->preview_no_relation();
		$user->add_form_field('state', 'right')->tab('Contact Details')->empty_option('<please select>')->preview_no_relation();

		$user->add_form_field('avatar')->display_as(frm_file_attachments)->display_files_as('single_image')->add_document_label('Upload avatar')->tab('Avatar')->no_attachments_label('Avatar is not uploaded')->image_thumb_size(100)->file_download_base_url(url('admin/files/get/'));
		self::$_user = $user;
	}

	public function get_user_field_options($field_name, $current_value) 
	{
		switch ($field_name) {
			case "gender": return array('M'=>'Male', 'F'=>'Female'); break;
			case "state": return Location_State::get_name_list(self::$_user->country_id); break;
			case "country": return Location_Country::get_name_list(); break;
		}
	}

}