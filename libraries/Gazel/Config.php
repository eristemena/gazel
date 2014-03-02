<?php

require_once "Gazel/Db.php";
require_once "Gazel/Tool.php";

class Gazel_Config
{
	// Hold an instance of the class
	private static $instance;
	
	private $baseurl;
	
	/* multiple user setting */
	private $multipleuser=false;
	private $mUser="";
	private $mMaster=false;
	
	/* directories */
	private $gazeldir;
	private $publicdir;
	private $applicationdir;
	private $userdatadir;
	private $cachedir;
	private $moduledir;
	
	/* urls */
	private $userdataurl;
	
	private $config=array();
	private $configfile;
	private $configinstance=null; // Zend_Config
	private $adminpath;
	private $namespace=null;
	
	/* theme */
	private $themebasepath;
	private $themename;
	private $themeadminname;
	private $themepath;
	private $themeurl;
	private $themeadminurl;
	private $themesurl;
	private $themespath;
	
	/* session namespace */
	private $sessionAdminNamespace = "Gazel_Admin_Auth";
	
	/* table prefix */
	private $tablePrefix='';
	
	private $debug=false;
	
	// i'm using this for execution time
	private $msec_start;
	
	// A private constructor; prevents direct creation of object
	private function __construct() 
	{
		
	}
	
	// The singleton method
	public static function getInstance() 
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		
		return self::$instance;
	}
	
	// Prevent users to clone the instance
	public function __clone()
	{}
	
	public function initConfig()
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		
		if ( !$this->hasSetting() )
		{
			$request=$front->getRequest();
			$response=$front->getResponse();
			
			if ( $front->getRouter()->getCurrentRouteName()!='config' )
			{
				$this->redirectToConfig();
			}
		}
		else
		{
			/**
			 * Load configuration from file (xml)
			 **/
			require_once "Zend/Config/Xml.php";
			
			if ( !$this->isInstalled() )
			{
				$this->redirectToConfig();
			}
			
			$this->configinstance=new Zend_Config_Xml($this->configfile,$this->getNamespace());
			
			$this->adminpath=$this->configinstance->adminpath;
			$this->namespace=$this->configinstance->namespace;
			
			if ( $this->configinstance->mu->active=="true" ) 
			{
				$this->multipleuser=true;
			}
			else
			{
				$this->multipleuser=false;
			}

			if($this->configinstance->debug=='true')
			{
				$this->debug = true;

				ini_set('display_errors','on');
				error_reporting(E_ALL ^ E_NOTICE);
			}
			else
			{
				$this->debug = false;
				
				ini_set('display_errors','off');
			}
			
			$configArray=$this->configinstance->toArray();
			$routers=array();
			if ( isset($configArray['routers']['router']) )
			{
				if ( is_array($configArray['routers']['router']) )
				{
					foreach ( $configArray['routers']['router'] as $routerName )
					{
						$routers[]=$routerName;
					}
				}
				else
				{
					$routers[]=$configArray['routers']['router'];
				}
			}
			$this->routers=$routers;
			
			/**
			 * Load configuration from database
			 **/
			/*$dbinstance=Gazel_Db::getInstance();
			$dbinstance->setConnection($this->configinstance);
			$db=$dbinstance->getDb();
			
			$res=$db->fetchAssoc($db->select()->from($this->getTableName('config')));
			foreach ( $res as $r )
			{
				$name=$r['config_name'];
				$this->$name=$r['config_value'];
			}*/
			
			/**
			 * baseurl
			 **/
			require_once "Zend/Controller/Request/Http.php";
			$request = new Zend_Controller_Request_Http();
			$this->baseurl=$request->getScheme().'://'.$request->getHttpHost().$request->getBasePath();
			
			/**
			 * urls
			 **/
			$this->userdataurl='/data/user';
			
			/**
			 * Setting themepath
			 **/
			$this->initTheme();
		}
	}
	
	public function initFile($configpath,$namespace=null)
	{
		require_once "Zend/Config/Xml.php";
		
		if ( !$namespace ){
			$namespace=$this->getNamespace();
		}
		$this->configinstance=new Zend_Config_Xml($configpath,$namespace);
		
		/** baseurl **/
		require_once "Zend/Controller/Request/Http.php";
		$request = new Zend_Controller_Request_Http();
		$this->baseurl=$request->getScheme().'://'.$request->getHttpHost();
		
		/** init db **/
		//$this->initDb();
		
		
	}
	
	function initDb()
	{
		$dbinstance=Gazel_Db::getInstance();
		$dbinstance->setConnection($this->configinstance);
		
		$db=$dbinstance->getDb();
		$res=$db->fetchAssoc($db->select()->from($this->getTableName('config')));
		foreach ( $res as $r )
		{
			$name=$r['config_name'];
			$this->$name=$r['config_value'];
		}
	}
	
	public function initTheme()
	{
		$this->themespath=$this->publicdir.DIRECTORY_SEPARATOR.'themes';
		$this->themesurl=$this->baseurl.'/themes';
		$this->themepath=$this->themespath.DIRECTORY_SEPARATOR.$this->themename;
		$this->themeurl=$this->themesurl.'/'.$this->themename;
		$this->themeadminurl=$this->themesurl.'/'.$this->themeadminname;
	}
	
	public function getTableName($tableBaseName)
	{
		return $this->tablePrefix.$tableBaseName;
		/*if ( $this->multipleuser && !$this->mMaster )
		{
			return $this->mUser.'_'.$tableBaseName;
		}
		else
		{
			return $tableBaseName;
		}*/
	}
	
	public function initTableName($prefix)
	{
		$this->tableName=array();
		
		$t=array(
			'page'		=> $prefix.'page',
			'section'	=> $prefix.'section',
			'config'	=> $prefix.'config',
			'module'	=> $prefix.'module',
			'admin'		=> $prefix.'admin'
		);
		
		$this->tableName=$t;
	}
	
	public function getNamespace()
	{
		if ( $this->namespace==null )
		{
			$namespace=$_SERVER['SERVER_NAME'];
			
			if ( substr($namespace,0,4)=='www.' ) {
				$namespace=substr($namespace,4);
			}
		}
		else
		{
			$namespace=$this->namespace;
		}
		
		return $namespace;
	}
	
	public function hasSetting()
	{
		if ( is_readable($this->configfile) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isInstalled()
	{
		if ( !is_readable($this->configfile) )
		{
			return false;
		}
		else
		{
			require_once "Zend/Config/Xml.php";
			
			try {
				$configinstance=new Zend_Config_Xml($this->configfile,$this->getNamespace());
				
				if ( $configinstance->installed=='false' )
				{
					return false;
				}
				else
				{
					return true;
				}
				return true;
			} catch(Exception $e) {
				echo $e->getMessage();exit;
				return false;
			}
		}
	}
	
	public function redirectToConfig()
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		$router=$front->getRouter();
		$params=array('module'=>'core','controller'=>'config');
		$url=$router->assemble($params, 'default', true);
		
		if (!preg_match('#^(https?|ftp)://#', $url)) 
		{
			$host  = (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'');
			$proto = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=="off") ? 'https' : 'http';
			$port  = (isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:80);
			$uri   = $proto . '://' . $host;
			if ((('http' == $proto) && (80 != $port)) || (('https' == $proto) && (443 != $port))) {
				$uri .= ':' . $port;
			}
			
			$url = $uri . '/' . ltrim($url, '/');
		}
    
		header("Location: $url");
		exit;
	}
	
	public function saveXmlConfig($configArray)
	{
		require_once "Zend/Config/Xml.php";
		$configXml=new Zend_Config_Xml($this->configfile,null,array(
			'skipExtends'        => true,
	    'allowModifications' => true
	  ));
		$cfgArray=$configXml->toArray();
		
		$cfgArray[$this->namespace]=$configArray;
		
		require_once "Zend/Config.php";
		require_once "Zend/Config/Writer/Xml.php";
		$configXML = new Zend_Config($cfgArray, true);
		$writer = new Zend_Config_Writer_Xml(array(
							'config'   => $configXML,
              'filename' => $this->configfile));
		$writer->write();
	}
	
	public function loadPlugins()
	{
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$pluginpath = $this->applicationdir.'/plugins/';
		
		require_once "Gazel/Db.php";
		$dbinstance=Gazel_Db::getInstance();
		$db=$dbinstance->getDb();
		
		$res=$db->fetchAssoc($db->select()->from($this->getTableName('plugin')));
		foreach ( $res as $r )
		{
			$pluginName = $r['plugin_name'];
			$plugindir = $pluginpath.DIRECTORY_SEPARATOR.$pluginName;
			
			require_once "Zend/Filter/Word/DashToCamelCase.php";
			$filter=new Zend_Filter_Word_DashToCamelCase();
			$pluginName=$filter->filter($pluginName);
			
			$pluginFile = $plugindir.DIRECTORY_SEPARATOR.$pluginName.'.php';
			
			if ( is_file($pluginFile) )
			{
				require_once $pluginFile;
				
				$classname = $pluginName.'Plugin';
				$plugin = new $classname();
				$plugin->setName($r['plugin_name']);
				$plugin->setTitle($r['plugin_title']);
				$plugin->setPluginOptions(unserialize($r['plugin_options']));
				$pluginBroker->registerPlugin($plugin, null, unserialize($r['plugin_options']));
			}
		}
	}
	
	public function scriptTimeStart()
	{
		$this->msec_start=microtime(true);
	}
	
	public function scriptTimeEnd()
	{
		$msec=microtime(true)-$this->msec_start;
		
		return number_format($msec,5,',','.');
	}
	
	public function __set($nm, $val)
	{
		$this->$nm=$val;
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
}
