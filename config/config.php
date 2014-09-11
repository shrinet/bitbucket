<?php

/**
 * Core Config File
 *
 * You should not need to update this
 */

if (!isset($CONFIG))
	$CONFIG = array();

$CONFIG['APP_NAME'] = "womBids";
$CONFIG['ADMIN_URL'] = '/admin';

if (!defined('PATH_APP'))
	return;

$CONFIG['DEV_MODE'] = false;

// Database settings
//
$CONFIG['DB_CONNECTION'] = array(
    'host'        => 'localhost',
    'database'    => 'wombids',
    'username'    => 'rajiv_wombids',
    'password'    => 'Admin123@',
    'locale'      => 'utf8'
);

// Show friendly errors on the public site
//
$CONFIG['SHOW_FRIENDLY_ERRORS'] = false;
$CONFIG['DISPLAY_ERROR_LOG_ID'] = false;
$CONFIG['DISPLAY_ERROR_LOG_STRING'] = false;


// Tracing and error logging features
//
$CONFIG['ERROR_LOG'] = true;
$CONFIG['ERROR_REPORTING'] = E_ALL | E_STRICT;
$CONFIG['ERROR_IGNORE'] = array('Phpr_ApplicationException', 'Phpr_DeprecateException');
$CONFIG['LOG_TO_DB'] = true;
$CONFIG['ENABLE_ERROR_STRING'] = false;

if (!isset($CONFIG['TRACE_LOG']['DEBUG']))
    $CONFIG['TRACE_LOG']['DEBUG'] = PATH_APP . '/logs/debug.txt';

// Redirecting and cookies
//
$CONFIG['REDIRECT'] = 'location';
$CONFIG['FRONTEND_AUTH_COOKIE_LIFETIME'] = 5;
$CONFIG['CONFIG_AUTH_COOKIE_LIFETIME'] = 1;

// Files
//
$CONFIG['FILESYSTEM_CODEPAGE'] = 'Windows-1251';

// Language
//
$CONFIG['LANGUAGE'] = 'en';

// ImageMagick
//
$CONFIG['IMAGEMAGICK_ENABLED'] = false;
$CONFIG['IMAGEMAGICK_PATH'] = null;
$CONFIG['IMAGE_JPEG_QUALITY'] = 85;

// System time zone
//
$CONFIG['TIMEZONE'] = 'America/Sao_Paulo';

// File and folder permissions
//
$CONFIG['FILE_PERMISSIONS'] = 0777;
$CONFIG['FOLDER_PERMISSIONS'] = 0777;

// URL Separator
//
$CONFIG['URL_SEPARATOR'] = '-';

// Cron access
//
$CONFIG['CRON_ALLOWED_IPS'] = array();

