<?php

class Payment_Config extends Core_Settings_Base
{
	public $record_code = 'payment_config';
	
	private static $instance = null;

	public static function create()
	{
		if (self::$instance !== null)
			return self::$instance;

		$config = new self();
		return self::$instance = $config->load();
	}   
	
	protected function build_form()
	{
		$this->add_field('currency_code', 'Currency Code', 'left', db_varchar)->tab('General')
			->comment('International currency code, e.g. USD', 'above', true);
		$this->add_field('sign', 'Symbol', 'right', db_varchar)->comment('Sign to put beside number, e.g. $', 'above')->tab('General');

		$this->add_field('decimal_point', 'Decimal Point', 'left', db_varchar)->comment('Character to use as decimal point', 'above')->tab('General');
		$this->add_field('thousand_separator', 'Thousand Separator', 'right', db_varchar)->comment('Character to separate thousands', 'above')->tab('General');
		$this->add_field('sign_before', 'Place symbol before number', 'left', db_bool)->tab('General');

		$this->add_field('credit_table', 'Credit Costs', 'full', db_text)->tab('Credits')->display_as(frm_widget, array(
			'class'=>'Db_Grid_Widget', 
			'custom_model_class'=>'Payment_Config',
			'sortable'=>true,
			'scrollable'=>true,
			'enable_csv_operations'=>false,
			'scrollable_viewport_class'=>'height-200',
			'columns'=>array(
				'credit'=>array('title'=>'Credit Amount', 'type'=>'text'),
				'cost'=>array('title'=>'Price', 'type'=>'text', 'align'=>'right'),
			)
		));        
	}

	protected function init_config_data()
	{
		$this->currency_code = "USD";
		$this->sign_before = true;
		$this->sign = '$';
		$this->decimal_point = '.';
		$this->thousand_separator = ',';
		$this->credit_table = array(array('credit'=>1, 'cost'=>'2.50'));
	}

	public function is_configured()
	{
		$config = self::create();
		if (!$config)
			return false;

		return true;
	}

	public static function currency_symbol()
	{
		$obj = self::create();
		return $obj->sign;
	}

	public static function format_currency($num, $decimals = 2)
	{
		if (!strlen($num))
			return null;
		
		$obj = self::create();
		
		$negative = $num < 0;
		$neg_symbol = null;

		if ($negative)
		{
			$num *= -1;
			$neg_symbol = '-';
		}
		
		$num = number_format($num, $decimals, $obj->dec_point, $obj->thousands_sep);
		
		if ($obj->sign_before)
		{
			return $neg_symbol.$obj->sign.$num;
		}
		else
		{
			return $neg_symbol.$num.$obj->sign;
		}
	}

	public static function get_credit_table()
	{
		$table_array = self::create()->credit_table;
		$table_object = array();
		foreach ($table_array as $data)
		{
			$table_object[] = (object)$data;
		}
		return $table_object;
	}
}
