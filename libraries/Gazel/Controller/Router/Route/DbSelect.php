<?php

require_once 'Zend/Controller/Router/Route/Abstract.php';
require_once "Gazel/Db.php";
require_once "Gazel/Tool.php";

class Gazel_Controller_Router_Route_DbSelect extends Zend_Controller_Router_Route
{
	protected $_defaults=array();
	protected $_alias=null;
	
	public function __construct($route, $defaults = array(), $reqs = array(), $scheme = null)
	{
		$this->_defaults=$defaults;
	}
	
	public function match($path)
	{
		$path = trim($path,$this->_urlDelimiter);
		if ( count(explode($this->_urlDelimiter,$path))>1 ) {
			return false;
		}
		
		$frontController = Zend_Controller_Front::getInstance();
    $request         = $frontController->getRequest();
    
		// Request keys
    $moduleKey     = $request->getModuleKey();
    $controllerKey = $request->getControllerKey();
   	$actionKey     = $request->getActionKey();
		
		// default
		$moduleName     = $frontController->getDefaultModule();
    $controllerName = $frontController->getDefaultControllerName();
    $actionName     = $frontController->getDefaultAction();
    
    $dbi=Gazel_Db::getInstance();
    $db=$dbi->getDb();
    $res=$db->fetchAssoc($db->select()->from('page')->where('page_alias=?',$path));
    
    if ( count($res)>0 )
    {
			$params=array();
			
			if ( $this->_defaults['module'] ) {
				$params[$moduleKey]=$this->_defaults[$moduleKey];
			} else {
				$params[$moduleKey]=$moduleName;
			}
			
			if ( $this->_defaults[$controllerKey] ) {
				$params[$controllerKey]=$this->_defaults[$controllerKey];
			} else {
				$params[$controllerKey]=$controllerName;
			}
			
			if ( $this->_defaults[$actionKey] ) {
				$params[$actionKey]=$this->_defaults[$actionKey];
			} else {
				$params[$actionKey]=$actionName;
			}
			
			$params['alias']=$path;
			$this->_alias = $path;
			
			return $params;
		}
		else
		{
			return false;
		}
	}
	
	public function assemble($data = array(), $reset = false, $encode = false)
	{
		//$url='/'.$data['alias'];
		if ( $data['alias'] ) {
			$url='/'.$data['alias'];
		} else {
			$url='/'.$this->_alias;
		}
		
		return ltrim($url,$this->_urlDelimiter);
	}
	
  public static function getInstance(Zend_Config $config)
  {
  	
  }
}