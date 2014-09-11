<?php

class Service_Quote_Status extends Db_ActiveRecord
{
	const status_new = 'new'; 
	const status_accepted = 'accepted'; 
	const status_shortlist = 'shortlist'; 
	const status_eliminate = 'eliminate'; 
	const status_deleted = 'deleted'; 

	public $table_name = 'service_quote_statuses';
		
	public static function find_id_from_code($code)
	{
		$status = self::create()->find_by_code($code);
		return ($status) ? $status->id : null;
	}
}

