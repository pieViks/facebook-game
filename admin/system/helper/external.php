<?php
function saveget($item)
{
	return (isset($_GET[$item])) ? $_GET[$item] : null;
}

function savepost($item)
{
	return (isset($_POST[$item])) ? $_POST[$item] : null;
}