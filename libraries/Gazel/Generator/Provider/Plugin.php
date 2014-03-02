<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Plugin extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_pluginPath;

	protected $_moduleName;
	protected $_pluginName;

	protected $_moduleClassName;
	protected $_pluginClassName;

	public function __construct()
	{
		$this->_addHelpMessage('create plugin plugin-name', 'Create a new plugin');
	}

	/**
	 * create a plugin
	 * usage: SCRIPT_NAME create plugin {pluginName} {moduleName}
	 */
	public function createAction()
	{
		if( count($_SERVER['argv'])!=4 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' create plugin {pluginName}');
		}

		$this->_init();

		if( file_exists($this->_pluginPath) )
		{
			$this->throwError('Plugin "'.$this->_pluginName.'" already exists');
		}

		if(!mkdir($this->_pluginPath, 0755, true)){
			$this->throwError('Oops, can not create directory: '.$this->_pluginPath);
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createPluginFile($this->_pluginPath, $this->_pluginName);

		$this->_createManifestFile();
	}

	protected function _createManifestFile()
	{
		$out = '<?xml version="1.0" encoding="utf-8"?>
<install type="plugin">
	<name>'.htmlentities($this->_pluginName).'</name>
	<description>'.htmlentities($this->_pluginName).'</description>
	<author>Your Name</author>
	<creationDate>May 2012</creationDate>
	<copyright>Copyright (C) 2009 Open Source Matters. All rights reserved.</copyright>
	<license>http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL</license>
	<authorEmail>you@example.com</authorEmail>
	<authorUrl>www.example.com.com</authorUrl>
	<version>0.0.1</version>
</install>';

		$xmlPath = $this->_pluginPath.DIRECTORY_SEPARATOR.$this->_pluginName.'.xml';
		file_put_contents($xmlPath, $out);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$pluginName = $args[3];
		if( preg_match('/[^a-z\-]/', $pluginName) ){
			$this->throwError('Plugin name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_pluginName = $pluginName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_pluginClassName = $filter->filter($this->_pluginName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pluginName;
		$this->_pluginPath = $path;
	}
}