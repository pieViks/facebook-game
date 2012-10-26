<?php
class Desktop extends Controller
{
	public function Desktop()
	{
		parent::Controller();
		
		l('auth');
	}

	public function index()
	{
		print view('desktop/index');
	}

	public function somehtml() {

		print "HELLO WORLD";
	}

}