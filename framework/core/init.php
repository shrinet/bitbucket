<?php

// Fix for XDebug aborting threads > 100 nested
ini_set('xdebug.max_nesting_level', 300);

//
// PHP functions that should really exist
//

// Creates a unified class id
if ( ! function_exists('get_class_id')) 
{
	function get_class_id($obj)
	{
		if (is_string($obj))
			$class_name = $obj;
		else
			$class_name = get_class($obj);

		$class_name = str_replace('\\', '_', $class_name);
		return $class_name;
	}
}

// Obtains an object class name without namespaces
if ( ! function_exists('get_real_class')) 
{
	function get_real_class($obj) 
	{
		if (!is_string($obj))
			$class_name = get_class($obj);
		else
			$class_name = $obj;

		if (preg_match('@\\\\([\w]+)$@', $class_name, $matches)) {
			$class_name = $matches[1];
		}

		return $class_name;
	}
}

//
// Configuration
//

// Load initial configuration. This is included again below.
if ($path = realpath(PATH_APP . '/config/config.php'))
	include($path); 

// Core PHPR class
require_once('phpr.php');

//
// Initialize auto class loading
// 

require_once('classloader.php');

Phpr::$class_loader = new Phpr\ClassLoader();

function phpr_autoload_internal($name) 
{
	if (!Phpr::$class_loader->load($name)) 
	{
		// Load failed
	}
}

if (function_exists('spl_autoload_register')) 
{
	spl_autoload_register('phpr_autoload_internal');
} 
else 
{
	function __autoload($name) 
	{
		phpr_autoload_internal($name);
	}
}

// Exception handling
require_once('exceptions.php');

// Process resource requests (may terminate thread)
Phpr_Response::process_resource_request();

// Event handling
Phpr::$events = new Phpr_Events();

// Reponse object
Phpr::$response = new Phpr_Response();

// Session handling
Phpr::$session = new Phpr_Session();

// Security system
Phpr::$security = new Phpr_Security();

// Internal deprecation
Phpr::$deprecate = new Phpr_Deprecate();

// Configure the application and initialize the request object
Phpr::$router = new Phpr\Router();

// Load config for usage
if ($path = realpath(PATH_APP . '/config/config.php'))
	include($path);

// Application controller
if ($path = Phpr::$class_loader->find_path('controllers/application.php'))
	include_once($path);

// Initialize script
if ($path = Phpr::$class_loader->find_path('init/init.php'))
	include_once($path);

// Config object
Phpr::$config = new Phpr_Config();

// Request object
Phpr::$request = new Phpr_Request();

// Error log
Phpr::$error_log = new Phpr_Error_Log();

// Trace log	
Phpr::$trace_log = new Phpr_Trace_Log();

// Localization
Phpr::$locale = new Phpr_Locale();

// Run modules initialization scripts
//

function init_phpr_modules() 
{
	$paths = Phpr::$class_loader->find_paths('modules');

	foreach ($paths as $path) 
	{
		$iterator = new DirectoryIterator($path);
		
		foreach ($iterator as $directory) 
		{
			if (!$directory->isDir() || $directory->isDot())
				continue;

			if ( ! file_exists($module_file = $directory->getPathname() . '/classes/module.php') 
				&& ! file_exists($module_file = $directory->getPathname() . '/classes/' . basename($directory->getPathname()) . '_module.php'))				
				continue;
			
			if (!file_exists($init_dir = $directory->getPathname() . '/init'))
				continue;
			
			$file_iterator = new DirectoryIterator($init_dir);
			
			foreach ($file_iterator as $file) 
			{
				if (!$file->isFile())
					continue;
					
				$info = pathinfo($file->getPathname());

				if (isset($info['extension']) && $info['extension'] == PHPR_EXT)
					include($file->getPathname());
			}
		}
	}
}

init_phpr_modules();

Phpr::$session->restore_db_data();

// Execute requested action 
if (empty($PHPR_INIT_ONLY))
	Phpr::$response->open(Phpr::$request->get_current_uri(true));
