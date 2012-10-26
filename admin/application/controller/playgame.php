<?php
class Playgame extends Controller
{
	public function Playgame()
	{
		parent::Controller();

		l('auth');
	}

	public function user($pkUserID)
	{
		$data 	= array();
		$data['api_root'] 		= 'http://api.gopdev002.isioux.nl/';
		$data['environment'] 	= 'facebook_admin';

		$data['token'] 			= m('player_model')->newAdminToken($pkUserID);


		print view('playgame/user', $data);
	}

}