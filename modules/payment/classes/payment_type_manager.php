<?php

class Payment_Type_Manager
{
	private static $_object_cache = null;
	private static $_class_cache = null;

	public static function get_payment_type_class_names()
	{
		if (self::$_class_cache !== null)
			return self::$_class_cache;

		$types_path = PATH_APP."/modules/payment/drivers/payment_gateways";
		$iterator = new DirectoryIterator($types_path);
		foreach ($iterator as $file)
		{
			$file_name = $file->getFilename();
			$file_path = $types_path.'/'.$file_name;

			if (is_dir($file_path))
				continue;

			if (substr($file_name, 0, 5) == 'payment_' && substr($file_name, -4) == '.php')
				require_once($types_path.'/'.$file->getFilename());
		}

		$modules = Core_Module_Manager::get_modules();
		foreach ($modules as $module_id => $module_info)
		{
			$class_path = PATH_APP."/modules/".$module_id."/drivers/payment_gateways";
			if (file_exists($class_path))
			{
				$iterator = new DirectoryIterator($class_path);

				foreach ($iterator as $file)
				{
					if (!$file->isDir() && preg_match('/^'.$module_id.'_[^\.]*\.php$/i', $file->getFilename()))
						require_once($class_path.'/'.$file->getFilename());
				}
			}
		}

		$classes = get_declared_classes();
		self::$_class_cache = array();
		foreach ($classes as $class)
		{
			if (preg_match('/_Gateway$/i', $class) && get_parent_class($class) == 'Payment_Type_Base')
				self::$_class_cache[] = $class;
		}

		return self::$_class_cache;
	}

	public static function get_payment_types()
	{
		if (self::$_object_cache !== null)
			return self::$_object_cache;

		$type_objects = array();
		foreach (self::get_payment_type_class_names() as $class_name)
			$type_objects[] = new $class_name();
		
		return self::$_object_cache = $type_objects;
	}

	// Partials
	// 

	public static function create_partials()
	{
		$partial_list = Db_Helper::object_array('select name, theme_id from cms_partials');
		$partials = array();

		foreach ($partial_list as $partial)
		{
			if (!$partial->theme_id)
				$partial->theme_id = 0;

			if (!array_key_exists($partial->theme_id, $partials))
				$partials[$partial->theme_id] = array();

			$partials[$partial->theme_id][$partial->name] = $partial;
		}

		$payment_types = Payment_Type::create()->find_all();

		foreach ($payment_types as $payment_type)
		{
			$class = $payment_type->class_name;

			if (preg_match('/_Gateway$/i', $class) && get_parent_class($class) == 'Payment_Type_Base')
			{
				$pos = strpos($class, '_');
				$payment_type_file = strtolower(substr($class, $pos+1, -8));
				$payment_partial_name = 'payment:'.$payment_type_file;
				$class_info = null;

				foreach ($partials as $theme_id=>$partial_list)
				{
					$payment_partial_exists = array_key_exists($payment_partial_name, $partial_list);

					if (!$payment_partial_exists)
					{
						$class_info = $class_info ? $class_info : new ReflectionClass($class);

						if (!$payment_partial_exists)
						{
							$file_path = dirname($class_info->getFileName()).'/'.strtolower($class).'/cms_partial.htm';
							self::create_partial_from_file($payment_partial_name, $file_path, $theme_id);
						}
					}
				}
			}
		}
	}

	protected static function create_partial_from_file($name, $file_path, $theme_id)
	{
		if (file_exists($file_path))
		{
			if ($theme_id == 0)
				$theme_id = null;

			$partial = Cms_Partial::create();
			$partial->ignore_file_copy = true;
			$partial->name = $name;
			$partial->theme_id = $theme_id;
			$partial->content = file_get_contents($file_path);
			$partial->save();
		}
	}
}