<?php

class Profile_User extends Phpr_Extension 
{
	
	private $model;
	
	public function __construct($model) 
	{
		parent::__construct();
		
		$this->model = $model;
	}

	public function eval_total_friends() 
	{
		return $this->model->total_following + $this->model->total_followers - $this->model->total_mutual;
	}

	public function eval_age() 
	{
		$age = null;

		if ($dob = $this->model->dob)
		{
			if (!$dob instanceof Phpr_DateTime)
				$dob = new Phpr_Datetime($dob);

			$days = Phpr_DateTime::now()->substract_datetime($dob)->get_days();
			$age = floor($days / 365);
		}
		return $age;
	}

	public function eval_location_string() 
	{
		$str = '';
		
		if ($this->model->city)
			$str .= $this->model->city . ', ';
		else if ($this->model->zip)
			$str .= $this->model->zip . ' ';
		
		$str .= ($this->model->state) ? $this->model->state->code : '';

		if (!$this->model->city && !$this->model->zip)
			$str .= ($this->model->country) ? ', ' . $this->model->country->code : '';

		if (!strlen($str))
			return null;

		return $str;
	}

	// Service methods
	// 

	public function get_avatar($size='100', $default=null)
	{
		$size = Phpr_String::dimension_from_string($size);

		if ($this->model->avatar->count > 0) {
			return $this->model->avatar[0]->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop'));			
		}
		else {

			if (substr($default, 0, 2) != '//' && strtolower(substr($default, 0, 4)) != 'http') {
				if ($default{0} == '/')
					$default = substr($default, 1);

				$default = Phpr::$request->get_root_url() . '/' . $default;
			}

			return "//www.gravatar.com/avatar/"
				. md5(strtolower(trim($this->model->email)))
				. "?d=".urlencode($default)
				. "&s=".$size['width'];
		}
	}

	public function delete_avatar()
	{
		if ($this->model->avatar->count > 0)
		{
			foreach ($this->model->avatar as $file)
			{
				$this->model->avatar->delete($file);
			}
			$this->model->save();
		}
	}  

	// Points and XP
	// 

	public function refresh_stats() 
	{
		$this->model->xp_level = Profile_Points::xp_get_level($this->model->xp_points);
		$this->model->xp_progress = Profile_Points::xp_get_progress($this->model->xp_points);        
	}

	// Friendship
	// 

	public function is_following($user_id, $return=false) 
	{
		$friendship = Profile_Friend::create()->where('follower_id=?', $this->model->id)->where('leader_id=?', $user_id)->where('deleted_at is null');
		return ($return) ? $friendship->find() : $friendship->get_row_count();
	}   

	public function befriend($user, $unfollow=false) 
	{
		$friendship = Profile_Friend::create()->where('follower_id=?', $this->model->id)->where('leader_id=?', $user->id)->find();
		if (!$friendship)
			$friendship = Profile_Friend::create();

		$mutual = $user->chef->is_following($this->model->id, true);
		if ($mutual) {
			$friendship->mutual = $mutual->mutual = !($unfollow);
			$mutual->save();
		}

		$friendship->follower_id = $this->model->id;
		$friendship->leader_id = $user->id;
		$friendship->deleted_at = ($unfollow) ? Phpr_DateTime::now() : null;
		$friendship->save();

		return $friendship;
	}

	public function get_friends() 
	{
		$friends = User::create()->join('profile_friends', 'users.id=leader_id OR users.id=follower_id');
		$friends->where('profile_friends.deleted_at is null');
		$friends->where('(users.id=leader_id AND follower_id=:id) OR (users.id=follower_id AND leader_id=:id)', array('id'=>$this->model->id));
		$friends->where('users.id!=?',$this->model->id);
		$friends->group('users.id');
		return $friends;
	}

	public function get_followers() 
	{
		$friends = User::create()->join('profile_friends', 'users.id=follower_id');
		$friends->where('profile_friends.deleted_at is null');
		$friends->where('profile_friends.leader_id=?', $this->model->id);
		$friends->where('users.id!=?', $this->model->id);     
		return $friends;
	}

	public function get_following() 
	{
		$friends = User::create()->join('profile_friends', 'users.id=leader_id');
		$friends->where('profile_friends.deleted_at is null');
		$friends->where('profile_friends.follower_id=?', $this->model->id);
		$friends->where('users.id!=?', $this->model->id);     
		return $friends;
	}    

}