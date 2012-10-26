<?php
class driver_mysql
{
	protected $config;
	protected $link;
	protected $db;

	protected $benchmark = false;


	public function driver_mysql($config = null)
	{
		if($config == null || !is_array($config)) {
			exit('Fatal Error: MySQL: please supply a config array');
		}

		if(config('application', 'benchmark', false))
		{
			$eva	=&eva();
			$this->benchmark = $eva->benchmark;
		}


		$this->config 		= $config;
		$this->connect();
	}

	public function connect()
	{
		$this->link = mysql_connect(
			$this->config['host'],
			$this->config['username'],
			$this->config['password']
		);
		if(!$this->link) {
			exit("Fatal Error: Error connecting mysql on ".$this->config['host']);
		}

		$this->db 	= mysql_select_db(
			$this->config['database']
		);

		if(!$this->db) {
			exit("Fatal Error: Error selecting mysql database ".$this->config['database']);
		}
	}

	public function disconnect()
	{
		mysql_close($this->link);
	}

	public function query($sql, $explain = false)
	{
		$starttime = microtime(true);

		if($explain === true) {
			$sql = "EXPLAIN ".$sql;
		}

		$result = mysql_query($sql, $this->link);

		if(!$result)
		{
    		$return = mysql_error($this->link);
		}
		else if(stripos($sql, 'insert') === 0)
		{
			$return = mysql_insert_id($this->link);
		}
		else
		{
			if(is_bool($result)) {
				$return = $result;
			} else {
				$rows = array();
				while($row = mysql_fetch_assoc($result))
				{
					foreach($row as &$val)
					{
					$val = (is_numeric($val)) ? (int)$val : $val;
					}

					$rows[] = $row;
				}

				$return = $rows;
			}
		}


		if($this->benchmark !== false && $explain != true)
		{
			$explain = null;
			if(config('application', 'benchmark_explain', false) && stripos(trim($sql), 'select') == 0 ) {
				$explain = array('explain' => $this->query($sql, true));
			}
			$this->benchmark->registerQuery('MySQL', $sql, (microtime(true) - $starttime), null, $explain);
		}

		return $return;
	}

}