<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_View extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_viewPath;

	protected $_moduleName;
	protected $_viewName;

	protected $_moduleClassName;
	protected $_viewClassName;

	public function __construct()
	{
		$this->_addHelpMessage('add-helper view view-helper-name module-name', 'Create a new view helper on a module');
	}

	/**
	 * add view helper to a module
	 * usage: SCRIPT_NAME add-helper view {viewName} {moduleName}
	 */
	public function addHelperAction()
	{
		if( count($_SERVER['argv'])!=5 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' add view {viewName} {moduleName}');
		}

		$this->_init();

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		if( file_exists($this->_viewPath) )
		{
			$this->throwError('View helper "'.$this->_viewName.'" already exists');
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createViewHelperFile($this->_modulePath, $this->_moduleName, $this->_viewName);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$viewName = $args[3];
		if( preg_match('/[^a-z\-]/', $viewName) ){
			$this->throwError('View name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_viewName = $viewName;

		$moduleName = $args[4];
		if( preg_match('/[^a-z\-]/', $moduleName) ){
			$this->throwError('Module name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_moduleName = $moduleName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_moduleClassName = $filter->filter($this->_moduleName);
		$this->_viewClassName = $filter->filter($this->_viewName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_moduleName;
		$this->_modulePath = $path;

		$path = $path.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$filter->filter($viewName).'View.php';
		$this->_viewPath = $path;
	}
}