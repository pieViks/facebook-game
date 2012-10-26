<?php
function view($class, $data = array(), $file = null)
{
	return eva()->view->load($class, $data, $file);
}

function l($library = null)
{
	if($library !== null) {
		return eva()->library->$library;
	} else {
		return eva()->library;
	}
}

function m($model = null)
{
	if($model !== null) {
		return eva()->model->$model;
	} else {
		return eva()->model;
	}
}

function c($connection = null)
{
	if($connection !== null) {
		return eva()->connection->$connection;
	} else {
		return eva()->connection;
	}
}