<?php
class driver_facebook
{
	private $config;
	private $link;
	private $token;

	private $benchmark = false;


	public function driver_facebook($config = null)
	{
		if(!isset($config) || !is_array($config)) {
			$config = array();
		}

		if(config('application', 'benchmark', false))
		{
			$eva	=&eva();
			$this->benchmark = $eva->benchmark;
		}

		$this->config = array(
			'graph_url' => "https://graph.facebook.com/",
			'token'		=> null
		);

		$this->config += $config;
	}

	public function call_graph_url($query, $token = null, $method = "GET")
	{
		$starttime 	= microtime(true);

		$url		= $this->config['graph_url'];
		if($token == null) {
			$token = $this->config['token'];
		}
		if($this->config['token'] == null) {
			$this->config['token'] = $token;
		}

		if($method == 'GET' || $method == 'DELETE') {
			$url.= $query."?access_token=".$token;
		}

		$ch 		= curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		if($method == "FQL")
		{
			$batch = array(
		    	array(
			        "method" 		=> "GET",
			        "name" 			=> "user_friends",
			        "omit_response_on_success" => false,
			        "relative_url" 	=> "method/fql.query?query=".urlencode($query)
				)
			);

			$param = array(
			    'access_token' 	=> $token,
			    'batch' 		=> json_encode($batch),
			    'callback' 		=> ''
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		} else if($method == "POST")
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		} else
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}


		$result = curl_exec($ch);

		if($method == "FQL")
		{
			$result = substr($result, 6, -2);
			$result = json_decode($result, true);
			$result = json_decode($result[0]['body'], true);
		}
		if($this->benchmark !== false)
		{
			$reg_query = ($method == 'GET' || $method == 'DELETE') ? $url : $query;
			$this->benchmark->registerQuery('Facebook', $reg_query, (microtime(true) - $starttime), null, null);
		}

		return $result;
	}

	public function query($query, $token = null, $method = "GET")
	{
		return $this->call_graph_url($query, $token, $method);
	}

}