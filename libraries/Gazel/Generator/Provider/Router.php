<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Router extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_routerPath;

	protected $_moduleName;
	protected $_routerName;

	protected $_moduleClassName;
	protected $_routerClassName;

	public function __construct()
	{
		$this->_addHelpMessage('create router router-name', 'Create a new router');
	}

	/**
	 * create a router
	 * usage: SCRIPT_NAME create router {routerName} {moduleName}
	 */
	public function createAction()
	{
		if( count($_SERVER['argv'])!=4 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' create router {routerName}');
		}

		$this->_init();

		if( file_exists($this->_routerPath) )
		{
			$this->throwError('Router "'.$this->_routerName.'" already exists');
		}

		if(!mkdir($this->_routerPath, 0755, true)){
			$this->throwError('Oops, can not create directory: '.$this->_routerPath);
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createRouterFile($this->_routerPath, $this->_routerName);

		$this->_createManifestFile();
	}

	protected function _createManifestFile()
	{
		$out = '<?xml version="1.0" encoding="utf-8"?>
<install type="router">
	<name>'.htmlentities($this->_routerName).'</name>
	<description>'.htmlentities($this->_routerName).'</description>
	<author>Your Name</author>
	<creationDate>May 2012</creationDate>
	<copyright>Copyright (C) 2009 Open Source Matters. All rights reserved.</copyright>
	<license>http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL</license>
	<authorEmail>you@example.com</authorEmail>
	<authorUrl>www.example.com.com</authorUrl>
	<version>0.0.1</version>
</install>';

		$xmlPath = $this->_routerPath.DIRECTORY_SEPARATOR.$this->_routerName.'.xml';
		file_put_contents($xmlPath, $out);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$routerName = $args[3];
		if( preg_match('/[^a-z\-]/', $routerName) ){
			$this->throwError('Router name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_routerName = $routerName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_routerClassName = $filter->filter($this->_routerName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routerName;
		$this->_routerPath = $path;
	}
}