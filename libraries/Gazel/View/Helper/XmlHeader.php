<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Db.php";

class Gazel_View_Helper_XmlHeader extends Zend_View_Helper_Abstract
{
	public function xmlHeader()
	{
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
	}
}