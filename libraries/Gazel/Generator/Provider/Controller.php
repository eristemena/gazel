<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Controller extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_controllerPath;

	protected $_moduleName;
	protected $_controllerName;

	protected $_moduleClassName;
	protected $_controllerClassName;

	public function __construct()
	{
		$this->_addHelpMessage('add controller controller-name module-name', 'Add a controller for module');
		$this->_addHelpMessage('add-admin controller controller-name module-name', 'Add an admin controller for module');
	}

	/**
	 * Delete a module
	 * usage: SCRIPT_NAME add controller {controllerName} {moduleName}
	 */
	public function addAction()
	{
		if( count($_SERVER['argv'])!=5 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' add controller {controllerName} {moduleName}');
		}

		$this->_init();

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		if( file_exists($this->_controllerPath) )
		{
			$this->throwError('Controller "'.$this->_controllerName.'" already exists');
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createControllerFile($this->_modulePath, $this->_moduleName, $this->_controllerName);
	}

	/**
	 * Delete a module
	 * usage: SCRIPT_NAME add-admin controller {controllerName} {moduleName}
	 */
	public function addAdminAction()
	{
		if( count($_SERVER['argv'])!=5 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' add controller {controllerName} {moduleName}');
		}

		$this->_init();

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		if( file_exists($this->_controllerPath) )
		{
			$this->throwError('Controller "'.$this->_controllerName.'" already exists');
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createAdminControllerFile($this->_modulePath, $this->_moduleName, $this->_controllerName);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$controllerName = $args[3];
		if( preg_match('/[^a-z\-]/', $controllerName) ){
			$this->throwError('Controller name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_controllerName = $controllerName;

		$moduleName = $args[4];
		if( preg_match('/[^a-z\-]/', $moduleName) ){
			$this->throwError('Module name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_moduleName = $moduleName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_moduleClassName = $filter->filter($this->_moduleName);
		$this->_controllerClassName = $filter->filter($this->_controllerName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_moduleName;
		$this->_modulePath = $path;

		$path = $path.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$filter->filter($controllerName).'Controller.php';
		$this->_controllerPath = $path;
	}
}