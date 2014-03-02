<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Gazel/Config.php";
require_once "Zend/Controller/Router/Route/Regex.php";

class Gazel_Router_Dimage extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$configi=Gazel_Config::getInstance();
		
		$router = new Zend_Controller_Router_Route_Regex(
			'dimages/(.+)/(.+)/(.+)/(.+)\.(jpg|jpeg|png|gif)',
			array(
				'module'			=> 'core',
				'controller'	=> 'image',
				'action'			=> 'dispatch'
			),
			array(
				1	=> 'mod',
				2	=> 'width',
				3 => 'height',
				4 => 'id',
				5 => 'ext'
			),
			'dimages/%s/%s/%s/%d.%s'
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
		
		$this->_front->getRouter()->addRoute('dimage', $router);
	}
}
