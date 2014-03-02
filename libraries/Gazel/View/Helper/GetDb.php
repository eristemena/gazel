<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Db.php";

class Gazel_View_Helper_GetDb extends Zend_View_Helper_Abstract
{
	public function getDb()
	{
		$dbi=Gazel_Db::getInstance();
		$db=$dbi->getDb();
		
		return $db;
	}
}