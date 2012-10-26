<?php
class Player_model extends Model
{
	public function Player_model()
	{
		parent::Model();
	}

	public function get_players($request)
	{
		$searchable = array('firstname','lastname','facebook.email','ipaddress');

		$result = array('count'=>0, 'result'=>array());

		$sql 	= "SELECT {select} FROM `user`
					LEFT JOIN `usergameinfo` ON `user`.`pkUserID` = `usergameinfo`.`fkUserID`
					LEFT JOIN `facebook` ON `user`.`pkUserID` = `facebook`.`fkUserID` ";

		if($request['filter'] !== false && is_array($request['filter']))
		{
			$filterBy 	= array_shift($request['filter']);
			// if *
			$val 		= $filterBy['value'];

			$search		= array();
			foreach($searchable as $sf) {
				if(strpos($val, '"') === 0) {
					$search[] = " ".$sf." = '".str_replace('"','',$val)."' ";
				} else {
					$search[] = " ".$sf." LIKE '%".$val."%' ";
				}
			}


			$sql.= "\nWHERE ".implode("\nOR", $search);
		}

		$result['count'] = $this->query(str_replace('{select}','count(*) as count',$sql));
		$result['count'] = $result['count'][0]['count'];

		if(count($request['sort']))
		{
			$sql.= "ORDER BY ";
			$c = count($request['sort']);
			for($i=0; $i<$c; $i++)
			{
				$s = $request['sort'][$i];
				$sql.= $s['property']." ".$s['direction'];
				if($i < ($c-1)) { $sql.= ", "; }
			}
		}
		$sql .= " LIMIT ".$request['start'].", ".$request['limit'];

		$result['result'] = $this->query(str_replace('{select}','pkUserID,facebookID,firstname,lastname,facebook.email,chips,gold,ranchvalue,gender,ipaddress,lastPlayed',$sql));

		return $result;
	}

	public function get_playerdetails($pkUserID)
	{
		$result = array(
			'count' 	=> 0,
			'result' 	=> array()
		);

		$tables = array(
			'user' 			=> '`pkUserID` = '.$pkUserID,
			'facebook' 		=> '`fkUserID` = '.$pkUserID,
			'usergameinfo' 	=> '`fkUserID` = '.$pkUserID
		);

		foreach($tables as $table => $where)
		{
			$sql 				= "SELECT * FROM ".$table." WHERE ".$where." LIMIT 1";
			$query_result		= $this->query($sql);

			if(count($query_result) == 0) {
				continue;
			}

			foreach($query_result[0] as $param => $value)
			{
				$result['result'][] = array(
					'table' => $table,
					'param' => $param,
					'value' => $value
				);

				$result['count']++;
			}
		}

		return $result;
	}

	public function set_playerdetails($pkUserID, $update)
	{
		$tables = array(
			'user' 			=> array(),
			'facebook' 		=> array(),
			'usergameinfo' 	=> array()
		);
		foreach($update['player'] as $row)
		{
			if(!isset($tables[$row['table']])) { continue; }

			$tables[$row['table']][] = "`".$row['param']."`='".mysql_real_escape_string($row['value'])."'";
		}

		foreach($tables as $table => $set)
		{

			$sql = "UPDATE `".$table."` SET ".implode(',',$set)."";

			if($table == 'user') {
				$sql.= " WHERE `pkUserID` = ".$pkUserID." LIMIT 1";
			} else {
				$sql.= " WHERE `fkUserID` = ".$pkUserID." LIMIT 1";
			}

			$this->query($sql);
		}

		return true;
	}


	public function get_playeritems($pkUserID)
	{
		$result = array(
				'count' 	=> 0,
				'result' 	=> array()
		);

		$sql 			= "SELECT * FROM `dynamicobject` WHERE `fkUserId` = '".$pkUserID."'";
		$result_query 	= $this->query($sql);

		$result['count'] 	= count($result_query);
		$result['result']	= $result_query;

		return $result;
	}

	public function newAdminToken($pkUserID)
	{
		if(!is_numeric($pkUserID)) { exit; }

		$sql = "DELETE FROM `adminToken` WHERE `fkUserID`='".$pkUserID."' LIMIT 1";
		$this->query($sql);

		$token 		= sha1( "SOME_RANDOM_HASH319h23rf248".date('r') );
		$expires 	= time() + 10;

		$sql 		= "INSERT INTO `adminToken` (`pkAdminTokenId`, `fkUserId`, `expires`, `token`)
						VALUES (NULL, '".$pkUserID."', '".$expires."', '".$token."');";

		$id = $this->query($sql);

		return $token;
	}
}