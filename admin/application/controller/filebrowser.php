<?php
class Filebrowser extends Controller
{
	private $root_path = "/var/domains/admin/public/";

	public function Filebrowser()
	{
		parent::Controller();

		l('auth');
	}

	public function getDirs()
	{

		$dirs 		= $this->_getDirs($this->root_path, '');

		print json_encode($dirs);
	}

	private function _getDirs($root, $path)
	{
		if($path == '') {
			$glob 			= glob($root.$path.'item*', GLOB_ONLYDIR);
		} else {
			$glob 			= glob($root.$path.'*', GLOB_ONLYDIR);
		}

		$directories 	= array();

		foreach($glob as $row)
		{

			$directories[] = array(
				'leaf' 	=> 'false',
				'cls'	=> 'folder',
				'text'	=> str_replace($root.$path, '', $row),
				'id'	=> str_replace($root, '', $row),
				'children' => $this->_getDirs($root, str_replace($root, '', $row).'/'),
			);
		}

		return $directories;
	}

	public function getFiles()
	{
		$path = (isset($_GET['path'])) ? $_GET['path'].'/' : '';
		$path = str_replace("../", "", $path);

		$root_path 	= $this->root_path;

		$browse 	= glob($root_path.$path.'*');
		$files 		= array();
		foreach($browse as $row)
		{
			if(!is_dir($row))
			{
				$files[] = array(
					'filename' => str_replace($root_path.$path, '', $row),
					'filesize' => filesize($row). ' bytes',
					'filedate' => date("F d Y H:i:s", filemtime($row)),
					'path' => $path
				);
			}
		}

		print json_encode($files);
	}

}

