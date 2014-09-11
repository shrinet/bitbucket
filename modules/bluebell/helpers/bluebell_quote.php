<?php

class Bluebell_Quote 
{
	const quote_type_onsite = 'onsite';
	const quote_type_flat_rate = 'flat_rate';

	public static function price_summary($quote)
	{
		if ($quote->quote_type == Bluebell_Quote::quote_type_onsite && $quote->onsite_travel_required)
		{
			return __('estimate %s - %s, travel %s', array(
				format_currency($quote->onsite_price_start),
				format_currency($quote->onsite_price_end),
				format_currency($quote->onsite_travel_price)
			));
		}
		else if ($quote->quote_type == Bluebell_Quote::quote_type_onsite)
		{
			return __('estimate %s - %s', array(
				format_currency($quote->onsite_price_start),
				format_currency($quote->onsite_price_end)
			));
		}
		else
		{
			return __('labor %s, materials %s', array(
				format_currency($quote->flat_labor_price),
				format_currency($quote->price-$quote->flat_labor_price)
			));
		}
	}

	public static function price_terms($quote)
	{
		if ($quote->quote_type == Bluebell_Quote::quote_type_flat_rate)
			return null;

		if ($quote->onsite_travel_required && $quote->onsite_travel_waived)
			return __('consultation required, travel price waived if accepted');

		return __('consultation required');
	}
}