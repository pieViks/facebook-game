<?php if(!defined('PATH_SYSTEM')) exit;

// check if the needed defines are set
if(!defined('PATH_APPLICATION')) exit('Error: PATH_APPLICATION not set');

// Check if the application directory can be found
if(!is_dir(PATH_APPLICATION)) exit('Error: Application directory "'.PATH_APPLICATION.'" could not be found');

// Set the current microtime for speed messurements
$starttime = microtime(true);

// Load the system helpers
foreach(glob(PATH_SYSTEM.'helper/*.php') as $helper_file) include $helper_file;

// Determine route ( controller -> method -> arguments )
$requestString 	= config('application', 'request_string', '');
$requestExpl	= explode('/', $requestString);

$_route = array(
	'controller' 	=> (isset($requestExpl[0]) && $requestExpl[0] != '') ? $requestExpl[0] : config('application', 'default_controller'),
	'method'		=> (isset($requestExpl[1]) && $requestExpl[1] != '') ? $requestExpl[1] : config('application', 'default_method'),
	'arguments'		=> (isset($requestExpl[2])) ? array_slice($requestExpl, 2) : array()
);

// start php session if required
if(config('application', 'use_php_session', false)) {
	session_start();
}


// Set requested controller
$controllerClass = $_route['controller'];
$controllerFile	 = strtolower($_route['controller']).'.php';
if(!file_exists(PATH_APPLICATION.'controller/'.$controllerFile))
{
	exit('Fatal Error: Controller '.$controllerClass.' not found');
}

// Load Eva core classes
require PATH_SYSTEM.'core/eva_controller.php';
require PATH_SYSTEM.'core/eva_loader.php';
require PATH_SYSTEM.'core/eva_model.php';
require PATH_SYSTEM.'core/eva_automodel.php';
require PATH_SYSTEM.'core/eva_library.php';

// Load called controller
require PATH_APPLICATION.'controller/'.$controllerFile;

$evaController 	= new $controllerClass();


// FOR BENCHMARK
if(config('application', 'benchmark', false)) {
	require PATH_SYSTEM.'core/eva_benchmark.php';
	$evaController->benchmark = new Benchmark();
	$evaController->benchmark->setStartTime($starttime);
	$evaController->benchmark->addTimeLabel('Controller Loaded');
}

// call route method
$result 		= call_user_func_array(array($evaController, $_route['method']), $_route['arguments']);



