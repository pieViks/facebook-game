<?php
class Benchmark
{
	// general
	private $startTime;
	private $endTime;
	private $totalTime;
	
	// query
	private $queryList;
	
	// timelabels
	private $timeLabels;
	
	public function Benchmark()
	{
		$this->queryList = array();
	}
	
	public function setStartTime($timestamp)
	{
		$this->startTime = $timestamp;
	}
	
	public function setEndTime($timestamp)
	{
		$this->endTime = $timestamp;
		$this->totalTime = $this->endTime - $this->startTime;
	}	
	
	public function clearQueryList()
	{
		$this->queryList = array();
	}
	
	public function addTimeLabel($label, $timestamp = null, $relative = true)
	{
		$timestamp = ($timestamp !== null) ? $timestamp : microtime(true);
		if($relative === true) {
			$timestamp -= $this->startTime;
		}
		
		$this->timeLabels[] = array('label'=>$label, 'timestamp'=>$timestamp);
	}
	
	public function registerQuery($driver, $query, $time, $origin = null, $extra = null)
	{
		if($origin === null) 
		{
			$origin		= "";
			$debug_bt 	= debug_backtrace();
			foreach($debug_bt as $bt) {
				if(isset($bt['file']) && strpos($bt['file'], PATH_APPLICATION) !== false) {
					$origin .= str_replace(PATH_APPLICATION, "", $bt['file']).":".$bt['line']." <- ";
				}
			}			
			$origin .= 'start';
		}
		
		$row = array(
			'driver'	=> $driver,
			'origin' 	=> $origin, 
			'query' 	=> $query,
			'time' 		=> $time
		);
		
		if($extra !== null && is_array($extra)) {
			foreach($extra as $k=>$v) {
				$row[$k] = $v;
			}
		}
		
		$this->queryList[] = $row;
		
		
		// store in db
		if(config('application', 'benchmark_method', 'print') == 'db')
		{
			// connect to benchmark db
			$db_cfg = config( config('application', 'benchmark_db') );
			
			$link = mysql_connect($db_cfg['host'],$db_cfg['username'],$db_cfg['password']);
			mysql_select_db($db_cfg['database']);
			
			// prepare query for log table
			$query = trim( str_replace(array("\n","\t"), "", $query) );
			if(stripos($query, 'http') === 0) {
			// query is an api call
				$query = substr($query, 0, strpos($query, '='));
				$query = preg_replace("/[0-9]/", "", $query);
			} else {
			// other
				$query = preg_replace("/[0-9]/", "", $query);
			}
		
			// get eva
			$eva =&eva();
			
			// check if query is listed;
			$sql 	= "SELECT * FROM `benchmark_query` WHERE `query` = '".$query."' LIMIT 1";
			$result = $this->query($sql, $link);
			if(count($result) > 0) {
				
				$stime 	= ($result[0]['slowestTime'] < $time) ? $time : $result[0]['slowestTime'];
				$ftime 	= ($result[0]['fastestTime'] > $time) ? $time : $result[0]['fastestTime'];
				$count  = $result[0]['count']+1;
				
				$sql 	= "UPDATE `benchmark_query` 
							SET `slowestTime` = '".$stime."',
							`fastestTime` = '".$ftime."',
							`origin`	  = '".$origin."',
							`count`		  = '".$count."' 
							WHERE `pkBenchmarkQueryId` = '".$result[0]['pkBenchmarkQueryId']."' LIMIT 1";
				$this->query($sql, $link);		
			} else {
				$sql 	= "INSERT INTO `benchmark_query` (`query`, `origin`, `slowestTime`, `fastestTime`,`count`) 
				VALUES ('".$query."', '".$origin."', '".$time."', '".$time."','1');";
				$this->query($sql, $link);
			}
			
		}
	}
	
	public function output()
	{
		$log = $this->_create_log();
		
		switch(config('application', 'benchmark_method', 'print')) 
		{
			case "print":
				printr($log, false, true);
				break;
			case "comment":
				print "<!-- \n".$log." \n\n-->";
				break;
			case "log":
				$file = config('application', 'benchmark_log_file');
				
				if(file_exists($file) && filesize($file) > 1048576) {
					rename($file, $file.'.'.date('dmY_Hi', filemtime($file)));
				}
				
				$handle = fopen($file, 'a+');
				fwrite($handle, $log);
				fclose($handle);
				break;
			case "email":
				$to = config('application', 'benchmark_email');
				mail($to, "bechmark result", $log);
				break;
			case "db":
				//
				break;
		}
	}
	
	private function _create_log()
	{
		$datetime 	= date('d/m/Y H:i:s');
		$log 		= "";
		$log.= "=========================================================================================================================== \n";	
		$log.= " BENCHMARK REPORT ".$datetime." \n";
		$log.= "   for user_agent: ".$_SERVER['HTTP_USER_AGENT']." \n";
		$log.= "=========================================================================================================================== \n";	
		$log.= " \n";
		$log.= "+General \n";
		$log.= " executionTime: ".$this->totalTime." \n";
		$log.= " \n";
		$log.= "+TimeLabels \n";
		foreach($this->timeLabels as $t) {
			$log.= $t['label'].": ".$t['timestamp']." \n";
		}
		$log.= " \n";
		$log.= "+Queries \n";
		
		$i=0;foreach($this->queryList as $q) 
		{
			if(strpos($q['time'], '-')) {
				$q['time'] = '0.00';
			}
			
			$log.= "---- query ".($i+1)." -------------------------------------------------------------------------------------------------------------- \n";
			$log.= " \n";
			$log.= str_replace("\t","",$q['query'])." \n";
			$log.= " \n";
			$log.= "[ time: ".$q['time']." driver: ".$q['driver']." ]\n";
			$log.= "[ origin: ".$q['origin']." ] \n";
			
			
			if(isset($q['explain']) && is_array($q['explain'])) 
			{
				$log.= "explain: \n";
				$log.= "| ";
				foreach($q['explain'][0] as $k=>$v) {
					$log.= $k.fillspaces(20 - strlen($k))." | ";
				}
				$log.= " \n";
				
				foreach($q['explain'] as $e) {
					$log.= "| ";
					foreach($e as $v) {
						$log.= $v.fillspaces(20 - strlen($v))." | ";
					}
					$log.= " \n";
				}
				
			}
			
			$log.= " \n";
		$i++; }
		$log.= "=========================================================================================================================== \n";
			
		return $log;
	}
	
	public function query($sql, $connection)
	{
		$starttime = microtime(true);

		$result = mysql_query($sql, $connection);
		
		if(stripos($sql, 'insert') === 0) {
			$return = mysql_insert_id();
		}
		
		if($result)
		{
			if(is_bool($result)) {
				$return = $result;
			} else {
				$rows = array();
				while($row = mysql_fetch_assoc($result))
				{
					$rows[] = $row;
				}
					
				$return = $rows;
			}
		} else
		{
			$return = false;
		}
		
		return $return;
	}
	
}

function benchmark_out()
{
	$eva=&eva();
	// set end time for benchmark
	$eva->benchmark->setEndTime(microtime(true));
	// output benchmark (as configured)
	$eva->benchmark->output();
}

register_shutdown_function('benchmark_out');

function fillspaces($amount, $char = " ") 
{
	$r = ''; for($i=0; $i<$amount; $i++) { $r.= $char; } return $r;
}