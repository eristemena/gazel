<?php

require_once "Zend/Controller/Action.php";
//require_once "Zend/Config/Xml.php";

class LocalController extends Zend_Controller_Action
{
	/*
		    Decide which adapter you want to use; ->Zend_Translate_Adapter_XmlTm

			Create your view and integrate Zend_Translate in your code;

			Create the source file from your code;

			Translate your source file to the desired language.

	*/
	
	public function init()
	{
		try {
			$translate = new Zend_Translate(
				array(
					'adapter' => 'Zend_Translate_Adapter_XmlTm',
					'content' => '/path/to/translate.xml',
					'locale'  => 'en',
					'myoption' => 'myvalue'
				)
			);
		} catch (Exception $e) {
			// File not found, no adapter class...
			// General failure
		}
	}
	
	public function indexAction()
	{
		i
	}
	
	
}