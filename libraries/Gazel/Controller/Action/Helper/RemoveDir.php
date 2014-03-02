<?php

class Gazel_Controller_Action_Helper_RemoveDir extends Zend_Controller_Action_Helper_Abstract
{
	public function removeDir($dir, $DeleteMe=true)
	{
		if(!$dh = @opendir($dir)) return;
		while (false !== ($obj = readdir($dh))) 
		{
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink($dir.'/'.$obj)) $this->removeDir($dir.'/'.$obj, true);
		}
		if ($DeleteMe){
			closedir($dh);
			@rmdir($dir);
		}
	}
	
	public function direct($dir, $DeleteMe=true)
	{
		$this->removeDir($dir, $DeleteMe);
	}
}