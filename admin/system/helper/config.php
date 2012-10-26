<?php
	function &defineFile($name)
	{
		static $_definefiles = array();

		// check if the file has been loaded previously
		if(!isset($_definefiles[$name]))
		{
			// load the file
			$file = PATH_APPLICATION.'config/'.strtolower($name).'.php';

			// does the requested file exist?
			if(!is_file($file)) {
				exit("Error: no such define file ".$name);
			}

			// include the file (defines are set)
			require $file;
			$_definefiles[$name] = true;
		}
	}

	function &config($name, $item = false, $default = null, $setValue = false)
	{
		static $_configfiles = array();

		// check if the file has been loaded previously
		if(!isset($_configfiles[$name]))
		{
			// load the config file
			$file = PATH_APPLICATION.'config/'.strtolower($name).'.php';
			if(!is_file($file))
			{
				if($default !== null)
				{
					return $default;
				}
				exit("Error: no such config file '".$name."'");
			}
			require $file;
			$_configfiles[$name] = $config;
		}

		if($setValue === true)
		{
			$_configfiles[$name][$item] = $default;
			return $default;
		}
		
		if($item !== false)
		{
			// check if the item is set on the name holder
			if(!isset($_configfiles[$name][$item]))
			{
				if($default !== null)
				{
					return $default;
				}
				exit("Error: no such config item '".$item."' on file '".$name."' and no default was given");
			}

			// return config value
			return $_configfiles[$name][$item];
		}

		return $_configfiles[$name];
	}