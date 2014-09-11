<?php

class Service_Request extends Db_ActiveRecord
{
	public $implement = 'Db_Model_Attachments, Db_Model_Dynamic';

	protected $api_added_columns = array();

	const link_type_invited = 'invited';
	const link_type_banned = 'banned';

	const type_sealed = 'sealed';
	const type_open = 'open';
	const type_private = 'private';

	public $has_and_belongs_to_many = array(
		'categories' => array('class_name'=>'Service_Category', 'join_table'=>'service_categories_requests', 'foreign_key'=>'category_id', 'primary_key'=>'request_id'),
		'skills' => array('class_name'=>'Service_Skill', 'join_table'=>'service_skills_requests', 'foreign_key'=>'skill_id', 'primary_key'=>'request_id'),
	);

	public $belongs_to = array(
		'user' => array('class_name' => 'User', 'foreign_key' => 'user_id'),
		'country' => array('class_name'=>'Location_Country', 'foreign_key' => 'country_id'),
		'state' => array('class_name'=>'Location_State', 'foreign_key' => 'state_id'),
		'status' => array('class_name'=>'Service_Status', 'foreign_key' => 'status_id'),
		'accepted_quote' => array('class_name'=>'Service_Quote', 'foreign_key' => 'id', 'primary_key' => 'request_id', 'conditions' => 'status_id = 2')
	);

	public $calculated_columns = array(
		'is_new' => array('sql' => "if (service_requests.created_at > timestampadd(hour, -24, now()), 1, 0)", 'type' => db_bool),
		'total_quotes' => array('sql' => "select count(*) from service_quotes where request_id = service_requests.id and deleted_at is null", 'type' => db_number),
		'total_questions' => array('sql' => "select count(*) from service_questions where request_id = service_requests.id", 'type' => db_number),
		'status_code' => array('sql'=>'status_calculated_join.code', 'type'=>db_varchar),
		'status_name' => array('sql'=>'status_calculated_join.name', 'type'=>db_varchar),
	);

	public $custom_columns = array(
		'category_string' => db_varchar,
		'address_string' => db_varchar,
		'location_string' => db_varchar,
		'description_html' => db_varchar,
		'description_summary' => db_varchar,
		'display_name' => db_varchar,
	);

	public $has_many = array(
		'files' => array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Service_Request' and field='files'", 'order'=>'sort_order, id', 'delete'=>true),
		'quotes' => array('class_name'=>'Service_Quote', 'foreign_key'=>'request_id', 'conditions'=>'deleted_at is null'),
		'questions' => array('class_name'=>'Service_Question', 'foreign_key'=>'request_id'),
		'answers' => array('class_name'=>'Service_Answer', 'foreign_key'=>'request_id')
	);

	public function define_columns($context = null)
	{
		$this->define_column('id', '#')->order('desc');
		$this->define_column('title', 'Title')->validation()->required();
		$this->define_column('required_by', 'Required By')->invisible();
		$this->define_column('description', 'Description')->invisible()->validation()->clean_html();
		$this->define_column('description_extra', 'Extra Description')->invisible()->validation()->clean_html();
		$this->define_multi_relation_column('categories', 'categories', 'Categories', '@name')->default_invisible()->validation();
		$this->define_column('city', 'City')->default_invisible();
		$this->define_column('zip', 'Zip / Postal Code')->default_invisible();
		$user_field = $this->define_relation_column('user', 'user', 'User', db_varchar, "concat(@username, ' (', @email, ')')")->validation();

		if ($context != "preview")
			$user_field->required();
		
		$this->define_relation_column('country', 'country', 'Country', db_varchar, '@name')->default_invisible();
		$this->define_relation_column('state', 'state', 'State', db_varchar, '@name')->default_invisible();
		$this->define_column('type', 'Privacy')->default_invisible()->validation();
		$this->define_relation_column('status', 'status', 'Status', db_varchar, '@name')->validation()->required("A request must have a status");

		$this->define_multi_relation_column('files', 'files', 'Attachments', '@name')->invisible();

		$this->define_column('created_at', 'Created');
		$this->define_column('updated_at', 'Last Updated')->default_invisible();
		$this->define_column('expired_at', 'Expired At')->default_invisible();

		$this->define_multi_relation_column('quotes', 'quotes', 'Quotes', '@id')->invisible()->validation();
		$this->define_multi_relation_column('questions', 'questions', 'Questions', '@id')->invisible()->validation();
		$this->define_multi_relation_column('answers', 'answers', 'Answers', '@id')->invisible()->validation();

		// Extensibility
		$this->defined_column_list = array();
		Phpr::$events->fire_event('service:on_extend_request_model', $this, $context);
		$this->api_added_columns = array_keys($this->defined_column_list);
	}

	public function define_form_fields($context = null)
	{
		if ($context != "preview")
		{
			$this->add_form_field('title','left')->tab('Request');
			$this->add_form_field('user','right')
				->display_as(frm_record_finder, array(
					'sorting'=>'first_name, last_name, email',
					'list_columns'=>'first_name,last_name,email,guest,created_at',
					'search_prompt'=>'Find user by name or email',
					'form_title'=>'Find User',
					'display_name_field'=>'name',
					'display_description_field'=>'email',
					'prompt'=>'Click the Find button to find a user'))->tab('Request');

			$this->add_form_field('status','left')->tab('Request')->reference_sort('id');
			$this->add_form_field('expired_at','right')->tab('Request');
			$this->add_form_field('description')->tab('Request');
			$this->add_form_field('files', 'left')->display_as(frm_file_attachments)->tab('Request')->display_files_as('file_list')->add_document_label('Add file attachment(s)')->no_attachments_label('There are no files uploaded')->file_download_base_url(url('admin/files/get/'));
			$this->add_form_field('type','right')->tab('Request')->display_as(frm_radio);

			$this->add_form_field('categories')->tab('Categories')->comment('Select categories the media belongs to.', 'above')->reference_sort('name');

			$this->add_form_field('city', 'left')->tab('Location');
			$this->add_form_field('zip', 'right')->tab('Location');
			$this->add_form_field('country', 'left')->tab('Location')->display_as(frm_dropdown)->empty_option('Select an Option');
			$this->add_form_field('state', 'right')->tab('Location')->display_as(frm_dropdown);

			// Extra Description
			$this->add_form_field('description_extra')->display_as(frm_widget, array(
				'class'=>'Db_MultiText_Widget', 
				'fields' => array(
					'created_at' => array('type'=>db_datetime, 'label'=>'Created At'),
					'description' => array('type'=>db_text, 'label'=>'Description')
				)
			))->tab('Additions');

		} else {
			// Preview
			$this->add_form_field('title', 'left')->tab('Request');
			$this->add_form_field('user', 'right')->tab('Request');
			$this->add_form_field('status','left')->preview_no_relation()->tab('Request');
			$this->add_form_field('expired_at','right')->tab('Request');
			$this->add_form_field('description')->tab('Request');
			$this->add_form_field('description_extra')->tab('Request');
			$this->add_form_field('type', 'left')->tab('Request')->display_as(frm_radio);
			$this->add_form_field('categories', 'right')->tab('Request');

			//$this->add_form_field('city', 'left')->tab('Request');
			//$this->add_form_field('zip', 'right')->tab('Request');
			//$this->add_form_field('country', 'left')->tab('Request')->preview_no_relation();
			//$this->add_form_field('state', 'right')->tab('Request')->preview_no_relation();

			// Quotes
			$this->add_form_field('quotes')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('status', 'comment', 'price'),
				'search_enabled' => true,
				'search_fields' => array('@price', '@comment'),
				'search_prompt' => 'find quotes by comment',
				'no_data_message' => 'This request has no quotes',
				'control_panel' => 'quote_control_panel',
				'is_editable' => true,
				'form_title' => 'Quote',
				'form_context' => 'create'
			))->tab('Quotes');

			// Questions
			$this->add_form_field('questions')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('description'),
				'search_enabled' => true,
				'search_fields' => array('@description'),
				'search_prompt' => 'find questions by description',
				'no_data_message' => 'This request has no questions',
				'control_panel' => 'question_control_panel',
				'is_editable' => true,
				'form_title' => 'Question',
				'form_context' => 'create'
			))->tab('Questions');  

			// Answers
			$this->add_form_field('answers')->display_as(frm_widget, array(
				'class'=>'Db_List_Widget', 
				'columns' => array('description'),
				'search_enabled' => true,
				'search_fields' => array('@description'),
				'search_prompt' => 'find answers by description',
				'no_data_message' => 'This request has no answers',
				'control_panel' => 'answer_control_panel',
				'is_editable' => true,
				'form_title' => 'Answer',
				'form_context' => 'create'
			))->tab('Questions');  

		}

		// Extensibility
		Phpr::$events->fire_event('service:on_extend_request_form', $this, $context);
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
		$result = Phpr::$events->fire_event('service:on_get_request_field_options', $db_name, $current_key_value);
		foreach ($result as $options)
		{
			if (is_array($options) || (strlen($options && $current_key_value != -1)))
				return $options;
		}

		return false;
	}

	// Events
	//

	public function before_save($session_key = null)
	{
		Phpr::$events->fire_event('service:on_before_save_request', $this, $session_key);

		if (!$this->type)
			$this->type = self::type_open;
	}

	public function after_delete()
	{
		$files = Db_File::create()->where('master_object_class=?', get_class($this))->where('master_object_id=?', $this->id)->find_all();
		foreach ($files as $file)
			$file->delete();

		Db_Helper::query('delete from service_categories_requests where request_id=:id', array('id'=>$this->id));
	}

	public function after_create($session_key = null)
	{
		$this->url_name = $url_name = substr(Phpr_Inflector::slugify($this->title . ' ' . $this->id), 0, 150);
		Db_Helper::query("update service_requests set url_name=:url_name where id=:id", array('url_name'=>$url_name, 'id'=>$this->id));
		self::geocode_address($this->id);
		// Deferred geocoding
		Phpr_Cron::queue_job('Service_Request::geocode_address', array($this->id));
	}

	public function after_update()
	{
		// Deferred geocoding
		if ((isset($this->fetched['city']) && $this->city != $this->fetched['city']) 
			|| (isset($this->fetched['zip']) && $this->zip != $this->fetched['zip']))
			self::geocode_address($this->id);
			Phpr_Cron::queue_job('Service_Request::geocode_address', array($this->id));
	}

	// Options
	//

	public function get_type_options($key_value = -1)
	{
		$options = array(
			self::type_open => 'Public',
			self::type_private => 'Invite Only',
			self::type_sealed => 'Sealed Bidding'
		);

		if ($key_value == -1)
			return $options;
		else if (array_key_exists($key_value, $options))
			return $options[$key_value];
		else
			return '???';
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

	// Where user owns the request
	public function apply_owner($user)
	{
		$this->where('user_id=?', $user->id);
		return $this;
	}

	// Where request can be shown to the public
	public function apply_visibility()
	{
		$this->apply_status(Service_Status::status_active);
		$this->where('type=:open OR type=:sealed', array(
			'open' => self::type_open,
			'sealed' => self::type_sealed
		));
		return $this;
	}

	public function apply_status($code)
	{
		if (!is_array($code))
			$code = array($code);

		$this->join('service_statuses', 'service_statuses.id = service_requests.status_id');
		return $this->where('service_statuses.code in (?)', array($code));
	}

	// Where request is in categories
	public function apply_category($category=array())
	{
		if (is_string($category))
			$category = array($category);

		$this->from('service_requests', 'distinct service_requests.*');
		$this->join('service_categories_requests', 'service_categories_requests.request_id = service_requests.id');
		$this->where('service_categories_requests.category_id in (?)', array($category));
		return $this;
	}

	// Where a user has a quote
	public function apply_user_quote($user=null)
	{
		$this->join('service_quotes', 'service_quotes.request_id = service_requests.id');

		if ($user)
			$this->where('service_quotes.user_id=?', $user->id);

		return $this;
	}

	// Where user has a quote with/without status
	public function apply_user_quote_with_status($user=null, $status_code=null, $is_negative_search=false)
	{
		$this->apply_user_quote($user);

		if ($status_code)
		{
			$this->join('service_quote_statuses', 'service_quote_statuses.id = service_quotes.status_id');

			if ($is_negative_search)
				$this->where('service_quote_statuses.code!=?', $status_code);
			else
				$this->where('service_quote_statuses.code=?', $status_code);
		}

		return $this;
	}

	// Where user has a question
	public function apply_user_question($user=null)
	{
		$this->join('service_questions', 'service_questions.request_id = service_requests.id');

		if ($user)
			$this->where('service_questions.user_id=?', $user->id);

		return $this;
	}

	// Where provider categories match request category
	// $provider_id - provider id's
	// $user_id - user context, don't return requests from this user
	public function apply_provider_categories($provider_id, $user_id=null)
	{
		if (!is_array($provider_id))
			$provider_id = array($provider_id);

		$this->join('service_categories_requests', 'service_categories_requests.request_id = service_requests.id');
		$this->join('service_categories_providers', 'service_categories_providers.category_id = service_categories_requests.category_id');
		$this->where('service_categories_providers.provider_id in (?)', array($provider_id));

		if (!$this->has_group())
			$this->group('service_requests.id');

		if ($user_id)
			$this->where('service_requests.user_id!=?', $user_id);

		return $this;
	}

	// Where a user has been invited or has ignored
	public function apply_provider_link($provider_id, $link_type, $is_negative_search=false)
	{
		if (!is_array($provider_id))
			$provider = array($provider_id);

		$bind = array(
			'provider_id' => $provider_id,
			'link_type' => $link_type
		);

		$this->join('service_requests_providers', 'service_requests_providers.request_id = service_requests.id', '', 'left outer');

		// Ban filter
		if ($is_negative_search)
			$this->where('service_requests_providers.type!=:link_type OR service_requests_providers.provider_id is null', $bind);
		else
			$this->where('service_requests_providers.type=:link_type', $bind);

		if (!$this->has_group())
			$this->group('service_requests.id');

		return $this;
	}

	// Where provider has permission to access the request
	public function apply_provider_visibility($provider_id)
	{
		if (!is_array($provider_id))
			$provider_id = array($provider_id);

		$bind = array(
			'provider_ids' => array($provider_id),
			'open' => self::type_open,
			'sealed' => self::type_sealed,
			'private' => self::type_private,
			'invited' => self::link_type_invited
		);
		$this->where('service_requests.type=:open OR service_requests.type=:sealed OR (service_requests.type=:private AND  
			(select count(*) from service_requests_providers 
				where service_requests_providers.request_id = service_requests.id 
				and service_requests_providers.provider_id in (:provider_ids) 
				and service_requests_providers.type=:invited) > 0
		)', $bind);

		return $this;
	}

	// Find any outstanding ratings for a user
	public function apply_user_todo_ratings($user)
	{
		$this->where('(select count(*) from service_ratings where request_id = service_requests.id and user_from_id = ?) = 0', $user->id);
		return $this;
	}


	// Service methods
	//

	public function set_status($status_code)
	{
		$status = Service_Status::create()->find_by_code($status_code);
		if (!$status)
			return $this;

		$this->status = $status;
		$this->status_id = $status->id;
		$this->status_code = $status->code;
		return $this;
	}

	public static function count_user_requests($user)
	{
		return Db_Helper::scalar('select count(*) from service_requests where user_id=:id', array('id'=>$user->id));
	}

	public function get_url($page=null, $add_hostname=false)
	{
		if (!$page) 
			$page = Cms_Page::get_url_from_action('service:request');

		return root_url($page.'/'.$this->url_name, $add_hostname);
	}

	public function get_remaining_time($abbreviated=false)
	{
		if (!$this->expired_at)
			return null;

		if ($abbreviated)
			$string = Phpr_DateTime::interval_as_string($this->expired_at->substract_datetime(Phpr_DateTime::now()), 'd', 'h', 'm', __('less than a minute', true));
		else
			$string = $this->expired_at->substract_datetime(Phpr_DateTime::now())->interval_as_string();

		return $string;
	}

	public function get_extra_description()
	{
		// preg_replace removes non-UTF8 characters causing JSON decode to fail
		return $this->description_extra ? json_decode(preg_replace('/[^(\x20-\x7F)]*/','', $this->description_extra)) : array();
	}

	public function add_extra_description($description)
	{
		$extra_desc = $this->description_extra;
		if (!$extra_desc)
			$extra_desc = array();
		else
			$extra_desc = json_decode($extra_desc);

		$extra_desc[] = array('created_at' => Phpr_DateTime::now()->format(Phpr_DateTime::universal_datetime_format), 'description' => $description);

		$this->description_extra = json_encode($extra_desc);
	}

	public function set_notify_vars(&$template, $prefix='')
	{
		$template->set_vars(array(
			$prefix.'status'   => $this->status->name,
			$prefix.'title'    => $this->title,
			$prefix.'location' => $this->location_string,
			$prefix.'link'     => $this->get_url(null, true),
			$prefix.'url'      => $this->get_url(null, true),
		));
	}

	public static function geocode_address($id)
	{
		$obj = self::create()->find($id);
		if (!$obj)
			return;

		Location_Geocode::geocode_to_object($obj, $obj->address_string);
		$bind = array('lng'=>$obj->longitude, 'lat'=>$obj->latitude, 'id'=>$obj->id);
		Db_Helper::query("update service_requests set latitude=:lat, longitude=:lng where id=:id", $bind);
	}

	// Provider Linkage
	// 

	public function link_provider($provider, $type)
	{
		if ($provider instanceof Service_Provider)
			$provider = $provider->id;

		$bind = array(
			'request_id' => $this->id,
			'provider_id' => $provider,
			'type' => $type,
			'datetime' => Phpr_DateTime::now()->to_sql_datetime()
		);
				
		Db_Helper::query('insert into service_requests_providers (provider_id, request_id, type, created_at) 
			values (:provider_id, :request_id, :type, :datetime) 
			on duplicate key update created_at =:datetime, type =:type', $bind);
	}

	public function provider_has_link($provider, $type)
	{
		if (!$provider)
			return false;

		return Db_Helper::scalar('select count(*) from service_requests_providers where request_id=:request_id and provider_id=:provider_id and type=:type', array(
			'type'=>$type,
			'provider_id'=>$provider->id,
			'request_id'=>$this->id
		));
	}

	public function has_provider_quoted($provider)
	{
		return Db_Helper::scalar('select count(*) from service_quotes where request_id=:request_id and provider_id=:provider_id', array(
			'provider_id'=>$provider->id,
			'request_id'=>$this->id
		));
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
		$str .= (strlen(trim($this->street_addr))) ? $this->street_addr . ', ' : '';
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
		$str .= ($this->city) ? $this->city : $this->zip;
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

	public function eval_display_name()
	{
		return ($this->user) ? $this->user->username : null;
	}

	public function eval_description_summary()
	{
		return Phpr_String::limit_words($this->description, 35);
	}
}
