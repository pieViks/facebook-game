<?php
/**
 * EVA FrameWork
 * @author 	Elvin Verlaat <elvin@isioux.nl>
 * @date	19/04/2011
 * @file	url.php
 * @package helpers
 *
 * The url helpers supply fast shortcuts for commen used url options
 */
function surl($controller = false, $method = false, $args = false)
{
	$sn = $_SERVER['SERVER_NAME'];

	if(config('application', 'ssl', false)) {
		$url = "https://";
	} else {
		$url = "http://";
	}

	$addExtension = false;
	$url.= config('application', 'domain', $_SERVER['SERVER_NAME']);

	if($controller !== false) {
		$url.= "/".$controller;
		$addExtension = true;
	}
	if($method !== false) {
		$url.= "/".$method;
		$addExtension = true;
	}
	if($args !== false) {
		foreach($args as $a) {
			$url.= "/".$a;
		}
		$addExtension = true;
	}

	if($addExtension == true)
	{
		$url.= config('application', 'extension', '.php');
	}
	return $url;
}

function sredirect($controller = false, $method = false, $args = false)
{
	$url = surl($controller, $method, $args);
	redirect($url);
}

function redirect($url)
{
	header("Location: ".$url);
	exit;
}

function srefresh($args = true)
{
	global $_route;

	if($args) {
		$args = $_route['arguments'];
	}
	sredirect($_route['controller'],$_route['method'],$args);
}

function get_route()
{
	global $_route;
	return $_route;
}