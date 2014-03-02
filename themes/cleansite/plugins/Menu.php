<?php

require_once "Gazel/Plugin/Abstract.php";

class Gazel_Plugin_Theme_Menu extends Gazel_Plugin_Abstract
{
	public function onAdminThemeMenu()
	{
		$m=array();
		$m['name'] = 'Clean Site';
		$m['menu'] = array(
			array('Configuration','config.php')
		);
		
		return $m;
	}
}