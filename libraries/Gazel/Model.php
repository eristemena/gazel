<?php

class Gazel_Model
{
	private $_config=null;
	private $_db=null;
	protected $_front=null;
	
	public function __construct()
	{
		require_once "Gazel/Config.php";
		$this->_config=Gazel_Config::getInstance();
		
		$this->_db = $this->getDb();
		
		require_once "Zend/Controller/Front.php";
		$this->_front = Zend_Controller_Front::getInstance();
		
		$this->init();
	}
	
	/**
	 * You can override this to bootstrap your model
	 */
	public function init(){}
	
	public function getDb()
	{
		require_once "Gazel/Db.php";
		$db=Gazel_Db::getInstance();
		return $db->getDb();
	}
	
	public function __call($methodName, $args)
	{
		require_once "Gazel/Exception.php";
		throw new Gazel_Exception(sprintf('%s() is not a valid method!',$methodName));
	}
	
	public function __get($name)
	{
		if ( substr($name,0,2)=='__' )
		{
			return $this->_config->getTableName(substr($name,2));
		}
		else
		{
			return $this->$name;
		}
	}
	
	public function loadModel($model=null,$module=null)
	{
		if ( $module==null ) {
			$module = $this->_getParam('module');
		}
		if ( $model==null ) {
			$model = $this->_getParam('controller');
		}
		$modeldir=$this->_front->getModuleDirectory($module).DIRECTORY_SEPARATOR.'models';
		
		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();
		
		$fmodel=$modeldir.DIRECTORY_SEPARATOR.$filter->filter($model).'Model.php';
		if ( !file_exists($fmodel) )
		{
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception('Can not find the model: '.$fmodel);
		}
		else
		{
			require_once $fmodel;
			
			$cmodel=$filter->filter($module).'_Model_'.$filter->filter($model);
			return new $cmodel();
		}
	}
}