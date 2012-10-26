<?php
class Controller
{
	// skeleton
	private static $eva;

	// loader
	public $load;

	public function Controller()
	{
		self::$eva = &$this;

		$this->_init_loader();

	}

	private function _init_loader()
	{
		$this->load = new Loader();
	}

	public static function &get_instance()
	{
		return self::$eva;
	}
}

function &eva()
{
	return Controller::get_instance();
}
