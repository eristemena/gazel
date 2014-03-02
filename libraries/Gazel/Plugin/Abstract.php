<?php

abstract class Gazel_Plugin_Abstract
{
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;

	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response;

	/**
	 * @var string name of the plugin
	 */
	protected $_pluginName;

	/**
	 * @var array Plugin options
	 */
	protected $_pluginOptions;

	/**
	 * Set request object
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 * Get DB Connection
	 */
	protected function _getDb()
	{
		$dbi=Gazel_Db::getInstance();
		return $dbi->getDb();
	}

	/**
	 * Get request object
	 *
	 * @return Zend_Controller_Request_Abstract $request
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Set response object
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setResponse(Zend_Controller_Response_Abstract $response)
	{
		$this->_response = $response;
		return $this;
	}

	public function getFrontController()
	{
		require_once "Zend/Controller/Front.php";

		return Zend_Controller_Front::getInstance();
	}

	public function url($urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		$router = $this->getFrontController()->getRouter();
		return $router->assemble($urlOptions, $name, $reset, $encode);
	}

	/**
	* Get response object
	*
	* @return Zend_Controller_Response_Abstract $response
	*/
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Set plugin name (@see Config.php)
	 */
	public function setName($name)
	{
		$this->_pluginName = $name;
	}

	/**
	 * Get plugin name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_pluginName;
	}

	/**
	 * Set plugin title (@see Config.php)
	 */
	public function setTitle($title)
	{
		$this->_pluginTitle = $title;
	}

	/**
	 * Get plugin title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_pluginTitle;
	}

	/**
	 * @return array plugin options
	 */
	public function getPluginOptions()
	{
		return $this->_pluginOptions;
	}

	/**
	 * @param array $options plugin options
	 */
	public function setPluginOptions($options)
	{
		if( !$options || !is_array($options) ){
			$options = array();
		}

		$this->_pluginOptions = $options;
	}

	public function onLoadConfig(Gazel_Config $configFile)
	{

	}

	/**
     * When frontend render page head (<head></head>)
     *
     * @param string $head Head content
     * @return string
     */
	public function onFrontendRenderHead($head)
	{
		return $head;
	}

	/**
     * When frontend render page body (<body></body>)
     *
     * @param string $body Page content
     * @return string
     */
	public function onFrontendRenderBody($body)
	{
		return $body;
	}

	/**
	 * Prepend Admin Menu
	 *
	 * @param Gazel_Menu_Xml $menu
	 * @return Gazel_Menu_Xml
	 */
	public function onPrependAdminMenu($menu)
	{
		return $menu;
	}

	/**
	 * Append Admin Menu
	 *
	 * @param Gazel_Menu_Xml $menu
	 * @return Gazel_Menu_Xml
	 */
	public function onAppendAdminMenu($menu)
	{
		return $menu;
	}

	/**
	 * Render panel
	 *
	 * @params Gazel_Form $form Form to render
	 * @return Gazel_Form
	 */
	public function onAdminRenderPanel($form)
	{
		return $form;
	}


	/**
	 * When application error happen
	 *
	 * @params string $code HTTP Error Code
	 * @params Zend_Controller_Action_Exception $exception
	 */
	public function onApplicationError($code, Exception $exception)
	{
		
	}

	/**
	 * Execute when route shutdown on MVC cycle
	 *
	 * @params Zend_Controller_Request_Abstract $request
	 */
	public function onRouteShutdown(Zend_Controller_Request_Abstract $request)
	{

	}

	/*
	 * On render frontend
	 *
	 * @deprecated since 2.5.1
	 */
	public function onFrontendRender($body)
	{
		return $body;
	}

	/*
	 * Untuk memasukan script (js/css) di head
	 * @deprecated since 2.5.1
	 */
	public function onFrontedHeader()
	{}

	/*
	 * Untuk memasukan script (js/css) di footer
	 * @deprecated since 2.5.1
	 */
	public function onFrontedFooter()
	{}

	/*
	 * Untuk tampilan di admin
	 * @deprecated since 2.5.1
	 */
	public function onAdminView()
	{}

	/*
	 * Untuk tampilan di admin
	 * @deprecated since 2.5.1
	 */
	public function onAdminThemeMenu()
	{}

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
