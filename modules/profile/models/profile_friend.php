<?php

class Profile_Friend extends Db_ActiveRecord
{

	public $table_name = 'profile_friends';

	public $belongs_to = array(
		'follower' => array('class_name' => 'User', 'foreign_key' => 'follower_id'),
		'leader' => array('class_name' => 'User', 'foreign_key' => 'leader_id')
	);


	public static function create()
	{
		return new self();
	}

	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
		$this->define_column('follower', 'Follower');
		$this->define_column('leader', 'Leader');
	}   

	// Gets friendships of a users friends
	public static function get_friend_friendship($user_id)
	{
		$friendship = self::create();
		$friendship->join('profile_friends as profile_friends_mine', 'profile_friends.follower_id=profile_friends_mine.leader_id OR profile_friends.leader_id=profile_friends_mine.leader_id');
		$friendship->where('profile_friends_mine.follower_id=?',$user_id);
		$friendship->where('profile_friends.leader_id!=?',$user_id);
		$friendship->where('profile_friends.follower_id!=?',$user_id);
		$friendship->where('profile_friends.deleted_at is null');
		$friendship->where('profile_friends_mine.deleted_at is null');
		$friendship->group('profile_friends.id');
		return $friendship;
	}   

}
