<?php if(!defined('PATH_SYSTEM')) exit;

// mandatory system settings
$config['default_controller'] 		= 'desktop';
$config['default_method']			= 'index';
$config['request_string']			= saveget('request');

// session
$config['use_php_session']			= true;

// database
$config['auto_connect']				= true;
$config['auto_connect_file']		= 'database';
$config['auto_connect_name']		= 'db';

// debug
$config['printr']					= true;
