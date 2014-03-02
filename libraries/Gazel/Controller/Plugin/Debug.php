<?php

require_once "Zend/Controller/Plugin/Abstract.php";
require_once "Gazel/Config.php";
require_once "Gazel/Tool.php";
require_once "Zend/Controller/Front.php";
require_once "Zend/Wildfire/Plugin/FirePhp.php";
require_once "Zend/Wildfire/Plugin/FirePhp/TableMessage.php";

require_once "Zend/Log.php";
require_once "Zend/Log/Writer/Firebug.php";
require_once "Zend/Controller/Request/Http.php";
require_once "Zend/Controller/Response/Http.php";

class Gazel_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
	protected $_label='';
	protected $_config=null;
	protected $_message=null;
	protected $_front=null;
	
	protected $_debugRow=array();
	
	public function __construct()
	{
		$this->_label='Debug';
		$this->_debugRow[]=array('Event','Action','Controller','Module','Params'); // headers
		
    $this->_config=Gazel_Config::getInstance();
    
    $this->_front=Zend_Controller_Front::getInstance();
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$this->_config->scriptTimeStart();
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		if ( $this->_config->debug )
		{
			$routeName = $this->_front->getRouter()->getCurrentRouteName();
			
			$nodebug=array('themeassets','assets');
			if ( in_array($routeName,$nodebug) )
			{
				$this->_config->debug=false;
			}
			else
			{
				$this->_label .= ' (Route name: '.$this->_front->getRouter()->getCurrentRouteName().') ';
				$this->_addLogMvcParams('routeShutdown',$request);
				
				Gazel_Tool::logFb('Route Name (shutdown): '.$routeName);
				Gazel_Tool::logFb($this->_front->getRequest()->getParams());
				$this->_flushConfigLog();
			}
		}
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		//$this->_addLogMvcParams('preDispatch',$request);
	}
	
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ( $this->_config->debug )
		{
			if ( $this->_request->isDispatched() ) {
				$this->_addLogMvcParams('postDispatch (d)',$request);
			} else {
				$this->_addLogMvcParams('postDispatch',$request);
			}
		}
	}
	
	public function dispatchLoopShutdown()
	{
		if ( $this->_config->debug )
		{
			$request=$this->_front->getRequest();
			$this->_addLogMvcParams('dispatchLoopShutdown',$request);
			
			$sec = $this->_config->scriptTimeEnd();
			$this->_label .= ' (Execution time: '.$sec.' sec)';
			
			$this->_flushLog();
			$this->_flushDbLog();
	    
			$view=$this->_front->getPlugin('Zend_Layout_Controller_Plugin_Layout')->getLayout()->getView();
			//Gazel_Tool::logFb($view->getScriptPaths());
			
			$p=$this->_front->getPlugins();
			$pn=array();
			foreach ( $p as $c ){
				$pn[]=get_class($c);
			}
			//Gazel_Tool::logFb($pn);
		}
	}
	
	private function _addLogMvcParams($event, Zend_Controller_Request_Abstract $request)
	{
		if ( $this->_config->debug )
		{
			$module=$request->getModuleName();
			$controller=$request->getControllerName();
			$action=$request->getActionName();
			
	    $this->_debugRow[]=array($event,$action,$controller,$module,$request->getParams());
	  }
	}
	
	private function _flushLog()
	{
		if ( $this->_config->debug )
		{
			$writer = new Zend_Log_Writer_Firebug();
			$logger = new Zend_Log($writer);
			
			$request = new Zend_Controller_Request_Http();
			$response = new Zend_Controller_Response_Http();
			$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
			$channel->setRequest($request);
			$channel->setResponse($response);
			
			$writer->setPriorityStyle(8, 'TABLE');
			$logger->addPriority('TABLE', 8);
			
			$table=array($this->_label,$this->_debugRow);
			
			// Start output buffering
			ob_start();
			
			$logger->table($table);
			
			// Flush log data to browser
			$channel->flush();
			$response->sendHeaders();
		}
	}
	
	private function _flushConfigLog()
	{
		if ( $this->_config->debug )
		{
			$writer = new Zend_Log_Writer_Firebug();
			$logger = new Zend_Log($writer);
			
			$request = new Zend_Controller_Request_Http();
			$response = new Zend_Controller_Response_Http();
			$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
			$channel->setRequest($request);
			$channel->setResponse($response);
			
			$writer->setPriorityStyle(8, 'TABLE');
			$logger->addPriority('TABLE', 8);
			
			require_once "Zend/Version.php";
			require_once "Gazel/Version.php";
			
			// routers
			$routers=array();
			if ( is_array($this->_front->getRouter()->getRoutes()) ){
				foreach ( $this->_front->getRouter()->getRoutes() as $r => $o ){
					$routers[$r]=get_class($o);
				}
			}
			
			$table=array('Config',array(
				array('Name','Value'),
				array('baseurl',$this->_config->baseurl),
				array('publicdir',$this->_config->publicdir),
				array('gazeldir',$this->_config->gazeldir),
				array('applicationdir',$this->_config->applicationdir),
				array('themespath',$this->_config->themespath),
				array('themesurl',$this->_config->themesurl),
				array('themename',$this->_config->themename),
				array('themepath',$this->_config->themepath),
				array('themeurl',$this->_config->themeurl),
				array('zend version',Zend_Version::VERSION),
				array('gazel version',Gazel_Version::VERSION),
				array('routers',$routers)
			));
			
			// Start output buffering
			ob_start();
			
			$logger->table($table);
			
			// Flush log data to browser
			$channel->flush();
			$response->sendHeaders();
		}
	}
	
	private function _flushDbLog()
	{
		if ( $this->_config->debug )
		{
			$writer = new Zend_Log_Writer_Firebug();
			$logger = new Zend_Log($writer);
			
			$request = new Zend_Controller_Request_Http();
			$response = new Zend_Controller_Response_Http();
			$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
			$channel->setRequest($request);
			$channel->setResponse($response);
			
			$writer->setPriorityStyle(8, 'TABLE');
			$logger->addPriority('TABLE', 8);
			
			require_once "Zend/Version.php";
			require_once "Gazel/Version.php";
			
			// routers
			$routers=array();
			if ( is_array($this->_front->getRouter()->getRoutes()) ){
				foreach ( $this->_front->getRouter()->getRoutes() as $r => $o ){
					$routers[$r]=get_class($o);
				}
			}
			
			require_once "Gazel/Db.php";
			$dbi=Gazel_Db::getInstance();
			$db=$dbi->getDb();
			$profiler = $db->getProfiler();
			
			$data=array();
			$data[]=array('Elapsed','Query','Params');
			foreach ($profiler->getQueryProfiles() as $query) {
				$data[]=array(
					$query->getElapsedSecs(),
					$query->getQuery(),
					$query->getQueryParams()
				);
			}
			
			$table=array('All DB Queries ('.$profiler->getTotalNumQueries().' @ '.$profiler->getTotalElapsedSecs().' sec)',$data);
			
			// Start output buffering
			ob_start();
			
			$logger->table($table);
			
			// Flush log data to browser
			$channel->flush();
			$response->sendHeaders();
		}
	}
}