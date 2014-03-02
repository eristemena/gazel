<?php

require_once "Zend/Controller/Plugin/Abstract.php";
require_once "Zend/Controller/Front.php";
require_once "Gazel/Plugin/Broker.php";
require_once "Gazel/Tool.php";

class Gazel_Controller_Plugin_RegisterPlugin extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		
		// register plugins
		//$pluginBroker->registerPluginFile('Config');
		//$pluginBroker->registerPluginFile('Multiuser');
		
		// set request
		$pluginBroker->setRequest($request);
	}
	
}