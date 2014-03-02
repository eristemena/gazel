<?php

require_once "Gazel/Module/Setup/Interface.php";
require_once "Gazel/Config.php";

abstract class Gazel_Module_Setup_Abstract implements Gazel_Module_Setup_Interface
{
	protected $_db;
	protected $_errors = array();
	protected $_config;
	
	public function __construct()
	{
		$this->_config = Gazel_Config::getInstance();
	}
	
	public function getTableName($tableName)
	{
		return $this->_config->getTableName($tableName);
	}
	
	public function setDb($db)
	{
		$this->_db = $db;
	}
	
	public function setErrors($errors)
	{
		$this->_errors=$errors;
	}
	
	public function addError($error)
	{
		$this->_errors[]=$error;
	}
	
	public function getErrors()
	{
		return $this->_errors;
	}
	
	public function install()
	{
		
	}
	
	public function uninstall()
	{
		
	}

	public function __get($name)
	{
		if ( substr($name,0,2)=='__' )
		{
			return $this->getTableName(substr($name,2));
		}
		else
		{
			return $this->$name;
		}
	}
}