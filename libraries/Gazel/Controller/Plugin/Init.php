<?php

require_once "Zend/Controller/Plugin/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_Controller_Plugin_Init extends Zend_Controller_Plugin_Abstract
{
	protected $_config=null;
	
	public function __construct()
	{
		$this->_config=Gazel_Config::getInstance();
	}
	
	public function loadRouter($routerName)
	{
		$routerName = strtolower($routerName);
		require_once "Zend/Filter/Word/DashToCamelCase.php";
		$filter=new Zend_Filter_Word_DashToCamelCase();
		$fileName=$filter->filter($routerName);
		
		if ( file_exists($this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.DIRECTORY_SEPARATOR.$fileName.'.php') )
		{
			 $routerPath = $this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName.DIRECTORY_SEPARATOR.$fileName.'.php';
		}
		else
		{
			$routerPath = '';
		}
		
		if ( $routerPath )
		{
			require_once $routerPath;
			$routerClass='Gazel_Router_'.$fileName;
			$router=new $routerClass();
			
			$router->init();
		}
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		
		// init db
		$this->_config->initDb();
		
		/** register default routers **/
		$this->loadRouter('page');
		$this->loadRouter('admin');
		$this->loadRouter('search');
		$this->loadRouter('dimage');
		/** end register default routers **/
		
		if ( $this->_config->isInstalled() )
		{
			// register additional routers from database
			$dbinstance=Gazel_Db::getInstance();
			$db=$dbinstance->getDb();
			
			$res=$db->fetchAll('select router_name from '.$this->_config->getTableName('router'));
			foreach ( $res as $r ){
				$this->loadRouter($r['router_name']);
			}
		}
		
		
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		
		$host = $request->getHttpHost();
		
		if ( $this->_config->multipleuser )
		{
			if ( $host == $this->_config->configinstance->mu->master || $host == 'www.'.$this->_config->configinstance->mu->master )
			{
				$this->_config->mUser='';
				$this->_config->mMaster=true;
				
				$authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
				if ( ($front->getRouter()->getCurrentRouteName()=='admin' && $authAdmin->auth->asUser) || $authAdmin->auth->asUser=='template' ){
					$this->_config->tablePrefix=$authAdmin->auth->asUser.'_';
				}
			}
			else
			{
				// check wheter username exists
				require_once "Gazel/Db.php";
				$dbinstance=Gazel_Db::getInstance();
				$dbinstance->setConnection($this->_config->configinstance);
				$db=$dbinstance->getDb();
				if ( !$res=$db->fetchRow('select * from users where user_login=?',$request->getParam('username')) )
				{
					header("Location: http://www.".$this->_config->configinstance->mu->master.'?username='.$request->getParam('username'));
					exit;
				}
				else
				{
					$this->_config->mUser=$request->getParam('username');
					$this->_config->tablePrefix=$this->_config->mUser.'_';
					$this->_config->mMaster=false;
				}
			}
		}
		else
		{
			// non multiple user
			$this->_config->mUser='';
			$this->_config->mMaster=false;
		}
		
		if ( $this->_config->multipleuser )
		{
			// user data dir/url for multiple user
			$this->_config->userdatadir = $this->_config->publicdir.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.$this->_config->mUser;
			$this->_config->userdataurl = $this->_config->baseurl.'/data/user/'.$this->_config->mUser;
		}
		else
		{
			// user data dir/url
			$this->_config->userdatadir = $this->_config->publicdir.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'user';
			$this->_config->userdataurl = $this->_config->baseurl.'/data/user';
		}
		
		/**
		 * Load configuration from database
		 **/
		require_once "Gazel/Db.php";
		$dbinstance=Gazel_Db::getInstance();
		$db=$dbinstance->getDb();
		
		$res=$db->fetchAssoc($db->select()->from($this->_config->getTableName('config')));
		foreach ( $res as $r )
		{
			$name=$r['config_name'];
			$this->_config->$name=$r['config_value'];
		}
		
		/** load plugins **/
		$this->_config->loadPlugins();
		
		/** init theme **/
		$this->_config->initTheme();

		// plugin instance
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$pluginBroker->onRouteShutdown($request);
	}

	public function dispatchLoopShutdown()
	{
		$body = $this->getResponse()->getBody();
		
		$htmlpart = array();

		if(preg_match('|^(.*)<html[^>]*>|ismU', $body, $params)){
			$htmlpart['tag-before-html'] = $params[1];
		}

		if( preg_match('|(<html[^>]*>)(.*)(</html>)|ismU',$body,$params) ){
			$htmlpart['html-tag-open'] = $params[1];
			$htmlpart['html-tag-close'] = $params[3];
		}

		if( preg_match('|(<head[^>]*>)(.*)(</head>)|ismU',$body,$params) ){
			$htmlpart['head-tag-open'] = $params[1];
			$htmlpart['head'] = $params[2];
			$htmlpart['head-tag-close'] = $params[3];
		}

		if( preg_match('|(<body[^>]*>)(.*)(</body>)|ismU',$body,$params) ){
			$htmlpart['body-tag-open'] = $params[1];
			$htmlpart['body'] = $params[2];
			$htmlpart['body-tag-close'] = $params[3];
		}
		
		// plugin instance
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		
		if($front->getRouter()->getCurrentRouteName() =='admin')
		{
			// TODO: Add plugin here
		}
		else
		{
			$htmlpart['body'] = $pluginBroker->onFrontendRender($htmlpart['body']); // for backward compatibility only
			$htmlpart['body'] = $pluginBroker->onFrontendRenderBody($htmlpart['body']);
			$htmlpart['head'] = $pluginBroker->onFrontendRenderHead($htmlpart['head']);
			
			//$body = $pluginBroker->onFrontendFooter($body);
		}

		if( count($htmlpart)>0 && $htmlpart['body'] )
		{
			$body = 
				$htmlpart['tag-before-html']."\n".
				$htmlpart['html-tag-open']."\n".
				$htmlpart['head-tag-open'].
				$htmlpart['head']."\n".
				$htmlpart['head-tag-close']."\n".
				$htmlpart['body-tag-open'].
				$htmlpart['body']."\n".
				$htmlpart['body-tag-close']."\n".
				$htmlpart['html-tag-close']
			;
		}
		
		$this->getResponse()->setBody($body);
	}
	
}
