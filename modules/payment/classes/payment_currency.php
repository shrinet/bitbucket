<?php

class Payment_Currency
{
	public static function create()
	{
		return new self();
	}

	public function convert($total, $code_from, $code_to)
	{
		return $total;
	}
}