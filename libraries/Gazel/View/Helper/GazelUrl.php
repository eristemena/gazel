<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Config.php";

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 */
class Gazel_View_Helper_GazelUrl extends Zend_View_Helper_Abstract
{
    public function gazelUrl(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $config=Gazel_Config::getInstance();
        if ( $config->configinstance->mu->active=="true" ) 
        {
        	require_once "Zend/Controller/Front.php";
        	$front=Zend_Controller_Front::getInstance();
					$request=$front->getRequest();
					
        	$urlOptions['username'] = $request->getParam('username');
        }
        
        return $router->assemble($urlOptions, $name, $reset, $encode);
    }
}
