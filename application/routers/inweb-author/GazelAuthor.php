<?php

require_once "Gazel/Controller/Router/Abstract.php";
require_once "Zend/Controller/Router/Route.php";
require_once "Zend/Controller/Router/Route/Hostname.php";
require_once "Gazel/Config.php";

class Gazel_Router_GazelAuthor extends Gazel_Controller_Router_Abstract
{
	public function init()
	{
		$configi=Gazel_Config::getInstance();
		
		$router = new Zend_Controller_Router_Route(
			'whowroteme',
			array(
				'module'			=> 'core',
				'controller'		=> 'index',
				'action'			=> 'whowroteme'
			)
		);
		
		$this->_front->getRouter()->addRoute('gazel-author', $router);
	}
}
