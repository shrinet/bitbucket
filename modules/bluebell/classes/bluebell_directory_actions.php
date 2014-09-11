<?php

class Bluebell_Directory_Actions extends Cms_Action_Base
{

	const default_mode = 'letter';
	const default_filter = 'top';

	public function directory()
	{
		$type = $this->request_param(0, 'l');

		// Short hand routing
		switch ($type)
		{
			case 'a': 
				$mode = 'area'; 
				break;
			case 'browse': 
			case 'b': 
				$mode = 'browse'; 
				break;
			case 'l': 
				$mode = 'letter'; 
				break;
			default: 
				$mode = self::default_mode;
				break;
		}

		$parent_mode = $mode;

		if ($mode=="area") // Area
		{
			$_POST['country'] = $country = $this->request_param(1);
			$_POST['state'] = $state = $this->request_param(2);
			$_POST['city'] = $city = $this->request_param(3);
			$_POST['role'] = $role_name = $this->request_param(4);
			$_POST['filter'] = $filter = $this->request_param(5, self::default_filter);

			if ($country && !$state)
				$mode = 'country';

			if ($country && $state && !$city)
				$mode = 'state';

			if ($country && $state && $city && !$role_name)
				$mode = 'city';
			
			if ($country && $state && $city && $role_name)
				$mode = 'role';
		}
		else if ($mode=="browse")
		{
			$_POST['category'] = $category = $this->request_param(1);
			$_POST['role'] = $role = $this->request_param(2);
			$_POST['filter'] = $filter = $this->request_param(3, self::default_filter);

			$mode = 'browse';

			if ($category)
				$mode = 'category';
			
			if ($category && $role)
				$mode = 'role';
		}
		else if ($mode=="letter")
		{
			$letter = $this->request_param(1, 'a');
			$_POST['letter'] = $letter;
		}

		$_POST['parent_mode'] = $parent_mode;
		$_POST['mode'] = $mode;
		$this->on_directory();
	}

	public function on_directory()
	{
		
		$parent_mode = post('parent_mode', self::default_mode);
		$mode = post('mode', self::default_mode);
		$providers = Service_Provider::create();

		if ($parent_mode == 'area')
			$providers = $this->_directory_by_location($providers);
		else if ($parent_mode == 'browse')
			$providers = $this->_directory_by_browse($providers);
		else if ($parent_mode == 'letter')
			$providers = $this->_directory_by_letter($providers);

		
		$this->data['parent_mode'] = $parent_mode;
		$this->data['mode'] = $mode;
		$this->data['providers'] = $providers;
	}

	// Letter Index
	// 

	private function _directory_by_letter($providers)
	{
		extract($this->_directory_variables());

		// Letter
		if ($mode == 'letter')
		{
			$providers->where("business_name LIKE :letter", array('letter'=>$letter.'%'));
			$this->data['current_letter'] = $letter;
		}
		
		$this->data['current_letter'] = $letter;
		$this->data['dir_url'] = root_url('directory/l');
		return $providers;
	}

	// Browse
	// 

	private function _directory_by_browse($providers)
	{
		extract($this->_directory_variables());

		// Browse
		if ($mode == 'browse')
		{
			$categories = Service_Category::create()->list_root_children();
			$this->data['categories'] = $categories;
			$this->page->title_name = __('Browse categories');
		}

		// Browse: Category
		if ($mode == 'category')
		{
			$roles = null;
			$browse_category = Service_Category::create()->find_by_url_name($category);
			if ($browse_category)
			{
				$this->page->title_name = $browse_category->name;
				$browse_roles = $browse_category->list_children();
			}
			$this->data['roles'] = $browse_roles;
			$this->data['category'] = $browse_category;
		}
		
		if ($mode == 'role')
		{
			$browse_category = Service_Category::create()->find_by_url_name($category);
			$browse_role = Service_Category::create()->find_by_url_name($role);
			
			if ($browse_role)
				$providers->apply_category($browse_role->id);

			$this->page->title_name = Phpr_Inflector::pluralize($browse_role->name);

			$this->_directory_apply_filter($providers, $filter);

			$this->data['category'] = $browse_category;
			$this->data['role'] = $browse_role;
			$this->data['filter'] = $filter;
			$this->data['country'] = null;
			$this->data['state'] = null;
			$this->data['city'] = null;
		}

		$this->data['dir_url'] = root_url('directory/b');
		return $providers;
	}

	// Location
	// 
	
	private function _directory_by_location($providers)
	{
		extract($this->_directory_variables());

		// Location: Country 
		if ($mode == "country"||$mode=="state"||$mode=="city"||$mode == "role")
		{
			$location_country = Location_Country::create()->find_by_code($country);
			if ($location_country)
			{
				$providers->where('country_id=?', $location_country->id);
				$this->page->title_name = __('Providers located in %s', $location_country->name);                
			}
			$this->data['country'] = $location_country;
		}

		// Location: State
		if ($mode == "state"||$mode=="city"||$mode == "role")
		{
			$location_state = Location_State::create()->where('country_id=?', $location_country->id)->find_by_code($state);
	
			if ($location_state)
			{
				$providers->where('state_id=?', $location_state->id);
				$this->page->title_name = __('Providers located in %s', $location_state->name);
			}

			$this->data['state'] = $location_state;
			$cities = Bluebell_Directory_City::create()->find_area($location_country, $location_state)->find_all();
			$this->data['cities'] = $cities;
		}

		// Location: City
		if ($mode == "city"||$mode == "role")
		{
			$location_city = Bluebell_Directory_City::create()->find_area($location_country, $location_state, $city)->find();

			$roles = null;
			if ($location_city)
			{
				$roles = Db_Helper::object_array('select distinct service_categories.name, service_categories.id from service_categories 
					left join service_categories_providers on service_categories_providers.category_id = service_categories.id 
					left join service_providers on service_providers.id = service_categories_providers.provider_id 
					left join bluebell_provider_zip on bluebell_provider_zip.zip = service_providers.zip
					where bluebell_provider_zip.zip = :zip', array('zip'=>$location_city->zip));
				
				$this->page->title_name = __('Providers located in %s', $location_city->name. ', ' . $location_state->code);
			}

			$this->data['city'] = $location_city;
			$this->data['roles'] = $roles;
		}

		// Location: Role
		if ($mode == "role")
		{
			$location_role = Service_Category::create()->find_by_url_name($role);
			
			if (!$location_role)
				throw new Phpr_SystemException('Unable to find service category for role: '.$role);

			$providers->apply_category($location_role->id);
			$this->page->title_name = __('%s located in %s', array(Phpr_Inflector::pluralize($location_role->name), $location_city->name. ', ' . $location_state->code));

			$this->_directory_apply_filter($providers, $filter);

			$this->data['role'] = $location_role;
			$this->data['filter'] = $filter;
		}

		$this->data['dir_url'] = root_url('directory/a');
		return $providers;
	}

	private function _directory_variables()
	{
		$arr = array();
		$arr['parent_mode'] = post('mode', 'letter');
		$arr['mode'] = post('mode', 'letter');
		$arr['letter'] = post('letter', 'a');
		$arr['country'] = post('country');
		$arr['state'] = post('state');
		$arr['city'] = post('city');
		$arr['role'] = post('role');
		$arr['category'] = post('category');
		$arr['filter'] = post('filter', self::default_filter);
		return $arr;    
	}

	private function _directory_apply_filter($providers, $filter)
	{
		switch ($filter)
		{
			// @todo Finish these filters
			case "top":
			case "cheap":
			case "reliable":
				$providers->find_top_providers();
			break;
		}

		return $providers;
	}

	public function on_directory_search()
	{
		try {
			// Attempt to geocode address
			$address = post('address');

			if (!$address)
				throw new Exception('Missing address');

			$geocode = Location_Geocode::from_address($address);

			if (!$geocode->getCountryCode())
				throw new Exception('Address lookup failed...');

			$country = Location_Country::create()->find_by_code($geocode->getCountryCode());
			if (!$country)
				throw new Exception('Could not find a country with code: '.$geocode->getCountryCode());

			$state = Location_State::create()->find_by_name($geocode->getRegion());
			if (!$state)
				throw new Exception('Could not find a state by the name of: '.$geocode->getRegion().', '.$geocode->getCountryCode());

			$_POST['state'] = $state->code;
			$_POST['country'] = $country->code;
			$_POST['city'] = Phpr_Inflector::slugify($geocode->getCity());
			$_POST['parent_mode'] = 'area';
			$_POST['mode'] = 'city';
			$this->on_directory();

		}
		catch (Exception $ex)
		{
			throw new Cms_Exception($ex->getMessage());
		}
	}

	public function on_directory_create_request()
	{
		if (!post('open_request'))
			$_POST['Request']['type'] = Service_Request::type_private;

		$_POST['Request']['title'] = post_array('Request', 'role_name');
		$this->exec_ajax_handler('bluebell:on_create_request', $this);
	}

}