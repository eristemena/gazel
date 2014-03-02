<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Zend/Controller/Router/Route.php";
require_once "Zend/Controller/Router/Route/Hostname.php";
require_once "Gazel/Config.php";

class Gazel_Router_PhpInfo extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$configi=Gazel_Config::getInstance();
		
		$router = new Zend_Controller_Router_Route(
			'phpinfo',
			array(
				'module'			=> 'core',
				'controller'		=> 'index',
				'action'			=> 'php-info'
			)
		);
		
		$this->_front->getRouter()->addRoute('phpinfo', $router);
	}
}
