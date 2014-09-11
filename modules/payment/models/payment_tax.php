<?php

class Payment_Tax extends Db_ActiveRecord
{
	public $table_name = 'payment_taxes';
	
	protected static $tax_class_cache = array();
	protected static $_tax_exempt = false;

	public function define_columns($context = null)
	{
		$this->define_column('name', 'Name')->order('asc')->validation()->fn('trim')->required();
		$this->define_column('description', 'Description')->validation()->fn('trim');
		$this->define_column('rates', 'Rates')->invisible()->validation()->required();
	}

	public function define_form_fields($context = null)
	{
		// $this->add_form_field('name')->tab('Tax Class');
		// $this->add_form_field('description')->comment('Description is optional.', 'above')->size('small')->tab('Tax Class');

		$this->add_form_field('rates')->display_as(frm_widget, array(
			'class'=>'Db_Grid_Widget', 
			'sortable'=>true,
			'scrollable'=>true,
			'enable_csv_operations'=>true,
			'scrollable_viewport_class'=>'height-300',
			'csv_file_name'=>'tax-table',
			'columns'=>array(
				'country'=>array('title'=>'Country Code', 'type'=>'text', 'autocomplete'=>'remote', 'autocomplete_custom_values'=>true, 'min_length'=>0, 'width'=>'100'),
				'state'=>array('title'=>'State Code', 'type'=>'text', 'width'=>'100', 'autocomplete'=>'remote', 'autocomplete_custom_values'=>true, 'min_length'=>0),
				'zip'=>array('title'=>'ZIP', 'type'=>'text', 'width'=>'100'),
				'city'=>array('title'=>'City', 'type'=>'text'),
				'rate'=>array('title'=>'Rate, %', 'type'=>'text', 'width'=>'60', 'align'=>'right'),
				'priority'=>array('title'=>'Priority', 'type'=>'text', 'width'=>'80', 'align'=>'right'),
				'tax_name'=>array('title'=>'Tax Name', 'type'=>'text', 'width'=>'80'),
				'compound'=>array('title'=>'Compound', 'type'=>'checkbox', 'width'=>'80')
			)
		));
	}
	
	public function get_grid_autocomplete_values($db_name, $column, $term, $row_data)
	{
		if ($column == 'country')
			return $this->get_country_list($term);

		if ($column == 'state')
		{
			$country_code = isset($row_data['country']) ? $row_data['country'] : null;
			return $this->get_state_list($country_code, $term);
		}
	}

	// Options
	// 

	protected function get_country_list($term)
	{
		$countries = Db_Helper::object_array('select code, name from location_countries where name like :term', array('term'=>$term.'%'));
		$result = array();
		$result['*'] = '* - Any country';
		foreach ($countries as $country)
			$result[$country->code] = $country->code.' - '.$country->name;

		return $result;
	}
	
	protected function get_state_list($country_code, $term)
	{
		$result = array('*'=>'* - Any state');

		$states = Db_Helper::object_array('select location_states.code as state_code, location_states.name
			from location_states, location_countries 
			where location_states.country_id = location_countries.id
			and location_countries.code=:country_code
			and location_states.name like :term
			order by location_countries.code, location_states.name', array(
			'country_code'=>$country_code,
			'term'=>$term.'%'
		));

		foreach ($states as $state)
			$result[$state->state_code] = $state->state_code.' - '.$state->name;

		return $result;
	}
	
	// Events
	// 

	public function before_delete($id = null)
	{
		throw new Phpr_ApplicationException('Cannot delete the primary tax class');
	}
	
	public function before_save($session_key = null)
	{
		$this->validate_rates();

		$this->rates = serialize($this->rates);
	}
	
	protected function after_fetch()
	{
		$this->rates = strlen($this->rates) ? unserialize($this->rates) : array();
	}
	
	// Getters
	// 

	public static function find_by_id($id)
	{
		if (isset(self::$tax_class_cache[$id]))
			return self::$tax_class_cache[$id];
		
		return self::$tax_class_cache[$id] = self::create()->find($id);
	}

	protected function get_rate($location_info, $priorities_to_ignore = array())
	{
		$country = Location_Country::find_by_id($location_info->country_id);
		if (!$country)
			return null;

		$state = null;
		if (strlen($location_info->state_id))
			$state = Location_State::find_by_id($location_info->state_id);

		$country_code = $country->code;
		$state_code = $state ? mb_strtoupper($state->code) : '*';

		$zip_code = str_replace(' ', '', trim(strtoupper($location_info->zip)));
		if (!strlen($zip_code))
			$zip_code = '*';

		$city = str_replace('-', '', str_replace(' ', '', trim(mb_strtoupper($location_info->city))));
		if (!strlen($city))
			$city = '*';

		$rate = null;
		foreach ($this->rates as $row)
		{
			$tax_priority = isset($row['priority']) ? $row['priority'] : 1;
			if (in_array($tax_priority, $priorities_to_ignore))
				continue;

			if ($row['country'] != $country_code && $row['country'] != '*')
				continue;

			if (mb_strtoupper($row['state']) != $state_code && $row['state'] != '*')
				continue;

			$row_zip = isset($row['zip']) && strlen($row['zip']) ? str_replace(' ', '', $row['zip']) : '*';
			if ($row_zip != $zip_code && $row_zip != '*')
				continue;

			$row_city = isset($row['city']) && strlen($row['city']) ? str_replace('-', '', str_replace(' ', '', mb_strtoupper($row['city']))) : '*';
			if ($row_city != $city && $row_city != '*')
				continue;

			$compound = isset($row['compound']) ? $row['compound'] : 0;

			if (preg_match('/^[0-9]+$/', $compound))
				$compound = (int)$compound;
			else
				$compound = $compound == 'Y' || $compound == 'YES';

			$rate_obj = array(
				'rate'=>$row['rate'],
				'priority'=>$tax_priority,
				'name'=>isset($row['tax_name']) ? $row['tax_name'] : 'TAX',
				'compound'=>$compound
			);
			 
			$rate = (object)$rate_obj;
			break;
		}
		
		return $rate;
	}

	public function get_tax_information($location_info)
	{
		$max_tax_num = 2;
		$priorities_to_ignore = array();
		$added_taxes = array();
		$compound_taxes = array();
		$result = array();

		for ($index = 1; $index <= $max_tax_num; $index++)
		{
			$tax_info = $this->get_rate($location_info, $priorities_to_ignore);
			if (!$tax_info)
				break;

			if (!$tax_info->compound)
				$added_taxes[] = $tax_info;
			else
				$compound_taxes[] = $tax_info;

			$priorities_to_ignore[] = $tax_info->priority;
		}
		
		foreach ($added_taxes as $added_tax)
			$result[] = $added_tax;

		foreach ($compound_taxes as $compound_tax)
			$result[] = $compound_tax;
			
		return $result;
	}

	/**
	 * Returns tax rates for a specified amount
	 * @return return array of applicable taxes
	 */
	public function get_tax_rates($amount, $location_info)
	{
		$max_tax_num = 2;
		$priorities_to_ignore = array();
		$added_taxes = array();
		$compound_taxes = array();
		for ($index = 1; $index <= $max_tax_num; $index++)
		{
			$tax_info = $this->get_rate($location_info, $priorities_to_ignore);
			if (!$tax_info)
				break;

			if (!$tax_info->compound)
				$added_taxes[] = $tax_info;
			else
				$compound_taxes[] = $tax_info;

			$priorities_to_ignore[] = $tax_info->priority;
		}

		$added_result = $amount;
		$result = array();
		foreach ($added_taxes as $added_tax)
		{
			$tax_info = array();
			$tax_info['name'] = $added_tax->name;
			$tax_info['tax_rate'] = $added_tax->rate/100;
			$added_result += $tax_info['rate'] = round($amount*$added_tax->rate/100, 2);
			$tax_info['total'] = $tax_info['rate'];
			$tax_info['added_tax'] = true;
			$tax_info['compound_tax'] = false;

			$result[] = (object)$tax_info;
		}

		foreach ($compound_taxes as $compound_tax)
		{
			$tax_info = array();
			$tax_info['name'] = $compound_tax->name;
			$tax_info['tax_rate'] = $compound_tax->rate/100;
			$tax_info['rate'] = round($added_result*$compound_tax->rate/100, 2);
			$tax_info['total'] = $tax_info['rate'];
			$tax_info['compound_tax'] = true;
			$tax_info['added_tax'] = false;

			$result[] = (object)$tax_info;
		}

		return $result;
	}

	public static function get_tax_rates_static($tax_class_id, $location_info)
	{
		if (!array_key_exists($tax_class_id, self::$tax_class_cache))
			self::$tax_class_cache[$tax_class_id] = self::create()->find($tax_class_id);

		 return self::$tax_class_cache[$tax_class_id]->get_tax_rates(1, $location_info);
	}

	protected static function find_added_tax($tax_list)
	{
		foreach ($tax_list as $tax)
		{
			if ($tax->added_tax)
				return $tax;
		}
		
		return null;
	}

	public static function get_default_class_id()
	{
		return Db_Helper::scalar('select id from payment_taxes where is_default=1');
	}

	/**
	 * Returns total tax value for a specific tax class and amount
	 */
	public static function get_total_tax($tax_class_id, $amount, $location_info)
	{
		if (!array_key_exists($tax_class_id, self::$tax_class_cache))
			self::$tax_class_cache[$tax_class_id] = self::create()->find($tax_class_id);
			
		$tax_class = self::$tax_class_cache[$tax_class_id];
			
		if (!$tax_class)
			return 0;

		$taxes = $tax_class->get_tax_rates($amount, $location_info);

		$result = 0;
		foreach ($taxes as $tax)
			$result += $tax->tax_rate*$amount;

		return $result;
	}

	/**
	 * Returns subtotal value for specified total amount and tax class
	 */
	public static function get_subtotal($tax_class_id, $total, $location_info)
	{
		if (!array_key_exists($tax_class_id, self::$tax_class_cache))
			self::$tax_class_cache[$tax_class_id] = self::create()->find($tax_class_id);
			
		$tax_class = self::$tax_class_cache[$tax_class_id];
		
		$max_tax_num = 2;
		$priorities_to_ignore = array();
		$added_taxes = array();
		$compound_taxes = array();
		for ($index = 1; $index <= $max_tax_num; $index++)
		{
			$tax_info = $tax_class->get_rate($location_info, $priorities_to_ignore);
			if (!$tax_info)
				break;

			if (!$tax_info->compound)
				$added_taxes[] = $tax_info;
			else
				$compound_taxes[] = $tax_info;

			$priorities_to_ignore[] = $tax_info->priority;
		}
		
		/*
		 * No applicable taxes case
		 */
		if (!$added_taxes && !$compound_taxes)
			return $total;

		if ($added_taxes && !$compound_taxes)
		{

			/*
			 * No compound taxes case
			 */
			
			if (count($added_taxes) == 1)
			{
				/*
				 * A single added tax case
				 */
				return $total/(1 + $added_taxes[0]->rate/100);
			}

			/*
			 * Two added taxes case
			 */

			return $total/(1 + $added_taxes[0]->rate/100 + $added_taxes[1]->rate/100);
		} else {

			/*
			 * Compound taxes case
			 */
			
			if (!count($added_taxes))
			{
				/*
				 * No added taxes case (there should be no such cases)
				 */
				if (count($compound_taxes) == 2)
					return $total/((1 + $compound_taxes[0]->rate/100) * (1 + $compound_taxes[1]->rate/100));
				else
					return $total/(1 + $compound_taxes[0]->rate/100);
			} else
			{
				/*
				 * Single added tax + single compound tax case
				 */
				return $total/((1 + $added_taxes[0]->rate/100)*(1 + $compound_taxes[0]->rate/100));
			}
		}
		
		return $total;
	}

	// Service methods
	// 

	public static function calculate_taxes($items, $location_info)
	{
		$result = (object)array(
			'tax_total' => 0,
			'taxes' => array(),
			'item_taxes' => array()
		);

		$item_taxes = array();
		$taxes = array();
		$tax_total = 0;

		foreach ($items as $item_index=>$item)
		{
			$tax_class = self::find_by_id($item->tax_class_id);
			if ($tax_class)
			{
				$item_discount = $item->price * $item->discount;
				$item_price = $item->price - $item_discount;

				$this_item_taxes = $tax_class->get_tax_rates($item_price, $location_info);

				$item_taxes[$item_index] = $this_item_taxes;

				foreach ($this_item_taxes as $tax)
				{
					$key = $tax_class->id.'|'.$tax->name;
					
					if (!array_key_exists($key, $taxes))
					{
						$effective_rate = $tax->tax_rate;
						
						if ($tax->compound_tax)
						{
							$added_tax = self::find_added_tax($this_item_taxes);
							if ($added_tax)
								$effective_rate = $tax->tax_rate*(1+$added_tax->tax_rate);
						}

						$taxes[$key] = array('total'=>0, 'rate'=>$tax->rate, 'effective_rate'=>$effective_rate, 'name'=>$tax->name, 'tax_amount');
					}
						
					$item_tax_value = $item_price*$item->quantity;
					
					$taxes[$key]['total'] += $item_tax_value;
				}
			}
		}

		$compound_taxes = array();

		foreach ($taxes as $tax_total_info)
		{
			if (!array_key_exists($tax_total_info['name'], $compound_taxes))
			{
				$tax_data = array('name'=>$tax_total_info['name'], 'total'=>0);
				$compound_taxes[$tax_total_info['name']] = (object)$tax_data;
			}

			$tax_value = $tax_total_info['total']*$tax_total_info['effective_rate'];
			$compound_taxes[$tax_total_info['name']]->total += $tax_value;

			$tax_total += $tax_value;
		}

		foreach ($compound_taxes as $name=>&$tax_data)
			$tax_data->total = round($tax_data->total, 2);
		
		$result->tax_total = round($tax_total, 2);
		$result->taxes = $compound_taxes;
		$result->item_taxes = $item_taxes;

		return $result;
	}

	public static function eval_total_tax($tax_list)
	{
		$result = 0;

		if (!$tax_list)
			return $result;

		foreach ($tax_list as $tax)
			$result += $tax->rate;
			
		return $result;
	}

	protected function validate_rates()
	{
		if (!is_array($this->rates) || !count($this->rates))
			$this->field_error('rates', 'Please specify tax rates.');

		/*
		 * Preload countries and states
		 */

		$db_country_codes = Db_Helper::object_array('select * from location_countries order by code');
		$countries = array();
		foreach ($db_country_codes as $country)
			$countries[$country->code] = $country;
		
		$country_codes = array_merge(array('*'), array_keys($countries));
		$db_states = Db_Helper::object_array('select * from location_states order by code');
		
		$states = array();
		foreach ($db_states as $state)
		{
			if (!array_key_exists($state->country_id, $states))
				$states[$state->country_id] = array('*'=>null);

			$states[$state->country_id][mb_strtoupper($state->code)] = $state;
		}
		
		foreach ($countries as $country)
		{
			if (!array_key_exists($country->id, $states))
				$states[$country->id] = array('*'=>null);
		}

		/*
		 * Validate table rows
		 */
		 
		$rate_list = $this->rates;

		$is_manual_disabled = isset($rate_list['disabled']);
		if ($is_manual_disabled)
			$rate_list = unserialize($rate_list['serialized']);

		$processed_rates = array();
		
		$line_number = 0;
		foreach ($rate_list as $row_index=>&$rates)
		{
			$line_number++;

			$empty = true;
			foreach ($rates as $value)
			{
				if (strlen(trim($value)))
				{
					$empty = false;
					break;
				}
			}

			if ($empty)
				continue;

			/*
			 * Validate country
			 */
			$country = $rates['country'] = trim(mb_strtoupper($rates['country']));
			if (!strlen($country))
				$this->field_error('rates', 'Please specify country code. Valid codes are: '.implode(', ', $country_codes).'. Line: '.$line_number, $row_index, 'country');
			
			if (!array_key_exists($country, $countries) && $country != '*')
				$this->field_error('rates', 'Invalid country code. Valid codes are: '.implode(', ', $country_codes).'. Line: '.$line_number, $row_index, 'country');
				
			/*
			 * Validate state
			 */
			if ($country != '*')
			{
				$country_obj = $countries[$country];
				$country_states = $states[$country_obj->id];
				$state_codes = array_keys($country_states);

				$state = $rates['state'] = trim(mb_strtoupper($rates['state']));
				if (!strlen($state))
					$this->field_error('rates', 'Please specify state code. State codes, valid for '.$country_obj->name.' are: '.implode(', ', $state_codes).'. Line: '.$line_number, $row_index, 'state');

				if (!in_array($state, $state_codes) && $state != '*')
					$this->field_error('rates', 'Invalid state code. State codes, valid for '.$country_obj->name.' are: '.implode(', ', $state_codes).'. Line: '.$line_number, $row_index, 'state');
			} else {
				$state = $rates['state'] = trim(mb_strtoupper($rates['state']));
				if (!strlen($state) || $state != '*')
					$this->field_error('rates', 'Please specify state code as wildcard (*) to indicate "Any state" condition. Line: '.$line_number, $row_index, 'state');
			}

			/*
			 * Validate rate
			 */
			
			$rate = $rates['rate'] = trim(mb_strtoupper($rates['rate']));
			if (!strlen($rate))
				$this->field_error('rates', 'Please specify rate. Line: '.$line_number, $row_index, 'rate');

			if (!Phpr_Number::is_valid_float($rate))
				$this->field_error('rates', 'Invalid numeric value in column Rate. Line: '.$line_number, $row_index, 'rate');
				
			/*
			 * Validate priority
			 */
			
			$priority = $rates['priority'] = trim(mb_strtoupper($rates['priority']));
			if (!strlen($priority))
				$this->field_error('rates', 'Please specify priority. Line: '.$line_number, $row_index, 'priority');

			if (!Phpr_Number::is_valid_float($priority))
				$this->field_error('rates', 'Invalid numeric value in column Priority. Line: '.$line_number, $row_index, 'priority');

			/*
			 * Validate compound
			 */

			$compound = $rates['compound'] = trim(mb_strtoupper($rates['compound']));
			if (strlen($compound))
			{
				if (preg_match('/^[0-9]+$/', $compound))
					$compound = (int)$compound;
				
				if ($compound != 'Y' && $compound != 'N' && $compound != 'YES' && $compound != 'NO' && $compound !== 0 && $compound !== 1)
				{
					$this->field_error('rates', 'Invalid Boolean value in column Compound. Please use the following values: y, yes, 1, n, no, 0. Line: '.$line_number, $row_index, 'compound');
				}
			}

			/*
			 * Validate tax name
			 */
			
			$tax_name = $rates['tax_name'] = trim(mb_strtoupper($rates['tax_name']));
			if (!strlen($tax_name))
				$this->field_error('rates', 'Please specify tax name', $row_index, 'tax_name');

			$rates['zip'] = trim(mb_strtoupper($rates['zip']));
			$rates['city'] = trim($rates['city']);

			$processed_rates[] = $rates;
		}

		if (!count($processed_rates))
			$this->field_error('rates', 'Please specify tax rates.');

		$this->rates = $processed_rates;
	}
	
	protected function field_error($field, $message, $grid_row = null, $grid_column = null)
	{
		if ($grid_row != null)
			$this->validation->set_widget_data(Db_Grid_Widget::get_cell_error_data($this, 'rates', $grid_column, $grid_row));
		
		$this->validation->set_error($message, $field, true);
	}

	/**
	 * Combines two arrays of taxes by tax name 
	 * You can pass arrays of shipping and sales taxes to the method
	 * @return array Returns an array of tax information objects. Each object has the name and total fields
	 */
	public static function combine_taxes_by_name($tax_array1, $tax_array2)
	{
		$result = array();

		$tax_array = array();
		foreach ($tax_array1 as $tax_info)
			$tax_array[] = $tax_info;

		foreach ($tax_array2 as $tax_info)
			$tax_array[] = $tax_info;
		
		foreach ($tax_array as $tax_info)
		{
			$tax_name = $tax_info->name;
			if (!array_key_exists($tax_name, $result))
			{
				$info = array('name'=>$tax_name, 'total'=>0);
				$result[$tax_name] = (object)$info;
			}
			$result[$tax_name]->total += $tax_info->total;
		}
		
		return $result;
	}
	
	/**
	 * Determines whether the tax exempt mode should be applied.
	 */
	public static function set_tax_exempt($value)
	{
		self::$_tax_exempt = $value;
	}
	
	/**
	 * Returns a sum of a given amount and its total tax.
	 * @param integer $tax_class_id Specifies tax class identifier.
	 * @param float $price Specifies amount to apply the tax to.
	 * @return float Returns a sum of the amount and its total tax
	 */
	public static function apply_tax($tax_class_id, $price)
	{
		return self::get_total_tax($tax_class_id, $price) + $price;
	}
}