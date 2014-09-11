<?php

class Cms_Demo 
{
	private static $module_name = null;

	public static function refresh($module_name=null)
	{
		if (!$module_name)
			return;

		self::$module_name = $module_name;

		// Init script     
		$init_path =  PATH_APP.'/modules/'.$module_name.'/demo/init.php';
		if (file_exists($init_path))
			include $init_path;

		// Execute sql
		self::load_sql();
	}

	public static function load_sql()
	{
		$sql_path = PATH_APP.'/modules/'.self::$module_name.'/demo/sql';

		if (!file_exists($sql_path))
			continue;

		$iterator = new DirectoryIterator($sql_path);

		foreach ($iterator as $file)
		{ 
			if (!$file->isDir() && preg_match('/^_[^\.]*\.sql$/i', $file->getFilename()))
				Db_Helper::execute_sql_from_file($sql_path.'/'.$file->getFilename());
		}
	}
}