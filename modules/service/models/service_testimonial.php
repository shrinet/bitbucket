<?php

class Service_Testimonial extends Db_ActiveRecord
{
	public $table_name = 'service_testimonials';

	public $belongs_to = array(
		'provider' => array('class_name' => 'Service_Provider', 'foreign_key' => 'provider_id'),
	);
	
	public function define_columns($context = null)
	{
		$this->define_column('id', '#');
		$this->define_column('created_at', 'Created');
		$this->define_column('updated_at', 'Last Updated');
		$this->define_column('is_published', 'Published');
		$this->define_column('name', 'Name');
		$this->define_column('email', 'Email');
		$this->define_column('location', 'Location');
		$this->define_column('invite_message', 'Invite Message');
		$this->define_column('comment', 'Comments');

		$this->define_relation_column('provider', 'provider', 'Provider', db_number, '@id')->validation();
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('name','left');
		$this->add_form_field('is_published','right')->comment('Tick if the testimonial should appear publically.', 'above');
		$this->add_form_field('location','left');
		$this->add_form_field('email','right');
		$this->add_form_field('invite_message')->size('small')->comment('The message sent when requesting this testimonial');
		$this->add_form_field('comment');
	}

	public static function create_testimonial($provider, $email=null, $subject=null, $message=null)
	{
		if (!$email)
			return;

		if (!$subject)
			$subject = __('Add a testimonial for me on %s', c('site_name'));

		if (!$message)
			$message = __('Would you add a brief recommendation of my work for my %s profile? Please let me know if you have any questions and thanks for your help!', c('site_name'));

		$testimonial = self::create();
		$testimonial->provider_id = $provider->id;
		$testimonial->provider = $provider;
		$testimonial->hash = md5($email.$provider->id.time());
		$testimonial->is_published = false;
		$testimonial->email = $email;
		$testimonial->invite_subject = $subject;
		$testimonial->invite_message = $message;
		$testimonial->save();

		return $testimonial;
	}

}