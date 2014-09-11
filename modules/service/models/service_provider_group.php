<?php

class Service_Provider_Group extends Db_ActiveRecord
{
	protected static $provider_sort_orders = null;

	public $has_and_belongs_to_many = array(
		'providers'=>array('class_name'=>'Service_Provider', 'join_table'=>'service_provider_groups_providers', 'primary_key'=>'provider_group_id', 'foreign_key'=>'provider_id', 'order'=>'business_name'),
	);

	public function define_columns($context = null)
	{

		$this->define_column('name', 'Group Name')->validation()->required();
		$this->define_column('code', 'API Code');
		$this->define_multi_relation_column('providers', 'providers', 'Providers', '@business_name')->invisible()->validation();
		//$this->define_column('provider_num', 'Providers');
	}

	public function define_form_fields($context = null)
	{
		$this->add_form_field('name','full')->tab('Group');
		$this->add_form_field('code','left')->tab('Group');
		$this->add_form_field('providers')->tab('Providers')->comment('Providers belonging to the group', 'above')->display_as('providers')->reference_sort('@business_name');
	}

	public function after_delete()
	{
		Db_Helper::query('delete from service_provider_groups_providers where provider_group_id=:id', array('id'=>$this->id));
	}

	public function get_provider_orders()
	{
		if (self::$provider_sort_orders !== null)
			return self::$provider_sort_orders;

		$orders = Db_Helper::object_array('select sort_order, provider_id from service_provider_groups_providers where provider_group_id=:group_id',
		array('group_id'=>$this->id));

		$result = array();
		foreach ($orders as $order_item)
			$result[$order_item->provider_id] = $order_item->sort_order;

		return self::$provider_sort_orders = $result;
	}

	public function set_provider_orders($item_ids, $item_orders)
	{
		if (is_string($item_ids))
			$item_ids = explode(',', $item_ids);

		if (is_string($item_orders))
			$item_orders = explode(',', $item_orders);

		foreach ($item_ids as $index=>$id)
		{
			$order = $item_orders[$index];
			Db_Helper::query('update service_provider_groups_providers set sort_order=:sort_order where provider_id=:provider_id and provider_group_id=:group_id', array(
				'sort_order'=>$order,
				'provider_id'=>$id,
				'group_id'=>$this->id
			));
		}
	}
}
