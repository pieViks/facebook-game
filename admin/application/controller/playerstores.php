<?php
class Playerstores extends Controller
{
	public function Playerstores()
	{
		parent::Controller();

		l('auth');
	}

	public function players()
	{
		$request 		= array(
			'page'	=> (isset($_GET['page'])) ? $_GET['page'] : 1,
			'start'	=> (isset($_GET['start'])) ? $_GET['start'] : 0,
			'limit'	=> (isset($_GET['limit'])) ? $_GET['limit'] : 25,
			'sort'	=> (isset($_GET['sort'])) ? json_decode($_GET['sort'], true) : array(array('property'=>'pkUserId','direction'=>'DESC')),
			'filter'=> (isset($_GET['filter'])) ? json_decode($_GET['filter'], true) : false
		);

		$players 		= m('player_model')->get_players($request);

		foreach($players['result'] as &$p)
		{
			$p['lastPlayed'] = time() - $p['lastPlayed'];
		}

		$result = array(
			'players' 	=> $players['result'],
			'total'		=> $players['count']
		);

		print json_encode($result);
	}

	public function playerdetails($pkUserID)
	{
		if(!isset($pkUserID)) { exit; }

		$rawpost = file_get_contents("php://input");
		//printr(json_decode($_POST));
		if($rawpost != '') {
			$rawpost = json_decode($rawpost, true);
			m('player_model')->set_playerdetails($pkUserID, $rawpost);
		}

		$player 	= m('player_model')->get_playerdetails($pkUserID);

		foreach($player['result'] as &$row)
		{
			$row['readablevalue'] = '';
			if($row['param'] == 'lastPlayed' || $row['param'] == 'energyUpdate' || $row['param'] == 'signupTimestamp')
			{
				$row['readablevalue'] = date('r', (int) $row['value']);
			}
			else if (strpos($row['value'], '{') === 0)
			{
				$row['readablevalue'] = print_r(json_decode($row['value']), true);
			}

		}

		$result = array(
			'player' 	=> $player['result'],
			'total'		=> $player['count']
		);

		print json_encode($result);
	}

	public function playeritems($pkUserID)
	{
		if(!isset($pkUserID)) {
			exit;
		}

		$items 	= m('player_model')->get_playeritems($pkUserID);
		foreach($items['result'] as &$row)
		{
			$row['stateStartTimeReadable'] = date('r', $row['stateStartTime']);
		}

		$result = array(
			'dynamicobjects' 	=> $items['result'],
			'total'				=> $items['count']
		);

		print json_encode($result);
	}
}