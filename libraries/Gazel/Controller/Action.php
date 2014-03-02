<?php

require_once "Zend/Controller/Action.php";
require_once "Zend/Controller/Action/HelperBroker.php";
require_once "Zend/Paginator.php";
require_once "Zend/Paginator/Adapter/DbSelect.php";
require_once "Zend/View/Helper/PaginationControl.php";
require_once "Zend/Config/Ini.php";
require_once "Zend/Session/Namespace.php";
require_once 'Zend/Translate.php';

require_once "Gazel/Db.php";
require_once "Gazel/Config.php";
require_once "Gazel/Form.php";
require_once "Gazel/Tool.php";

class Gazel_Controller_Action extends Zend_Controller_Action
{
	protected $_config;
	protected $_db;
	protected $_pageType='';

	public $_translate;
	protected $_t; // shortcut to translate
	
	public function init()
	{
		$this->view->addHelperPath('Gazel/View/Helper', 'Gazel_View_Helper');
		Zend_Controller_Action_HelperBroker::addPrefix('Gazel_Controller_Action_Helper');
		
		// db instance
		$this->_config = Gazel_Config::getInstance();
		$this->_db = $this->getDb();
		
		// set view and layout suffix
		$this->_helper->viewRenderer->setViewSuffix('html');
		$this->_helper->layout->setViewSuffix('html');
		
		$this->view->addScriptPath($this->_config->themepath);
		
		$lg=$this->_config->uselanguage;
		$ps = DIRECTORY_SEPARATOR;
		$content = $this->_config->applicationdir.$ps.'modules'.$ps.$this->_request->getModuleName().$ps.'languages'.$ps.$lg.'.csv'; 
		
		if ( !file_exists($content) )
		{
			$content = $this->_config->applicationdir.$ps.'modules'.$ps.'admin'.$ps.'languages'.$ps.$lg.'.csv'; 
		}
		
		try {
			$translate = new Zend_Translate(
				array(
					'adapter' => 'csv',
					'content' => $content,
					'locale'  => $lg,
					'delimiter' => ','
				)
			);
		} catch (Exception $e) {
			
		}
		Zend_Registry::set('translate', $translate);
		
		$this->_translate = $translate;
		$this->_t = $translate; // shortcut
		$this->view->translate=$translate;
		
		$this->initGazel();
	}
	
	public function initGazel()
	{}
	
	public function preDispatch()
	{
		
	}
	
	public function postDispatch()
	{
		$alias = $this->_getParam('alias');
		$moduleName = $this->_request->getModuleName();
		$controllerName = $this->_request->getControllerName();
		$actionName = $this->_request->getActionName();
		$suffix = $this->_helper->viewRenderer->getViewSuffix();
		$pageType = $this->_getParam('page_type');
		
		if ( $pageType=='module' )
		{
			/**
			 * If we have content_:module_:controller_:action.:suffix in themepath then override the default view script in ViewRenderer!
			 **/
			if ( is_readable($this->_config->themepath.'/content_'.$moduleName.'_'.$controllerName.'_'.$actionName.'.'.$suffix) )
			{
				if ($this->_request->isDispatched())
				{
					$this->_helper->viewRenderer->setNoRender();
					$this->renderScript('content_'.$moduleName.'_'.$controllerName.'_'.$actionName.'.'.$suffix);
				}
			}
			
			/**
			 * Override layout with :module.:suffix in themepath
			 **/
			if ( $this->_helper->layout->getLayout()=='layout' ) // if setLayout() never been called
			{
				if ( is_readable($this->_config->themepath.'/'.$moduleName.'.'.$suffix) )
				{
					$this->_helper->layout->setLayout($moduleName);
				}
				else
				{
					$this->_helper->layout->setLayout('master');
				}
			}
		}
		else
		{
			/**
			 * If we have content_:alias.:suffix in themepath then override the default view script in ViewRenderer!
			 **/
			if ( is_readable($this->_config->themepath.'/content_'.$alias.'.'.$suffix) )
			{
				if ($this->_request->isDispatched())
				{
					$this->_helper->viewRenderer->setNoRender();
					$this->renderScript('content_'.$alias.'.'.$suffix);
				}
			}
			
			/**
			 * Override layout with :alias.:suffix in themepath
			 **/
			if ( $this->_helper->layout->isEnabled() && $this->_helper->layout->getLayout()=='layout' ) // if setLayout() never been called
			{
				if ( is_readable($this->_config->themepath.'/'.$alias.'.'.$suffix) )
				{
					$this->_helper->layout->setLayout($alias);
				}
				else
				{
					$this->_helper->layout->setLayout('master');
				}
			}
		}
	}

	/**
	 * Override this to get the file path
	 */
	protected function _getDimageFilePath($id)
	{
		return false;
	}

	/**
	 * Override this to get the file MIME
	 */
	protected function _getDimageFileMime($id)
	{
		return 'image/jpeg';
	}

	public function dimageAction()
	{
		require_once "Gazel/Filter/ImageSize.php";
		
		$id = $this->_getParam('id');
		$productpictdir=$this->_config->userdatadir;
		$thumbdir = $this->_config->cachedir;
		
		$mime = $this->_getDimageFileMime($id);
		$fpath = $this->_getDimageFilePath($id);

		if(!$fpath){
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception('Dimage Error: File path is empty');
		}

		$filter=new Gazel_Filter_ImageSize();
		$filter->setHeight($this->_getParam('height'));
		$filter->setWidth($this->_getParam('width'));
		$filter->setOverwriteMode('cache_older');
		$filter->setThumbnailDirectory($thumbdir);
		$out=$filter->filter($fpath);
		
		header('Content-Type: '.$mime);
		$fh = fopen($out, 'r');
		fpassthru($fh);
		fclose($fh);
		exit;
	}
	
	public function getConfig()
	{
		return Gazel_Config::getInstance();
	}
	
	public function getDb()
	{
		$db=Gazel_Db::getInstance();
		return $db->getDb();
	}
	
	public function redirect($urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		$url=$this->_helper->Url->url($urlOptions, $name, $reset, $encode);
		header("Location: $url");
		exit;
	}

	public function redirect404()
	{
		$this->_forward('404');
	}
	
	public function loadModel($model=null,$module=null)
	{
		if ( $module==null ) {
			$module = $this->_getParam('module');
		}
		if ( $model==null ) {
			$model = $this->_getParam('controller');
		}
		$modeldir=$this->getFrontController()->getModuleDirectory($module).DIRECTORY_SEPARATOR.'models';
		
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
	
	public function loadForm($form=null,$module=null)
	{
		if ( $module==null ) {
			$module = $this->_getParam('module');
		}
		if ( $model==null ) {
			$model = $this->_getParam('controller');
		}
		$modeldir=$this->getFrontController()->getModuleDirectory($module).DIRECTORY_SEPARATOR.'forms';
		
		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$fform=$modeldir.DIRECTORY_SEPARATOR.$filter->filter($form).'Form.php';
		if ( !file_exists($fform) )
		{
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception('Can not find the form: '.$fform);
		}
		else
		{
			require_once $fform;
			require_once 'Zend/Filter/Word/DashToCamelCase.php';
			$filter=new Zend_Filter_Word_DashToCamelCase();
			
			$cform=$filter->filter($module).'_Form_'.$filter->filter($form);
			$obj = new $cform();
			
			return $obj->getForm();
		}
	}
	
	public function getPaginator($dbselect,$type=null,$num=0)
	{
		$paginatorType=($type) ? $type : 'Sliding';
		$countPerPage=($num) ? $num : 10;
		
		$adapter = new Zend_Paginator_Adapter_DbSelect($dbselect);
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle($paginatorType);
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage($countPerPage);
		
		return $paginator;
	}
	
	public function render($action = null, $name = null, $noController = false)
	{
		if (!$this->getInvokeArg('noViewRenderer') && $this->_helper->hasHelper('viewRenderer')) {
			return $this->_helper->viewRenderer->render($action, $name, $noController);
		}
		
		$view   = $this->initView();
		$script = $this->getViewScript($action, $noController);
		
		$this->getResponse()->appendBody(
			$view->render($script),
			$name
		);
	}
	
	public function getImageType($fname)
	{
		return 'jpg';
		/*
		$fileinfo=getimagesize($fname);
    switch($fileinfo[2]) {
      case IMAGETYPE_GIF:
          $outputType = 'gif';
          break;
      case IMAGETYPE_PNG:
          $outputType = 'png';
          break;
      default:
      case IMAGETYPE_JPEG:
          $outputType = 'jpg';
          break;
    }
    
    return $outputType;
    */
	}
	
	public function getMime($fname)
	{
		$type=$this->getImageType($fname);
		
		return "image/$type";
	}
	
	public function vardump($var)
	{
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
	
	public function __get($name)
	{
		if ( substr($name,0,3)=='___' )
		{
			return $this->loadModel(substr($name,3));
		}
		elseif ( substr($name,0,2)=='__' )
		{
			return $this->_config->getTableName(substr($name,2));
		}
		else
		{
			return $this->$name;
		}
	}
}