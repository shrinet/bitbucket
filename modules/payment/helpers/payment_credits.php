<?php

class Payment_Credits
{
	public static function create()
	{
		return new self();
	}

	public static function get_table()
	{
		return Payment_Config::get_credit_table();
	}

	public static function get_cost_for_credits($credit_amount)
	{
		$credit_table = self::get_table();
		foreach ($credit_table as $table)
		{
			if ($table->credit == $credit_amount)
				return $table->cost;
		}

		return false;
	}

	public static function add_credits($user_id, $credit_amount)
	{
		Db_Helper::scalar('update users set credits = ifnull(credits,0) + :amount where id = :id',
			array('id'=>$user_id, 'amount'=>(int)$credit_amount));
	}
}