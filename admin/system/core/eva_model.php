<?php 
class Model 
{
	public $connection;
	public $modelconnection;
	public $eva;
	
	public function Model()
	{
		$this->eva 			=&eva();
		$this->connection 	=&$this->eva->connection;
	
		// check for a connection
		if(sizeof($this->connection) == 1) {
			$this->modelconnection = $this->connection->autoConnect();
		}
	}
	
	public function set_modelconnection($connection) 
	{
		$this->modelconnection = $connection;
	}
	
	public function query() 
	{
		if(isset($this->modelconnection)) {
			return call_user_func_array(array($this->modelconnection, 'query'), func_get_args());
		}
		return null;
	}
	
}