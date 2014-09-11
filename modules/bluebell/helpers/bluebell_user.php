<?php

class Bluebell_User
{
	public static function avatar($user)
	{
		$photo = theme_url('assets/images/avatar_thumb.jpg');
		
		if ($user)
			$photo = $user->get_avatar('100', $photo);

		return $photo;
	}
}