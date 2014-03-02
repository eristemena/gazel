<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_View_Helper_ThemeUrl extends Zend_View_Helper_Abstract
{
	public function themeUrl($file=null)
	{
		$config = Gazel_Config::getInstance();
		
		require_once 'Zend/Controller/Front.php';
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		
    // Remove scriptname, eg. index.php from baseUrl
		$baseUrl = $this->_removeScriptName($baseUrl);
    
		$themeName = $config->themename;
		
		if (null !== $file) {
			$file = '/' . ltrim($file, '/\\');
		}
		
		$out = $baseUrl.'/themes/'.$themeName.$file;
		
		return $out;
	}
	
    protected function _removeScriptName($url)
    {
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            // We can't do much now can we? (Well, we could parse out by ".")
            return $url;
        }

        if (($pos = strripos($url, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }
}