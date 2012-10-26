<?php
class Gatekeeper extends Controller
{
	public function Gatekeeper()
	{
		parent::Controller();

	}

	public function login()
	{
		eva()->library->load('auth', array('attempt' => true));
		$result = array('success' => l('auth')->attempt_login());

		header('Content-type: application/json');
		print json_encode($result);
	}

	public function logout()
	{
		session_destroy();
	}
}