<?php

require_once "Gazel/View/Helper/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_View_Helper_DimageUrl extends Gazel_View_Helper_Abstract
{
	public function dimageUrl($module, $width, $height, $id, $ext)
	{
		$params=array();
    $params[0] = $module;
    $params[1] = $width;
    $params[2] = $height;
    $params[3] = $id;
    $params[4] = $ext;
    
    return $this->routerAssemble($params, 'dimage', true);
	}
	
}