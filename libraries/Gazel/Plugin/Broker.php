<?php

require_once "Gazel/Plugin/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_Plugin_Broker extends Gazel_Plugin_Abstract
{
	/**
	 * Singleton instance
	 *
	 * Marked only as protected to allow extension of the class. To extend,
	 * simply override {@link getInstance()}.
	 *
	 * @var Gazel_Plugin_Broker
	 */
	protected static $_instance = null;
	
	/**
	 * Array of instance of objects extending Gazel_Plugin_Abstract
	 *
	 * @var array
	 */
	protected $_plugins = array();
  
	/**
	 * Instance of Zend_Controller_Request_Abstract
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request = null;
	
	/**
	 * Constructor
	 *
	 * Instantiate using {@link getInstance()}; Gazel plugin broker is a singleton
	 * object.
	 *
	 * @return void
	 */
	protected function __construct()
	{

	}

	/**
	 * Enforce singleton; disallow cloning
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Singleton instance
	 *
	 * @return Gazel_Plugin_Broker
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register a plugin.
	 *
	 * @param  Gazel_Plugin_Abstract $plugin
	 * @param  int $stackIndex
	 * @return Gazel_Plugin_Broker
	 */
	public function registerPlugin(Gazel_Plugin_Abstract $plugin, $stackIndex = null, $data=array())
	{
		if (false !== array_search($plugin, $this->_plugins, true)) {
			require_once 'Gazel/Exception.php';
			throw new Gazel_Exception('Plugin already registered');
		}

		$stackIndex = (int) $stackIndex;

		if ($stackIndex) {
			if (isset($this->_plugins[$stackIndex])) {
				require_once 'Gazel/Exception.php';
				throw new Gazel_Exception('Plugin with stackIndex "' . $stackIndex . '" already registered');
			}
			$this->_plugins[$stackIndex] = array('class' => $plugin, 'data' => $data);
		} else {
			$stackIndex = count($this->_plugins);
			while (isset($this->_plugins[$stackIndex])) {
				++$stackIndex;
			}
			$this->_plugins[$stackIndex] = array('class' => $plugin, 'data' => $data);
		}

		ksort($this->_plugins);

		return $this;
	}

	public function registerPluginFile($pluginName,$stackIndex=null)
	{
		$config=Gazel_Config::getInstance();

		require_once $config->gazeldir.'/plugins/'.$pluginName.'.php';
		$classname='Gazel_Plugin_'.$pluginName;
		$plugin=new $classname();
		$plugin->setName($pluginName);

		$this->registerPlugin($plugin);
	}

	/**
	 * Retrieve all plugins
	 *
	 * @return array
	 */
	public function getPlugins()
	{
		return $this->_plugins;
	}

	/**
	 * Set request object, and register with each plugin
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Gazel_Controller_Plugin_Broker
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->_request = $request;

		foreach ($this->_plugins as $plugin) {
			$plugin['class']->setRequest($request);
		}

		return $this;
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

	public function getDb()
	{
		require_once "Gazel/Db.php";
		$dbinstance=Gazel_Db::getInstance();
		$db=$dbinstance->getDb();

		return $db;
	}

	/**
	 * Set response object
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 * @return Gazel_Controller_Plugin_Broker
	 */
	public function setResponse(Zend_Controller_Response_Abstract $response)
	{
		$this->_response = $response;

		foreach ($this->_plugins as $plugin) {
			$plugin['class']->setResponse($response);
		}

		return $this;
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

	public function onLoadConfig(Gazel_Config $config)
	{
		foreach ($this->_plugins as $plugin) {
			$plugin['class']->onLoadConfig($config);
		}
	}

    public function onAdminThemeMenu()
    {
        $a=array();
        foreach ($this->_plugins as $plugin) {
            if ( $m=$plugin['class']->onAdminThemeMenu($m) ){
                $a[]=$m;
            }
        }

        return $a;
    }

    public function onPrependAdminMenu($menu)
    {
        foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onPrependAdminMenu')){
                $menu = $plugin['class']->onPrependAdminMenu($menu, $plugin['data']);
            }
        }

        return $menu;
    }

    public function onAppendAdminMenu($menu)
    {
        foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onAppendAdminMenu')){
                $menu = $plugin['class']->onAppendAdminMenu($menu, $plugin['data']);
            }
        }

        return $menu;
    }

    public function onFrontendRenderBody($body)
    {
        foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onFrontendRenderBody')){
                $body = $plugin['class']->onFrontendRenderBody($body, $plugin['data']);
            }
        }

        return $body;
    }

    public function onApplicationError($code, Exception $exception)
    {
        foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onApplicationError')){
                $plugin['class']->onApplicationError($code, $exception);
            }
        }
    }

    public function onRouteShutdown(Zend_Controller_Request_Abstract $request)
    {
    	foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onRouteShutdown')){
                $plugin['class']->onRouteShutdown($request);
            }
        }
    }

    /**
     * we keep this for backward compatibility only
     */
    public function onFrontendRender($body)
    {
        foreach ($this->_plugins as $plugin) {
            if(method_exists($plugin['class'],'onFrontendRender')){
                $body = $plugin['class']->onFrontendRender($body, $plugin['data']);
            }
        }

        return $body;
    }

	public function onFrontendRenderHead($head)
	{
        foreach ($this->_plugins as $plugin) {
			if(method_exists($plugin['class'],'onFrontendRenderHead')){
				$head = $plugin['class']->onFrontendRenderHead($head, $plugin['data']);
			}
		}

		return $head;
	}

	public function onFrontendFooter($body)
	{
		$a=array();
		$text = '';
	  	foreach ($this->_plugins as $plugin) {
			if(method_exists($plugin['class'],'onFrontendFooter')){			
				$text .= $plugin['class']->onFrontendFooter($plugin['data']);
			}
		}
		$res = str_replace('</body>', $text . '</body>', $body);
		return $res;
	}
	
	public function onAdminView($name='', $data=array(), $path='')
	{
		$pluginName = $name;
		
		$plugindir = $path.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.lcfirst($pluginName);
		
		require_once "Zend/Filter/Word/DashToCamelCase.php";
		$filter=new Zend_Filter_Word_DashToCamelCase();
		$pluginName=$filter->filter($pluginName);
			
		$pluginFile = $plugindir.DIRECTORY_SEPARATOR.$pluginName.'.php';
		
		if ( is_file($pluginFile) )
		{
			require_once $pluginFile;
				
			$classname=$pluginName.'Plugin';
			$plugin=new $classname();
			return $plugin->onAdminView($data);
		}
	}
	
	public function onAdminSave($name='',$data=array())
	{
		$pluginName = lcfirst(mysql_escape_string($name));
		
		require_once "Gazel/Db.php";
		$dbinstance=Gazel_Db::getInstance();
		$db=$dbinstance->getDb();
		
		$res=$db->fetchRow("SELECT * FROM plugin WHERE plugin_name='$pluginName'");
		$options = unserialize($res['plugin_options']);
		//print_r($res);exit;
		foreach($data as $k => $v){
			$options[$k] = $v;
		}
		
		//$db->update('plugin',array('plugin_options' => serialize($options)), array('plugin_id='.$data['plugin_id']));
		//echo "UPDATE plugin SET plugin_options='".serialize($options)."' WHERE plugin_id=".$res['plugin_id'];exit;
		$db->query("UPDATE plugin SET plugin_options='".serialize($options)."' WHERE plugin_id=".$res['plugin_id']);
		//exit;	
	}

	/**
	 * Render panel
	 *
	 * @params Gazel_Form $form Form to render
	 * @return Gazel_Form
	 */
	public function adminRenderPanel($pluginName, $form)
	{
		//var_dump($this->_plugins);exit;
		foreach ($this->_plugins as $plugin) {
			if( $plugin['class']->getName() == $pluginName )
			{
				if(method_exists($plugin['class'],'onAdminRenderPanel')){
					$form = $plugin['class']->onAdminRenderPanel($form);
				}
			}
		}

		return $form;
	}

	/**
	 * Create submenu on admin for all plugin that implements
	 * onAdminRenderPanel()
	 *
	 * @return array
	 */
	public function getAdminSubMenu()
	{
		$plugins = array();
		foreach ($this->_plugins as $plugin) 
		{
			require_once "Gazel/Form.php";
			$form = new Gazel_Form(); // construct blank form

			if(method_exists($plugin['class'],'onAdminRenderPanel'))
			{
				$form = $plugin['class']->onAdminRenderPanel($form);

				if( $form instanceof Gazel_Form ) // make sure it returns Gazel_Form
				{
					if( count($form->getElements())>0 ) // make sure it has elements
					{
						$plugins[] = array(
							'name' 	=> $plugin['class']->getName(),
							'title' => $plugin['class']->getTitle()
						);
					}
				}
			}
		}

		return $plugins;
	}

	/**
	 * Save plugin options from admin panel
	 *
	 * @see onAdminRenderPanel()
	 * @param string $pluginName
	 * @param array $data data submitted through form
	 */
	public function savePluginOptions($pluginName, $data)
	{
		$this->_db->update($this->__plugin, array(
			'plugin_options'	=> serialize($data)
		), array('plugin_name=?' => $pluginName));
	}

	/**
	 * Get plugin options from database
	 *
	 * @param string $pluginName plugin name
	 * @return array
	 */
	public function getPluginOptions($pluginName)
	{
		foreach ($this->_plugins as $plugin)
		{
			if( $plugin['class']->getName() == $pluginName )
			{
				return $plugin['class']->getPluginOptions();
			}
		}

		return array(); // should never happen
	}

	public function getAdminSave($name='')
	{
		$pluginName = lcfirst(mysql_escape_string($name));
		
		require_once "Gazel/Db.php";
		$dbinstance=Gazel_Db::getInstance();
		$db=$dbinstance->getDb();
		
		$res=$db->fetchRow("SELECT * FROM plugin WHERE plugin_name='$pluginName'");
		$options = unserialize($res['plugin_options']);
		
		return $options;
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
			return $this->getDb();
		}
		else
		{
			return $this->$name;
		}
	}
}
