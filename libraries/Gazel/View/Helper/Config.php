<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Zend/Config.php";

class Gazel_View_Helper_Config extends Zend_View_Helper_Abstract
{
	public function config($name=null)
	{
		$config=Gazel_Config::getInstance();
		
		if ( $name==null ){
			return $config;
		} else {
			return $config->$name;
		}
	}
	
}