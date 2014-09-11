<?php

class Profile_Wall extends Db_ActiveRecord {

	public $table_name = 'profile_user_wall';
	
	public $belongs_to = array(
		'user' => array('class_name' => 'User', 'foreign_key' => 'user_id'),
		'sender' => array('class_name' => 'User', 'foreign_key' => 'sender_id')
	);

	public static function create()
	{
		return new self();
	}

	public function define_columns($context = null)
	{   
		$this->define_column('message', 'Message');
		$this->define_relation_column('user', 'user', 'User', db_varchar, '@username')->validation();
		$this->define_relation_column('sender', 'sender', 'Sender', db_varchar, '@username')->validation();
	}

	public static function get_friend_wall($user_id)
	{
		$wall = self::create();
		$wall->join('profile_friends', 'profile_wall.user_id=profile_friends.leader_id');
		$wall->where('profile_friends.follower_id=?',$user_id);
		$wall->where('profile_wall.sender_id!=?',$user_id);
		$wall->group('profile_wall.id');
		return $wall;
	}
}
