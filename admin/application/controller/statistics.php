<?php
class Statistics extends Controller
{
	public function Statistics()
	{
		parent::Controller();

		l('auth');
	}


	public function onlineuserslive($getdata = false)
	{
		if($getdata != false)
		{
			$online = m('statistics_model')->getOnlineUsers();

			$online['ranch'] = (isset($online['ranch'])) ? $online['ranch'] : 0;
			$online['poker'] = (isset($online['poker'])) ? $online['poker'] : 0;

			print json_encode($online);
			return;
		}

		$data = array();

		print view('statistics/onlineuserslive', $data);
	}

}

