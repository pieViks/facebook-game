<?php
class Log_model extends Model
{
	public function Log_model()
	{
		parent::Model();

		c()->load('mysql', 'database_stat_facebook', 'stat');
		$this->set_modelconnection( c('stat') );
	}

	public function get_errors($request)
	{
		//`pkUniqueErrorsId`, `errno`, `errnoString`, `message`, `filename`, `lineno`, `lastOccurrence`, `firstOccurrence`, `amount`, `follow`, `email`)
		$result = array('count'=>0, 'result'=>array());

		$sql 	= "SELECT {select} FROM `unique_errors`";

		$result['count'] = $this->query(str_replace('{select}','count(*) as count',$sql));
		$result['count'] = $result['count'][0]['count'];

		if(count($request['sort']))
		{
			$sql.= "ORDER BY ";
			$c = count($request['sort']);
			for($i=0; $i<$c; $i++)
			{
				$s = $request['sort'][$i];
				if($s['property'] == 'errnoString') { $s['property'] = 'errno'; }
				$sql.= $s['property']." ".$s['direction'];
				if($i < ($c-1)) {
					$sql.= ", ";
				}
			}
		}

		$sql .= " LIMIT ".$request['start'].", ".$request['limit'];
		$result['result'] = $this->query(str_replace('{select}','*',$sql));
		return $result;
	}

	public function delete_error($pkUniqueErrorsId = null)
	{
		if($pkUniqueErrorsId === null || !is_numeric($pkUniqueErrorsId)){
			return;
		}

		$sql = "DELETE FROM `unique_errors`
				WHERE `pkUniqueErrorsId`='".$pkUniqueErrorsId."'
				LIMIT 1";

		return $this->query($sql);
	}

	public function follow_error($pkUniqueErrorsId, $follow = "0", $email = "")
	{
		if($pkUniqueErrorsId === null || !is_numeric($pkUniqueErrorsId)){
			return;
		}

		$sql = "UPDATE `unique_errors`
					SET `follow`='".$follow."',
					`email`='".$email."'
					WHERE `pkUniqueErrorsId`='".$pkUniqueErrorsId."'
					LIMIT 1";

		return $this->query($sql);
	}

}