<?php

class Service_Provider_Manager 
{

	public static function set_provider_images($provider, $data, $files=array(), $session_key=null)
	{
		$result = array();
		$detect_upload = false;

		// Photo
		if (isset($files['provider_photo']))
		{
			$detect_upload = true;
			$post_post = $files['provider_photo'];
			
			$size = (isset($data['provider_photo_size'])) ? $data['provider_photo_size'] : '100';
			$size = Phpr_String::dimension_from_string($size);

			$file = $provider->save_attachment_from_post('photo', $post_post);
			$result = array(
				'id' => $file->id,
				'thumb'=> (($file->is_image()) ? $file->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop')) : null)
			);
		}

		// Logo
		if (isset($files['provider_logo']))
		{
			$detect_upload = true;
			$post_post = $files['provider_logo'];

			$size = (isset($data['provider_logo_size'])) ? $data['provider_logo_size'] : 'autox60';
			$size = Phpr_String::dimension_from_string($size);

			$file = $provider->save_attachment_from_post('logo', $post_post);
			$result = array(
				'id' => $file->id,
				'thumb'=> (($file->is_image()) ? $file->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop')) : null)
			);
		}

		// Portfolio
		if (isset($files['provider_portfolio']))
		{
			$detect_upload = true;
			$file_data = File_Upload::extract_multi_file_info($files['provider_portfolio']);
			
			$size = (isset($data['provider_portfolio_size'])) ? $data['provider_portfolio_size'] : '100x75';
			$size = Phpr_String::dimension_from_string($size);

			foreach ($file_data as $file)
			{
				$file = $provider->save_attachment_from_post('portfolio', $file);
			}

			$result = array(
				'id' => $file->id,
				'thumb'=> (($file->is_image()) ? $file->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop')) : null)
			);
		}

		if ($detect_upload) {            
			echo json_encode($result);
			die();
		}
	}

	public static function create_provider($user, $data, $files=array(), $session_key=null)
	{
		$provider = Service_Provider::create();
		$provider->init_columns();
		$provider->init_form_fields('preview');

		// Find orphaned logo @todo use deferred binding
		if (isset($data['provider_logo']))
		{
			$logo = Db_File::create()->where('master_object_id is null')->find($data['provider_logo']);
			if ($logo)
				$provider->logo->add($logo);
		}

		// Find orphaned photo @todo use deferred binding
		if (isset($data['provider_photo']))
		{
			$photo = Db_File::create()->where('master_object_id is null')->find($data['provider_photo']);
			if ($photo)
				$provider->photo->add($photo);
		}

		$provider->user = $user;
		//print_r($data);
		//die();
		$provider->save($data, $session_key);
		return $provider;
	}
	
	public static function expire_provider($provider, $session_key=null)
    {
        Service_Provider_Membership::renew_membership($provider);
    }

}