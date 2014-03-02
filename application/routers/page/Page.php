<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Zend/Controller/Router/Route.php";
require_once "Zend/Controller/Router/Route/Static.php";
require_once "Zend/Controller/Router/Route/Hostname.php";
require_once "Gazel/Config.php";

class Gazel_Router_Page extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$router = new Zend_Controller_Router_Route(
			':alias/:act/*',
			array(
				'module'	=> 'core',
				'controller' => 'page',
				'action' => 'dispatch',
				'alias'	=> 'default',
				'act' => 'index'
			)
		);
		
		$configi=Gazel_Config::getInstance();
		if ( $configi->configinstance->mu->active == "true" )
		{
			$hostnameRouter = new Zend_Controller_Router_Route_Hostname(
				':username.'.$configi->configinstance->mu->domain,
	      		array(
	      		)
			);
			$router = $hostnameRouter->chain($router);
		}
		
		$this->_front->getRouter()->addRoute('page', $router);

		$router = new Zend_Controller_Router_Route_Static(
			'404',
			array(
				'module'		=> 'core',
				'controller' 	=> 'error',
				'action' 		=> '404'
			)
		);

		$this->_front->getRouter()->addRoute('notfound-404', $router);
	}
}
