<?php

require_once "Gazel/Form.php";
require_once "Gazel/Config.php";

class Gazel_Widget
{
	protected $config;
	
	public function __construct()
	{
		$this->config=Gazel_Config::getInstance();
	}
}