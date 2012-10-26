<?php 
class autoModel extends Model
{
	private $table;
	
	public function autoModel($table)
	{
		parent::Model();
		
		$this->table = $table;
	}	
	
	public function query($sql)
	{
		$sql = str_replace('@', $this->table, $sql);
		return parent::query($sql);
	}
}