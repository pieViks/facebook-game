<?php
class Loader
{
	public $loadedFiles;
	private $view;

	public function Loader()
	{
		// get controller
		$eva =&eva();

		//
		$this->loadedFiles 	= array();

		// create default loaders
		$eva->model 		= new loaderHolder('model', 	array(PATH_APPLICATION.'model/'));
		$eva->library		= new loaderHolder('library', 	array(PATH_APPLICATION.'library/'));

		// create alternative loader for views & connections
		$eva->view			= new loaderViews(array(PATH_APPLICATION.'view/'));
		$eva->connection	= new loaderConnection(array(PATH_SYSTEM.'driver/'));
		$eva->automodel		= new loaderAutoModel();
	}

	public function newHolder($name, $locations)
	{
		$eva =&eva();
		if(!is_array($locations)) {
			$locations = array($locations);
		}

		$eva->$name = new loaderHolder($name, $locations);
	}

	public function helper($name, $file = null)
	{
		if($file === null) {
			$file = PATH_APPLICATION.'helper/'.strtolower($name).'.php';
		}
		if(file_exists($file) === false) {
			if(config('application', 'printr', false)) {
				printr("FATAL ERROR: no sush file ".$file);
			} else {
				exit("Fatal Error: No such file: ".$file);
			}
		}

		$this->loadedFiles[] = $file;
		require_once $file;
	}
}

class loaderHolder
{
	private $type;
	private $filelocations;

	public function __construct($type, $filelocations)
	{
		$this->type 			= $type;
		$this->filelocations 	= $filelocations;
	}

	public function __get($var)
	{
		if(isset($this->$var) === false)
		{
			$eva 	=&eva();
			$type 	= $this->type;
			$eva->$type->load($var);

			return $eva->$type->$var;
		}
		else
		{
			return $this->$var;
		}
	}

	public function load($class, $init = array(), $file = null, $name = null)
	{
		// search file if not provided
		if($file === null) {
			$file = strtolower($class).'.php';
			foreach($this->filelocations as $path)
			{
				if(file_exists($path.$file)) {
					$file = $path.$file;
					break;
				}
			}
		}

		// set default attachname if not provided
		if($name === null) {
			$name = $class;
		}

		$eva 			=&eva();
		$loadedFiles 	=&$eva->load->loadedFiles;

		// check if file has been loaded previously
		if(in_array($file, $loadedFiles) === false)
		{
			// does the file exists?
			if(file_exists($file) === false) {
				if(config('application', 'printr', false)) {
					printr("FATAL ERROR: no such file ".$file);
				} else {
					exit("Fatal Error: No such file: ".$file);
				}
			} else {
				require_once $file;
				$loadedFiles[] = $file;
			}
		}

		// check if file has been added previously
		if(isset($this->$name) === false)
		{
			$this->$name = new $class($init);
		}
	}
}

class loaderViews
{
	private $filelocations;

	public function __construct($filelocations)
	{
		$this->filelocations 	= $filelocations;
	}

	public function load($class, $data = array(), $file = null)
	{
		// search file if not provided
		if($file === null) {
			$file = strtolower($class).'.php';
			foreach($this->filelocations as $path)
			{
				if(file_exists($path.$file)) {
					$file = $path.$file;
					break;
				}
			}
		}

		if(file_exists($file) === false)
		{
			if(config('application', 'printr', false)) {
				printr("FATAL ERROR: no such file ".$file);
			} else {
				exit("Fatal Error: No such file: ".$file);
			}
		}

		// extract the variables;
		if(is_array($data) && count($data) > 0)
		{
			extract($data);
		}

		$view_string = file_get_contents($file);

		ob_start();
		print eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $view_string)));
		$return = ob_get_contents();
		ob_end_clean();

		return $return;
	}
}

class loaderConnection
{
	private $filelocations;
	private $autoconnectfile;

	public function __construct($filelocations)
	{
		$this->filelocations 	= $filelocations;
	}

	public function load($class, $config = false, $name = null, $file = null)
	{
		return $this->connect($class, $config, $name, $file);
	}

	public function set_autoconnectfile($file)
	{
		$this->autoconnectfile = $file;
	}

	public function autoConnect()
	{
		$autoName = config('application', 'auto_connect_name');

		if(isset($this->$autoName))
		{
			return $this->$autoName;
		}

		if(config('application', 'auto_connect', false) === true)
		{
			if(isset($this->autoconnectfile) && $this->autoconnectfile !== null)
			{
				$auto_cfg = config($this->autoconnectfile);
			} else
			{
				$auto_cfg = config(config('application', 'auto_connect_file'));
			}
			return $this->connect($auto_cfg['driver'], $auto_cfg, config('application', 'auto_connect_name'));
		}
		return null;
	}

	public function connect($class, $config = false, $name = null, $file = null)
	{
		$class = 'driver_'.strtolower($class);
		// search file if not provided
		if($file === null) {
			$file = $class.'.php';
			foreach($this->filelocations as $path)
			{
				if(file_exists($path.$file)) {
					$file = $path.$file;
					break;
				}
			}
		}

		// set default attachname if not provided
		if($name === null) {
			$name = $class;
		}

		$eva 			=&eva();
		$loadedFiles 	=&$eva->load->loadedFiles;

		// check if file has been loaded previously
		if(in_array($file, $loadedFiles) === false)
		{
			// does the file exists?
			if(file_exists($file) === false) {
				if(config('application', 'printr', false)) {
					printr("FATAL ERROR: no such file ".$file);
				} else {
					exit("Fatal Error: No such file: ".$file);
				}
			} else {
				require_once $file;
				$loadedFiles[] = $file;
			}
		}

		// check if file has been added previously
		if(isset($this->$name) === false)
		{
			if($config != false)
			{
				if(!is_array($config))
				{
					// load required config
					$config = config($config);
				}
			}

			$this->$name = new $class($config);
		}

		return $this->$name;
	}
}

class loaderAutoModel
{
	public function __get($model)
	{
		if(!isset($this->$model)) {
			$this->$model = new autoModel($model);
		}
		return $this->$model;
	}
}
