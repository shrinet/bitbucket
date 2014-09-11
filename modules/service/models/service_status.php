<?php

class Service_Status extends Db_ActiveRecord
{
	const status_active = 'active';
	const status_draft = 'draft';
	const status_pending = 'pending';
	const status_expired = 'expired';
	const status_closed = 'closed';
	const status_cancelled = 'cancelled';
	const status_archived = 'archived';

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name');
		$this->define_column('code', 'API Code');
	}
	
	public function define_form_fields($context = null)
	{
		$this->add_form_field('name');
		$this->add_form_field('code');
	}

	public static function find_id_from_code($code)
	{
		$status = self::create()->find_by_code($code);
		return ($status) ? $status->id : null;
	}
}
