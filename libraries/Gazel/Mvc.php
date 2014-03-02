<?php

class Gazel_Mvc
{
	private static $instance;
	
	public $config=null;
	protected $_front=null;
	
	protected $_publicdir;
	protected $_adminpath;
	
	/**
	 * Additional routers
	 **/
	protected $_routers=array();
	
	private function __construct() 
	{
		require_once "Gazel/Config.php";
		$this->config=Gazel_Config::getInstance();
		
		require_once "Zend/Controller/Front.php";
		$this->_front=Zend_Controller_Front::getInstance();
	}
	
	public static function getInstance() 
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		
		return self::$instance;
	}
	
	public function __clone()
	{}
	
	public function __set($nm, $val)
	{
		$this->$nm=$val;
	}
	
	public function addRouter($router)
	{
		$this->_routers[]=$router;
	}
	
	public function getRouters()
	{
		return $this->_routers;
	}
	
	public function loadThemePlugins()
	{
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$plugindir = $this->config->publicdir.'/themes/'.$this->config->themename.'/plugins';
		
		if ( is_dir($plugindir) && is_readable($plugindir) )
		{
			if ($handle = opendir($plugindir)) 
			{
				while (false !== ($file = readdir($handle))) 
				{
					if ( is_file($plugindir.'/'.$file) )
					{
						require_once $plugindir.'/'.$file;
						
						$name=str_replace('.php','',basename($file));
				  		$classname='Gazel_Plugin_Theme_'.$name;
						$plugin=new $classname();
						$pluginBroker->registerPlugin($plugin);
					}
				}
				closedir($handle);
			}
		}
	}
	
	public function phpSetup()
	{
		if (get_magic_quotes_gpc()) 
		{
			function stripslashes_deep($value)
			{
				$value = is_array($value) ?
		                    array_map('stripslashes_deep', $value) :
		                    stripslashes($value);
				
				return $value;
			}
			
			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
			$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}
		
		if ( $this->config->debug )
		{
			ini_set('display_errors','on');
			error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
		}
		else
		{
			ini_set('display_errors','off');
		}
	}
	
	public function install()
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		
		$front->addModuleDirectory($this->config->applicationdir.'/modules');
		
		$front->setDefaultModule('core');
		$front->setDefaultControllerName('config');
		
		require_once "Zend/Layout.php";
		Zend_Layout::startMvc();
		
		$front->dispatch();
		exit;
	}
	
	public function start()
	{
		// starting session
		require_once "Zend/Session.php";
		Zend_Session::start();
		
		// PHP initial setup
		$this->phpSetup();
		
		// directories
		$this->config->configdir = dirname($this->config->configfile);
		//$this->config->userdatadir = $this->config->publicdir.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'user';
		$this->config->userdir = $this->config->userdatadir; // back compatibility
		$this->config->cachedir = $this->config->publicdir.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache';
		
		/** init config if configuration file exists **/
		if ( $this->config->isInstalled() )
		{
			$this->config->initConfig();
		}
		else
		{
			$this->install();
		}
		/** end init config **/
		
		// main module directory
		$this->_front->addModuleDirectory($this->config->applicationdir.'/modules');
		
		// default module
		$this->_front->setDefaultModule('core');
		
		// default controller
		$this->_front->setDefaultControllerName('page');
		
		$this->loadThemePlugins();
		
		// register plugins
		require_once "Gazel/Controller/Plugin/RegisterPlugin.php";
		$this->_front->registerPlugin(new Gazel_Controller_Plugin_RegisterPlugin());
		
		// init
		require_once "Gazel/Controller/Plugin/Init.php";
		$this->_front->registerPlugin(new Gazel_Controller_Plugin_Init());
		
		// debug
		require_once "Gazel/Controller/Plugin/Debug.php";
		$this->_front->registerPlugin(new Gazel_Controller_Plugin_Debug());
		
		require_once "Zend/Layout.php";
		Zend_Layout::startMvc();
		
		if ( $this->config->multipleuser )
		{
			// fix domain for multiple user
			if ( $_SERVER['HTTP_HOST'] == $this->config->configinstance->mu->domain ) // http://gazelmu.com (master)
			{
				$_SERVER['HTTP_HOST'] = 'www.'.$_SERVER['HTTP_HOST'];
			}
			elseif ( $_SERVER['HTTP_HOST'] == 'www.'.$this->config->configinstance->mu->domain ) // http://www.gazelmu.com (master)
			{
				// do nothing
			}
			elseif ( substr($_SERVER['HTTP_HOST'],0,4)=='www.' ) // http://www.eristemena.gazelmu.com (user)
			{
				$_SERVER['HTTP_HOST'] = substr($_SERVER['HTTP_HOST'],4);
			}
		}
				
		$this->_front->dispatch();
	}
	
}