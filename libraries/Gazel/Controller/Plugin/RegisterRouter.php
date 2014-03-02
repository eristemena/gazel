<?php

require_once "Zend/Controller/Plugin/Abstract.php";
require_once "Zend/Controller/Front.php";
require_once "Gazel/Config.php";

class Gazel_Controller_Plugin_RegisterRouter extends Zend_Controller_Plugin_Abstract
{
	protected $_config=null;
	
	public function __construct()
	{
		$this->_config=Gazel_Config::getInstance();
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$this->loadRouter('Page');
		$this->loadRouter('Admin');
		$this->loadRouter('Search');
		$this->loadRouter('Assets');
		$this->loadRouter('Themeassets');
		$this->loadRouter('Dimage');
		$this->loadRouter('Config');
		
		// additional routers
		
		if ( $this->_config->hasSetting() && is_array($this->_config->routers->router) )
		{
			foreach ( $this->_config->routers->router as $router )
			{
				$this->loadRouter($router);
			}
		}
	}
	
	public function loadRouter($routerName)
	{
		if ( file_exists($this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.'.php') )
		{
			 $routerPath = $this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.'.php';
		}
		elseif ( file_exists($this->_config->gazeldir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.'.php') )
		{
			$routerPath = $this->_config->gazeldir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.'.php';
		}
		else
		{
			$routerPath = '';
		}
		
		if ( $routerPath )
		{
			require_once $routerPath;
			$routerClass='Gazel_Router_'.$routerName;
			$router=new $routerClass();
			
			$router->init();
		}
	}
}