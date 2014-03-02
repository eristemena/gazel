<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Widget extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_widgetPath;

	protected $_moduleName;
	protected $_widgetName;

	protected $_moduleClassName;
	protected $_widgetClassName;

	public function __construct()
	{
		$this->_addHelpMessage('create widget widget-name', 'Create a new widget');
	}

	/**
	 * create a widget
	 * usage: SCRIPT_NAME create widget {widgetName} {moduleName}
	 */
	public function createAction()
	{
		if( count($_SERVER['argv'])!=4 ){
			$this->throwError('Error: Usage: '.$_SERVER['SCRIPT_NAME'].' create widget {widgetName}');
		}

		$this->_init();

		if( file_exists($this->_widgetPath) )
		{
			$this->throwError('Widget "'.$this->_widgetName.'" already exists');
		}

		if(!mkdir($this->_widgetPath, 0755, true)){
			$this->throwError('Oops, can not create directory: '.$this->_widgetPath);
		}

		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createWidgetFile($this->_widgetPath, $this->_widgetName);

		$this->_createManifestFile();
	}

	protected function _createManifestFile()
	{
		$out = '<?xml version="1.0" encoding="utf-8"?>
<install type="widget">
	<name>'.htmlentities($this->_widgetName).'</name>
	<description>'.htmlentities($this->_widgetName).'</description>
	<author>Your Name</author>
	<creationDate>May 2012</creationDate>
	<copyright>Copyright (C) 2009 Open Source Matters. All rights reserved.</copyright>
	<license>http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL</license>
	<authorEmail>you@example.com</authorEmail>
	<authorUrl>www.example.com.com</authorUrl>
	<version>0.0.1</version>
</install>';

		$xmlPath = $this->_widgetPath.DIRECTORY_SEPARATOR.$this->_widgetName.'.xml';
		file_put_contents($xmlPath, $out);
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$widgetName = $args[3];
		if( preg_match('/[^a-z\-]/', $widgetName) ){
			$this->throwError('Widget name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_widgetName = $widgetName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_widgetClassName = $filter->filter($this->_widgetName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$widgetName;
		$this->_widgetPath = $path;
	}
}