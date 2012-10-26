<?php
/*CREATE TABLE `online_users` (
`fkUserId` INT( 11 ) NOT NULL ,
`lastTimestamp` INT( 32 ) NOT NULL ,
`lastAction` VARCHAR( 256 ) NOT NULL ,
`location` VARCHAR( 256 ) NOT NULL
) ENGINE = MYISAM ;*/
class Statistics_model extends Model
{
	public function Statistics_model()
	{
		parent::Model();

		c()->load('mysql', 'database_stat_facebook', 'stat');
		$this->set_modelconnection( c('stat') );
	}

	public function getOnlineUsers()
	{
		$this->_revalidateOnlineUsers();

		$sql 		= "SELECT count(`location`) AS count, `location` FROM `online_users` GROUP BY `location`";
		$result 	= $this->query($sql);

		$return		= array('total' => 0);

		foreach($result as $row)
		{
			$return[ $row['location'] ] = (int) $row['count'];
			$return['total']			+= (int) $row['count'];
		}

		return $return;
	}

	private function _revalidateOnlineUsers()
	{
		$time 	= time() - 62;
		$sql 	= "DELETE FROM `online_users` WHERE `lastTimestamp` < ".$time;

		return $this->query($sql);
	}


}