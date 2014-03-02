<?php

require_once "Zend/Controller/Front.php";

abstract class Gazel_Controller_Router_Abstract
{
	protected $_front=null;
	
	public function __construct()
	{
		$this->_front=Zend_Controller_Front::getInstance();
	}
	
	public function init()
	{
	}
}