<?php

class Gazel_Controller_Action_Helper_Dimage extends Zend_Controller_Action_Helper_Abstract
{
	public function getDimageUrl($module, $width, $height, $id, $ext)
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		$router=$front->getRouter();
		
		$params=array();
        $params[0] = $module;
        $params[1] = $width;
        $params[2] = $height;
        $params[3] = $id;
        $params[4] = $ext;
    
        require_once "Gazel/Config.php";
        $configi=Gazel_Config::getInstance();

        if ( $configi->configinstance->mu->active == "true" )
        {
        	$params['username'] = $configi->mUser;
        }
        
        return $front->getRouter()->assemble($params, 'dimage', true);
	}
}