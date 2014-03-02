<?php

require_once "Zend/View/Helper/Abstract.php";

class Gazel_View_Helper_HumanFileSize extends Zend_View_Helper_Abstract
{
	public function humanFileSize($size)
	{
		if($size!=0 && $size!=''){
			$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
			return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
		}else{
			return 0;
		}
	}
}