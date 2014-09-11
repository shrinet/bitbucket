<?php

class Service_Provider extends Db_ActiveRecord
{
	public $implement = 'Db_Model_Attachments, Db_Model_Dynamic';
	public $table_name = 'service_providers';
	protected $api_added_columns = array();	
	protected static $cache_by_user = array();
	public $belongs_to = array(
		'user' => array('class_name' => 'User', 'foreign_key' => 'user_id'),
		'country' => array('class_name'=>'Location_Country', 'foreign_key' => 'country_id'),
		'state' => array('class_name'=>'Location_State', 'foreign_key' => 'state_id'),
		'plan' => array('class_name'=>'Service_Plan', 'foreign_key'=>'plan_id')
	);
	public $has_and_belongs_to_many = array(
		'categories' => array('class_name'=>'Service_Category', 'join_table'=>'service_categories_providers', 'foreign_key'=>'category_id', 'primary_key'=>'provider_id'),
#		'membership' => array('class_name'=>'Service_Membership','join_table'=>'service_membership_providers', 'foreign_key'=>'plan_id',)
	);
	public $has_many = array(
		'photo' => array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Service_Provider' and field='photo'", 'order'=>'sort_order, id', 'delete'=>true),
		'logo' => array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Service_Provider' and field='logo'", 'order'=>'sort_order, id', 'delete'=>true),
		'portfolio' => array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Service_Provider' and field='portfolio'", 'order'=>'sort_order, id', 'delete'=>true),
		'testimonials' => array('class_name'=>'Service_Testimonial', 'foreign_key'=>'provider_id', 'conditions'=>'is_published = 1'),
		'quotes' => array('class_name'=>'Service_Quote', 'foreign_key'=>'provider_id'),
		'ratings' => array('class_name'=>'Service_Rating', 'foreign_key'=>'provider_id', 'order'=>'created_at'),
#		'membership' => array('class_name'=>'service_membership_providers', 'foreign_key'=>'provider_id'),
	);
	public $custom_columns = array(
		'category_string' => db_varchar,
		'address_string' => db_varchar,
		'location_string' => db_varchar,
		'description_html' => db_varchar,
#		'membership_string' => db_varchar
	);
	public function define_columns($context = null)
	{
		$this->define_column('id', '#')->order('desc');
		$this->define_column('business_name', 'Business Name')->validation()->required();
		$this->define_column('license', 'License')->default_invisible()->validation()->required();
		$this->define_column('type', 'Type')->default_invisible()->validation()->required();
		$this->define_column('bonded', 'Bounded')->default_invisible()->validation()->required();
		$this->define_column('licn_state', 'State')->default_invisible()->validation()->required();
		$this->define_column('date_issue', 'Date Issued')->date_format('%x')->default_invisible()->validation()->required();
		$this->define_column('description', 'Description')->invisible()->validation()->fn('trim');
		$this->define_column('url', 'Website URL');
		$this->define_column('established', 'Year Established')->invisible();
		$this->define_column('phone', 'Phone Number')->default_invisible();
		$this->define_column('mobile', 'Mobile Phone Number')->default_invisible();
		$this->define_column('street_addr', 'Street Address');
		$this->define_column('city', 'City');
		$this->define_column('zip', 'Zip / Postal Code');
		$this->define_column('skills', 'Skills');
		$this->define_relation_column('user', 'user', 'User', db_varchar, '@first_name')->default_invisible();
		$this->define_relation_column('country', 'country', 'Country', db_varchar, '@name')->default_invisible();
		$this->define_relation_column('state', 'state', 'State', db_varchar, '@name')->default_invisible();
		$this->define_multi_relation_column('photo', 'photo', 'Photo', '@name')->invisible();
		$this->define_multi_relation_column('logo', 'logo', 'Logo', '@name')->invisible();
		$this->define_multi_relation_column('portfolio', 'portfolio', 'Portfolio', '@name')->invisible();
		$this->define_multi_relation_column('ratings', 'ratings', 'Ratings', '@rating')->invisible();
		$this->define_relation_column('plan', 'plan', 'Plan', db_varchar, '@name')->invisible();
		$this->define_multi_relation_column('categories', 'categories', 'Categories', '@name')->default_invisible()->validation();
		$this->define_column('rating', 'Rating (Approved)')->default_invisible();
		$this->define_column('rating_all', 'Rating (All)')->default_invisible();
		$this->define_column('stat_earned', 'Total Earned')->default_invisible();
		$this->define_column('stat_leads', 'Total Leads')->default_invisible();
		$this->define_column('stat_quotes', 'Total Quotes')->default_invisible();
		$this->define_column('stat_wins', 'Total Wins')->default_invisible();
		$this->define_column('stat_quote_average', 'Average Quote')->default_invisible();
		$this->define_multi_relation_column('ratings', 'ratings', 'Ratings', '@id')->invisible()->validation();
		$this->define_multi_relation_column('testimonials', 'testimonials', 'Testimonials', '@id')->invisible()->validation();
		$this->define_column('credits', 'Credit')->default_invisible();
		$this->define_column('active_date', 'Active Date')->default_invisible();
		// Extensibility
		$this->defined_column_list = array();
		Phpr::$events->fire_event('service:on_extend_provider_model', $this, $context);
		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public $calculated_columns = array(
#		'credits' => array('sql'=>'plan_calculated_join.credits', 'type'=>db_number),
		'plan_name' => array('sql'=>'plan_calculated_join.name', 'type'=>db_varchar),
	);
	
	public function define_form_fields($context = null)
	{
		if ($context != "preview")
		{
			$this->add_form_field('business_name','left')->tab('Business');
			$this->add_form_field('user','right')
				->display_as(frm_record_finder, array(
					'sorting'=>'first_name, last_name, email',
					'list_columns'=>'first_name,last_name,email,guest,created_at',
					'search_prompt'=>'Find user by name or email',
					'form_title'=>'Find User',
					'display_name_field'=>'name',
					'display_description_field'=>'email',
					'prompt'=>'Click the Find button to find a user'))->tab('Business');
			
			$this->add_form_field('license','left')->tab('Business');
			$this->add_form_field('type','right')->tab('Business');
			$this->add_form_field('bonded','left')->tab('Business');
			$this->add_form_field('licn_state','right')->tab('Business');
			$this->add_form_field('date_issue','left')->display_as(frm_date)->tab('Business');		

			$this->add_form_field('url', 'left')->tab('Business');
			$this->add_form_field('established', 'right')->tab('Business');

			$this->add_form_field('photo','left')->display_as(frm_file_attachments)->display_files_as('single_image')->add_document_label('Upload photo')->tab('Profile')->no_attachments_label('Photo is not uploaded')->image_thumb_size(100)->file_download_base_url(url('admin/files/get/'));
			$this->add_form_field('logo','right')->display_as(frm_file_attachments)->display_files_as('single_image')->add_document_label('Upload logo')->tab('Profile')->no_attachments_label('Logo is not uploaded')->image_thumb_size(100)->file_download_base_url(url('admin/files/get/'));
			$this->add_form_field('description')->tab('Profile');
			$this->add_form_field('categories')->tab('Categories')->comment('Select categories the media belongs to.', 'above')->reference_sort('name');
			$this->add_form_field('phone', 'left')->tab('Contact');
			$this->add_form_field('mobile', 'right')->tab('Contact');
			$this->add_form_field('street_addr')->tab('Contact');
			$this->add_form_field('city', 'left')->tab('Contact');
			$this->add_form_field('zip', 'right')->tab('Contact');
			$this->add_form_field('country', 'left')->tab('Contact')->display_as(frm_dropdown)->empty_option('Select an Option');
			$this->add_form_field('state', 'right')->tab('Contact')->display_as(frm_dropdown);
			$this->add_form_field('portfolio')->tab('Portfolio')->display_as(frm_file_attachments)->display_files_as('image_list')->add_document_label('Add image(s)')->no_attachments_label('There are no images uploaded')->file_download_base_url(url('admin/files/get/'));
			$this->add_form_field('plan')->tab('Membership')->display_as(frm_radio)->empty_option('Select an Option');
			// Ratings
			$this->add_form_field('ratings')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('rating', 'comment'),
				'search_enabled' => true,
				'search_fields' => array('rating', 'comment'),
				'search_prompt' => 'find ratings by comment',
				'no_data_message' => 'This provider has no ratings',
				'control_panel' => 'ratings_control_panel',
				'is_editable' => true,
				'form_title' => 'Rating',
				'form_context' => 'create'
			))->tab('Ratings');  

			// Testimonials
			$this->add_form_field('testimonials')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('name', 'location'),
				'search_enabled' => true,
				'search_fields' => array('@name', '@location'),
				'search_prompt' => 'find testimonials by name',
				'no_data_message' => 'This provider has no testimonials',
				'control_panel' => 'testimonials_control_panel',
				'is_editable' => true,
				'form_title' => 'Testimonial',
				'form_context' => 'create'
			))->tab('Testimonials');  

		}
		else 
		{
			// Preview
			$this->add_form_field('business_name', 'left')->tab('Provider');
			$this->add_form_field('url','right')->tab('Provider');
			$this->add_form_field('established', 'left')->tab('Provider');
			$this->add_form_field('logo','right')->tab('Provider');
			$this->add_form_field('description')->tab('Provider');
			
		}

		// Extensibility
		Phpr::$events->fire_event('service:on_extend_provider_form', $this, $context);
		foreach ($this->api_added_columns as $column_name)
		{
			$form_field = $this->find_form_field($column_name);
			if ($form_field)
			$form_field->options_method('get_added_field_options');
		}
	}

	// Extensibility
	//

	public function get_added_field_options($db_name, $current_key_value = -1)
	{
		$result = Phpr::$events->fire_event('service:on_get_provider_field_options', $db_name, $current_key_value);
		foreach ($result as $options)
		{
			if (is_array($options) || (strlen($options && $current_key_value != -1)))
				return $options;
		}

		return false;
	}

	// Events
	//

	public function after_create($session_key = null)
	{
		$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->business_name, 130);
		$bind = array(
			'id' => $this->id,
			'url_name' => $this->url_name
		);
		self::geocode_address($this->id);
		Db_Helper::query('update service_providers set url_name=:url_name where id=:id', $bind);

		// Deferred geocoding
		Phpr_Cron::queue_job('Service_Provider::geocode_address', array($this->id));
	}

	public function after_update()
	{
		Phpr::$events->fire_event('service:on_after_update_provider', $this);
		self::geocode_address($this->id);
//		Db_Helper::query('insert into provider_to_membership (service_provider_id, plan_id, plan_active_date, credits_remaining) VALUES (:service_provider_id, :plan_id, :plan_active_date, :credits_remaining)', $bindplan);
		// Deferred geocoding
		if ((isset($this->fetched['city']) && $this->city != $this->fetched['city']) 
			|| (isset($this->fetched['zip']) && $this->zip != $this->fetched['zip']))
			Phpr_Cron::queue_job('Service_Provider::geocode_address', array($this->id));
	}
	
	public function after_delete()
	{
		$files = Db_File::create()->where('master_object_class=?', get_class($this))->where('master_object_id=?', $this->id)->find_all();
		foreach ($files as $file)
		{
			$file->delete();
		}

		Db_Helper::query('delete from service_provider_groups_providers where provider_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_categories_providers where provider_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_quotes where provider_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_ratings where provider_id=:id', array('id'=>$this->id));
		Db_Helper::query('delete from service_testimonials where provider_id=:id', array('id'=>$this->id));
	}

	public function before_save($session_key = null)
	{

		Service_plan::apply_membership($this);
		Phpr::$events->fire_event('service:on_before_save_provider', $this, $session_key);
	}

	public function before_update($session_key = null)
	{
		if (isset($this->fetched['business_name']) && $this->fetched['business_name'] != $this->business_name)
			$this->url_name = Db_Helper::get_unique_slugify_value($this, 'url_name', $this->business_name, 130);
	}

	
	public function get_state_options($key_value = -1)
	{
		return Location_State::get_name_list($this->country_id);
	}

	public function get_country_options($key_value = -1)
	{
		return Location_Country::get_name_list();
	}

	// Filters
	// 

	// Where user owns the profile
	public function apply_owner($user)
	{
		$this->where('user_id=?', $user->id);
		return $this;
	}

	// Where provider is in categories
	public function apply_category($category=array())
	{
		if (is_string($category))
			$category = array($category);

		$this->from('service_providers', 'distinct service_providers.*');
		$this->join('service_categories_providers', 'service_categories_providers.provider_id = service_providers.id');
		$this->where('service_categories_providers.category_id in (?)', array($category));
		return $this;
	}

	public function find_top_providers($user=null)
	{
		if ($user)
			$this->where('user_id!=?', $user->id);

		$this->order('rating desc');
		return $this;
	}

	public function find_in_object_categories($object, $field_name='categories')
	{
		if (!$object->{$field_name}->count)
			return $this;

		$object_categories = $object->{$field_name}->as_array('id');
		$this->apply_category($object_categories);
		return $this;
	}

	// Where provider has touched (asked a question or quoted on) a request
	public function apply_request_touch($request)
	{
		$this->from('service_providers', 'distinct service_providers.*');
		$this->join('service_quotes', 'service_quotes.provider_id = service_providers.id');
		$this->join('service_questions', 'service_questions.provider_id = service_providers.id');
		$this->join('service_requests', 'service_requests.id = service_questions.request_id OR service_requests.id = service_quotes.request_id');
		$this->where('service_requests.id=?', $request->id);
		return $this;
	}

	// Service methods
	//

	public function load_from_user($user)
	{
		$this->phone = $user->phone;
		$this->mobile = $user->mobile;
		$this->street_addr = $user->street_addr;
		$this->city = $user->city;
		$this->zip = $user->zip;
		$this->state_id = $user->state_id;
		$this->country_id = $user->country_id;
		return $this;
	}
/*	
	public function find_membership()
	{
		return Service_Membership::create()->where('provider_id=?', $this->id);
	}*/
	public function find_ratings()
	{
		return Service_Rating::create()->where('provider_id=?', $this->id);
	}

	public function find_testimonials()
	{
		return Service_Testimonial::create()->where('provider_id=?', $this->id);
	}

	public static function create_from_user($user)
	{
		if (!$user)
			return null;
		
		return self::create()->find_by_user_id($user->id);
	}

	public static function create_from_request_and_user($request, $user)
	{
		if (!$user)
			return null;
		
		return self::create()->find_in_object_categories($request)->find_by_user_id($user->id);
	}

	public static function match_to_request($request)
	{
		return self::create()->find_in_object_categories($request)->find_all();
	}

	public static function get_profile_ids_from_user($user)
	{
		$id = $user->id;

		if (isset(self::$cache_by_user[$id]))
			return self::$cache_by_user[$id];

		return self::$cache_by_user[$id] = Db_Helper::scalar_array('SELECT id FROM service_providers WHERE user_id=?', $id);
	}

	public function get_url($page=null, $add_hostname=false)
	{
		if (!$page) 
			$page = Cms_Page::get_url_from_action('service:provider');

		return root_url($page.'/'.$this->url_name, $add_hostname);
	}

	public function get_photo($size=100, $default=null)
	{
		if ($this->photo->count==0)
			return $default;

		$size = Phpr_String::dimension_from_string($size);
		return $this->photo[0]->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop'));
	}

	public function get_logo($size='autox60', $default=null)
	{
		if ($this->logo->count==0)
			return $default;
		
		$size = Phpr_String::dimension_from_string($size);
		return $this->logo[0]->get_thumbnail_path($size['width'], $size['height'], true, array('mode'=>'crop'));
	}

	public function get_portfolio()
	{
		if ($this->portfolio->count==0)
			return array();

		$result = array();
		foreach ($this->portfolio as $folio)
		{
			$item = new stdClass();
			$item->id = $folio->id;
			$item->image = $folio->get_thumbnail_path(990, 660, true, array('mode'=>'crop'));
			$item->thumb = $folio->get_thumbnail_path(100, 75, true, array('mode'=>'crop'));
			$result[] = $item;
		}
		return $result;
	}

	public static function count_user_profiles($user)
	{
		return Db_Helper::scalar('select count(*) from service_providers where user_id=:id', array('id'=>$user->id));
	}

	public static function update_rating_fields($provider_id)
	{
		Db_Helper::query(
			"update service_providers set 
				rating = (select avg(rating) from service_ratings where rating is not null and provider_id=service_providers.id and moderation_status=:approved_status),
				rating_all = (select avg(rating) from service_ratings where rating is not null and provider_id=service_providers.id),
				rating_num = ifnull((select count(*) from service_ratings where rating is not null and provider_id=service_providers.id and moderation_status=:approved_status), 0),
				rating_all_num = ifnull((select count(rating) from service_ratings where rating is not null and provider_id=service_providers.id), 0)
			where service_providers.id=:provider_id", 
			array(
				'provider_id' => $provider_id,
				'approved_status' => Service_Rating::status_approved
			)
		);
	}

	public static function update_stat_fields($provider_id)
	{
		Db_Helper::query(
			"update service_providers set 
				stat_earned = (select ifnull(sum(price),0.00) from service_quotes where price is not null and provider_id=service_providers.id and moderation_status=:approved_status and status_id=:status_accepted_id),
				stat_quotes = (select count(id) from service_quotes where provider_id=service_providers.id and moderation_status=:approved_status),
				stat_wins = (select count(id) from service_quotes where provider_id=service_providers.id and moderation_status=:approved_status and status_id=:status_accepted_id),
				stat_quote_average = (select ifnull(sum(price)/count(id),0.00) from service_quotes where provider_id=service_providers.id and moderation_status=:approved_status)
			where service_providers.id=:provider_id", 
			array(
				'provider_id' => $provider_id,
				'status_accepted_id' => Service_Quote_Status::find_id_from_code(Service_Quote_Status::status_accepted),
				'approved_status' => Service_Rating::status_approved
			)
		);
	}

	public function set_notify_vars(&$template, $prefix='')
	{
		$template->set_vars(array(
			$prefix.'business_name' => $this->business_name,
			$prefix.'phone'         => $this->phone,
			$prefix.'mobile'        => $this->mobile,
			$prefix.'street_addr'   => $this->street_addr,
			$prefix.'city'          => $this->city,
			$prefix.'zip'           => $this->zip,
			$prefix.'country'       => ($this->country) ? $this->country->name : null,
			$prefix.'state'         => ($this->state) ? $this->state->name : null
		));
	}
	
	
	public static function geocode_address($id)
	{
		$obj = self::create()->find($id);
		if (!$obj)
			return;
		
		Location_Geocode::geocode_to_object($obj, $obj->address_string);
		$bind = array('lng'=>$obj->longitude, 'lat'=>$obj->latitude, 'id'=>$obj->id);
		Db_Helper::query("update service_providers set latitude=:lat, longitude=:lng where id=:id", $bind);
	}    

	// Custom columns
	//

	public function eval_category_string($categories=null)
	{
		if (!$categories)
			$categories = $this->categories;

		$str = "";
		if ($categories)
		{
			foreach ($categories as $key=>$category)
			{
				if ($key == 0) // Is first
					$str .= $category->name;
				else
					$str .= ", " . $category->name;
			}
		}
		return $str;
	}

	public function eval_address_string()
	{
		$str = '';
		$str .= ($this->street_addr) ? $this->street_addr . ', ' : '';
		$str .= $this->city;
		$str .= ' ' . $this->zip;
		$str .= ($this->state) ? ', ' . $this->state->name : '';
		$str .= ($this->country) ? ', ' . $this->country->name : '';
		$str = trim($str);

		if (!strlen($str))
			return null;

		return $str;
	}

	public function eval_location_string()
	{
		$str = '';
		$str .= $this->city;
		$str .= ($this->state) ? ', ' . $this->state->code : '';
		$str = trim($str);
		
		if (!strlen($str))
			return null;

		return $str;
	}

	public function eval_description_html()
	{
		if (strlen($this->description))
			return Phpr_Html::paragraphize($this->description);
		else
			return null;
	}

}

