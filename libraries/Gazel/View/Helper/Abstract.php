<?php

/**
 * @see Zend_View_Helper_Abstract
 */
require_once "Zend/View/Helper/Abstract.php";

/**
 * @see Gazel_Db
 */
require_once "Gazel/Db.php";

/**
 * @see Gazel_Config
 */
require_once "Gazel/Config.php";

/**
 * @category   Gazel
 * @package    Gazel_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2000-2011 PT Inti Artistika Solusitama (http://www.inarts.co.id)
 */
abstract class Gazel_View_Helper_Abstract extends Zend_View_Helper_Abstract
{
	/**
	 * Get DB Connection
	 */
	protected function _getDb()
	{
		$dbi=Gazel_Db::getInstance();
		return $dbi->getDb();
	}

	protected function _getFront()
	{
		require_once "Zend/Controller/Front.php";
		$front = Zend_Controller_Front::getInstance();

		return $front;
	}

	protected function _getRequest()
	{
		return $this->_getFront()->getRequest();
	}

	protected function _getConfig()
	{
		require_once "Gazel/Config.php";
		return Gazel_Config::getInstance();
	}
	
	public function routerAssemble(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		require_once "Zend/Controller/Front.php";
		
		$configi=Gazel_Config::getInstance();
		if ( $configi->configinstance->mu->active == "true" )
		{
			$urlOptions['username'] = $configi->mUser;
		}
		
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($urlOptions, $name, $reset, $encode);
	}
	
	public function __get($name)
	{
		$config=Gazel_Config::getInstance();
		
		if ( substr($name,0,2)=='__' )
		{
			return $config->getTableName(substr($name,2));
		}
		elseif ($name=='_db')
		{
			return $this->_getDb();
		}
		else
		{
			return $this->$name;
		}
	}
}