<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Zend/Controller/Router/Route.php";
require_once "Zend/Controller/Router/Route/Hostname.php";
require_once "Gazel/Config.php";

class Gazel_Router_Admin extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$configi=Gazel_Config::getInstance();
		
		$router = new Zend_Controller_Router_Route(
			$configi->adminpath.'/:module/:controller/:action/*',
			array(
				'module'		=> 'admin',
				'controller'	=> 'index',
				'action'		=> 'index'
			)
		);
		
		if ( $configi->configinstance->mu->active == "true" )
		{
			$hostnameRouter = new Zend_Controller_Router_Route_Hostname(
				':username.'.$configi->configinstance->mu->domain,
	      array(
	      )
			);
			$router = $hostnameRouter->chain($router);
		}
		
		$this->_front->getRouter()->addRoute('admin', $router);
	}
}
