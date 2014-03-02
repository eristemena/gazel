<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Form extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_formPath;

	protected $_moduleName;
	protected $_formName;

	protected $_moduleClassName;
	protected $_formClassName;

	public function __construct()
	{
		$this->_addHelpMessage('add form form-name module-name', 'Create a new form on a module');
	}

	/**
	 * Delete a module
	 * usage: SCRIPT_NAME add form {formName} {moduleName}
	 */
	public function addAction()
	{
		if( count($_SERVER['argv'])!=5 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' add form {formName} {moduleName}');
		}

		$this->_init();

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		if( file_exists($this->_formPath) )
		{
			$this->throwError('Form "'.$this->_formName.'" already exists');
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createFormFile($this->_modulePath, $this->_moduleName, $this->_formName);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$formName = $args[3];
		if( preg_match('/[^a-z\-]/', $formName) ){
			$this->throwError('Form name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_formName = $formName;

		$moduleName = $args[4];
		if( preg_match('/[^a-z\-]/', $moduleName) ){
			$this->throwError('Module name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_moduleName = $moduleName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_moduleClassName = $filter->filter($this->_moduleName);
		$this->_formClassName = $filter->filter($this->_formName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_moduleName;
		$this->_modulePath = $path;

		$path = $path.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.$filter->filter($formName).'Form.php';
		$this->_formPath = $path;
	}
}