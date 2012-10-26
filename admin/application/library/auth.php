<?php
class Auth
{
	public function Auth( $config )
	{
		// set config
		// foo = bar

		// check if current session is logged in
		if(!isset($config['attempt']) || $config['attempt'] != true)
		{
			$this->_check_if_logged_in();
		}
	}

	public function attempt_login()
	{
		list($user, $pass) = $this->_get_current_user_pass(true);

		if($user != null || $pass != null) {
			return $this->_attempt_login($user, $pass);
		}

		return false;
	}

	private function _attempt_login($user, $pass)
	{
		if($user == 'log' && $pass == sha1('3lv1n15d3m@n'))
		{
			$_SESSION['username'] 	= $user;
			$_SESSION['password'] 	= $pass;

			return true;
		}

		return false;
	}

	private function _check_if_logged_in()
	{
		list($user, $pass) = $this->_get_current_user_pass();

		if($user === null || $pass === null) {
			$this->_show_login();
		}

		$result = $this->_attempt_login($user, $pass);

		if($result == false) {
			$this->_show_login();
		}
	}

	private function _get_current_user_pass($allowPost = false)
	{
		if($allowPost && isset($_POST['username']) && isset($_POST['password']))
		{
			return array( $_POST['username'], sha1($_POST['password']) );
		}
		else if(isset($_SESSION['username']) && isset($_SESSION['password']))
		{
			return array( $_SESSION['username'], $_SESSION['password'] );
		}

		return false;
	}

	private function _show_login()
	{
		print view('auth/login');

		exit;
	}
}