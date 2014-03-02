<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_Router_Search extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$router = new Zend_Controller_Router_Route(
			'search/:query/*',
			array(
				'module'	=> 'core',
				'controller' => 'page',
				'action' => 'search',
				'query'	=> ''
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
		
		$this->_front->getRouter()->addRoute('search', $router);
	}
}
