<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Model extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_modelPath;

	protected $_moduleName;
	protected $_modelName;

	protected $_moduleClassName;
	protected $_modelClassName;

	public function __construct()
	{
		$this->_addHelpMessage('add model model-name module-name', 'Create a new form on a module');
	}

	/**
	 * Delete a module
	 * usage: SCRIPT_NAME add model {modelName} {moduleName}
	 */
	public function addAction()
	{
		if( count($_SERVER['argv'])!=5 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' add model {modelName} {moduleName}');
		}

		$this->_init();

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		if( file_exists($this->_modelPath) )
		{
			$this->throwError('Model "'.$this->_modelName.'" already exists');
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createModelFile($this->_modulePath, $this->_moduleName, $this->_modelName);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$modelName = $args[3];
		if( preg_match('/[^a-z\-]/', $modelName) ){
			$this->throwError('Model name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_modelName = $modelName;

		$moduleName = $args[4];
		if( preg_match('/[^a-z\-]/', $moduleName) ){
			$this->throwError('Module name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_moduleName = $moduleName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_moduleClassName = $filter->filter($this->_moduleName);
		$this->_modelClassName = $filter->filter($this->_modelName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_moduleName;
		$this->_modulePath = $path;

		$path = $path.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.$filter->filter($modelName).'Model.php';
		$this->_modelPath = $path;
	}
}