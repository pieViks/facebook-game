<?php
class Logbrowser extends Controller
{
	private $root_path = "/var/youda/statlog/";

	public function Logbrowser()
	{
		parent::Controller();

		l('auth');
	}

	public function getDirs()
	{

		$dirs 		= $this->_getVirtualDirsFromLogs($this->root_path, '');

		print json_encode($dirs);
	}

	private function _getVirtualDirsFromLogs($root)
	{
		$glob 			= glob($root.'*.log*');

		$tree			= array();

		foreach($glob as $f)
		{
			// remove path
			$f = str_replace($root, '', $f);
			// remove extension
			$f = substr($f, 0, strpos($f, '.'));

			// create virtual path
			list($stage, $env, $log) = explode("-", $f);

			// create tree
			if(!isset($tree[$stage])) { $tree[$stage] = array(); }
			if(!isset($tree[$stage][$env])) { $tree[$stage][$env] = array(); }
			if(!isset($tree[$stage][$env][$log])) { $tree[$stage][$env][$log] = array(); }
		}

		// convert tree to ext js dir tree
		$vDirs = $this->_createVDirFromArray($tree);

		return $vDirs;
	}

	private function _createVDirFromArray($arr, $path = '')
	{
		$vDirs = array();
		foreach($arr as $key => $val)
		{
			$item = array(
				'leaf' 	=> 'false',
				'cls'	=> 'folder',
				'text'	=> $key,
				'id'	=> $path.$key,
				'children' => array()
			);

			if(is_array($val)) {
				$item['children'] = $this->_createVDirFromArray($val, $path.$key.'-');
			}

			$vDirs[] = $item;
		}

		return $vDirs;
	}

	public function getFiles()
	{
		$path = (isset($_GET['path'])) ? explode('-',$_GET['path']) : array();
		for($i=0; $i<3; $i++) {
			if(!isset($path[$i])) {
				$path[$i] = '*';
			}
		}

		$root			= $this->root_path;
		$glob 			= glob($root.implode('-', $path).'*.log*');
		$files			= array();

		foreach($glob as $f)
		{
			$files[] = array(
				'filename' => str_replace($root, '', $f),
				'filesize' => filesize($f). ' bytes',
				'filedate' => date("F d Y H:i:s", filemtime($f)),
				'path' => $path
			);
		}

		print json_encode($files);
	}

	public function getFilesForUser($userId)
	{
		if(!is_numeric($userId)) { return; }

		//TMP
		$env = 'dev';

		$grep 	= 'grep -lr "[user '.$userId.']" '.$this->root_path;
		$grep 	= explode("\n", shell_exec($grep) );

		$files  = array();
		foreach($grep as $f)
		{
			$f = trim($f);
			if($f == "") continue;
			if(strpos($f, $env) === false) continue;

			$files[] = array(
				'filename' => str_replace($this->root_path, '', $f),
				'filesize' => filesize($f). ' bytes',
				'filedate' => date("F d Y H:i:s", filemtime($f)),
				'path' => $this->root_path
			);
		}

		print json_encode($files);
	}

	public function openLog()
	{
		$file = (isset($_GET['file'])) ? $_GET['file'] : false;
		if($file == false) return;

		$file 			= str_replace('/', '', $file);
		$root			= $this->root_path;

		if(!file_exists($root.$file)) return;

		$logFile 		= file($root.$file);
		$totalLines		= count($logFile);
		$logFileLines 	= array();
		$found_keywords = array();

		$page			= (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page']-1 : 0;
		$limit			= (isset($_GET['limit'])) ? $_GET['limit'] : 25;

		if($limit != 'tail')
		{
			$start			= $page * $limit;
			$end			= $start + $limit;

			if(isset($_GET['filter']) && $_GET['filter'] != "")
			{
				$json_decoded_filter = json_decode($_GET['filter'], true);
				if(is_array($json_decoded_filter))
				{
					$keyword 	= $json_decoded_filter[0]['value'];
					preg_match_all('/"(.*?)(\n|\r|$|")/', $keyword, $quoted, PREG_PATTERN_ORDER );
					$keyword	= explode(" ", str_replace($quoted[0], "", $keyword));

					foreach($keyword as $k) { trim($k); if($k != "") $found_keywords[] = $k; }
					foreach($quoted[1] as $k) { trim($k); if($k != "") $found_keywords[] = $k; }

					foreach($found_keywords as $keyword)
					{
						$pregGrep 	= preg_grep("/".preg_quote($keyword)."/i", $logFile);

						if(is_array($pregGrep)) {
							$logFile 	= array_values($pregGrep);
						} else {
							$logFile	= array();
						}
					}
				}
			}
			// recount totalLines
			$totalLines		= count($logFile);
		}
		else
		{
			$start 	= $totalLines - 50;
			$end 	= $totalLines;

			$totalLines = 50;
		}


		for($i=$start; $i<$end; $i++)
		{
			if(!isset($logFile[$i])) {
				break;
			}

			foreach($found_keywords as $keyword) {
				$logFile[$i] = str_ireplace($keyword, "<span class=\"x-livesearch-match\">".$keyword."</span>", $logFile[$i]);
			}
			$logFile[$i] =

			$logFileLines[] = array(
				'linenumber'	=> $i,
				'line' 			=> $logFile[$i]
			);


		}

		$result = array(
			'total' => $totalLines,
			'lines' => $logFileLines

		);

		print json_encode($result);
	}

	public function errors()
	{
		//`pkUniqueErrorsId`, `errno`, `errnoString`, `message`, `filename`, `lineno`, `lastOccurrence`, `firstOccurrence`, `amount`)
		$request = array(
			'page'	=> (isset($_GET['page'])) ? $_GET['page'] : 1,
			'start'	=> (isset($_GET['start'])) ? $_GET['start'] : 0,
			'limit'	=> (isset($_GET['limit'])) ? $_GET['limit'] : 25,
			'sort'	=> (isset($_GET['sort'])) ? json_decode($_GET['sort'], true) : array(array('property'=>'amount','direction'=>'DESC')),
			'filter'=> (isset($_GET['filter'])) ? json_decode($_GET['filter'], true) : false
		);

		$errors = m('log_model')->get_errors($request);

		foreach($errors['result'] as &$row)
		{
			$row['lastOccurrence'] = date('r', $row['lastOccurrence']);
			$row['firstOccurrence'] = date('r', $row['firstOccurrence']);

			$row['errnoString'] = "<div style=\"float: left; margin: 0 10px 0 0; width: 16px; height: 16px; background:".$this->_get_errno_cat($row['errno'], 'color')."\"></div>".$row['errnoString'];
		}

		$result = array(
			'errors' 	=> $errors['result'],
			'total'		=> $errors['count']
		);

		print json_encode($result);
	}

	private function _get_errno_cat($errno, $type = 'cat')
	{
		switch($errno)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				return ($type == 'cat') ? 1 : '#FF0000';

			default:
			case E_WARNING:
			case E_CORE_WARNING:
				return ($type == 'cat') ? 2 : '#F7931E';

			case E_NOTICE:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				return ($type == 'cat') ? 3 : '#FCEE21';
		}
	}

	public function followerror($id)
	{
		if(isset($_POST['delete']) && $_POST['delete'] == 'on') {
			m('log_model')->delete_error($id);
			return;
		}

		$follow 	= (isset($_POST['follow']) && $_POST['follow'] == 'on') ? 1 : 0;
		$email		= (isset($_POST['email'])) ? $_POST['email'] : '';

		m('log_model')->follow_error($id, $follow, $email);
	}

	public function searchLog()
	{
		if(!isset($_GET['search'])) {
			print "{}";
		} else {
			$search = json_decode($_GET['search'], true);
		}

		// time
		list($start['year'], $start['month'], $start['date']) 		= explode('-', substr($search['startDate'], 0, strpos($search['startDate'], 'T')));
		list($start['hour'], $start['minutes'], $start['seconds']) 	= explode(':', substr($search['startTime'], strpos($search['startTime'], 'T')+1));
		list($end['year'], $end['month'], $end['date']) 			= explode('-', substr($search['endDate'], 0, strpos($search['endDate'], 'T')));
		list($end['hour'], $end['minutes'], $end['seconds']) 		= explode(':', substr($search['endTime'], strpos($search['endTime'], 'T')+1));

		foreach($start as $key => $val) { 	$start[$key] = intval($val); 	}
		foreach($end as $key => $val) { 	$end[$key] = intval($val); 		}

		$startTimeStamp 	= mktime($start['hour'],$start['minutes'],$start['seconds'],$start['month'],$start['date'],$start['year']);
		$endTimeStamp 		= mktime($end['hour'],$end['minutes'],$end['seconds'],$end['month'],$end['date'],$end['year']);

		// filter
		$found_keywords = array();
		$keyword 		= $search['search'];
		preg_match_all('/"(.*?)(\n|\r|$|")/', $keyword, $quoted, PREG_PATTERN_ORDER );
		$keyword		= explode(" ", str_replace($quoted[0], "", $keyword));

		foreach($keyword as $k) { trim($k); if($k != "") $found_keywords[] = $k; }
		foreach($quoted[1] as $k) { trim($k); if($k != "") $found_keywords[] = $k; }

		$search['search'] = $found_keywords;

		// files
		$glob_str			= $this->root_path.$search['stage'].'*'.$search['logtypes'].'*';
		$files				= glob($glob_str);

		// search files
		$found = array();
		foreach($files as $file)
		{
			$fileLastMod		= filemtime($file);

			if($fileLastMod > $startTimeStamp) {
				$found += $this->_searchLog_searchFile($file, $search, $startTimeStamp, $endTimeStamp);
			}
		}

		// reorder result
		$orderIndex = array();
		foreach($found as $row) {
			$orderIndex[] = $row['timestamp'];
		}
		array_multisort($orderIndex, SORT_ASC, $found);

		// return
		$result = array(
			'lines' 	=> $found,
			'total'		=> count($found)
		);

		print json_encode($result);
	}

	private function _searchLog_searchFile($file, $search, $startTimeStamp, $endTimeStamp)
	{
		$fileLines 	= file($file);
		$filename	= str_replace($this->root_path, '', $file);
		$foundLines = array();
		foreach($fileLines as $l)
		{
			preg_match_all('/\[(.*?)(\n|\r|$|\])/', $l, $quoted, PREG_PATTERN_ORDER );
			if(!isset($quoted[1]) || !isset($quoted[1][0])) {
				continue;
			}

			$logtime = strtotime($quoted[1][0]);

			// in time range
			if($logtime >= $startTimeStamp && $logtime <= $endTimeStamp) {

				// matches any search keywords?
				$matched = false;
				foreach($search['search'] as $keyword)
				{
					if(preg_match("/".preg_quote($keyword)."/i", $l)) {
						$matched = true;
					} else {
						$matched = false;
					}
				}

				foreach($search['search'] as $keyword) {
					$l = str_ireplace($keyword, "<span class=\"x-livesearch-match\">".$keyword."</span>", $l);
				}

				if($matched == true) {
					$foundLines[] = array(
						'timestamp' => $logtime,
						'logfile' => $filename,
						'line' => $l
					);
				}
			}

		}

		return $foundLines;
	}

}

