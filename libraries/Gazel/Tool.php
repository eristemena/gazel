<?php

require_once "Zend/Log.php";
require_once "Zend/Log/Writer/Firebug.php";
require_once "Zend/Controller/Request/Http.php";
require_once "Zend/Controller/Response/Http.php";

class Gazel_Tool
{
	public static function logFb($msg)
	{
		require_once "Gazel/Config.php";
		$config=Gazel_Config::getInstance();
		
		if ( $config->debug )
		{
			$writer = new Zend_Log_Writer_Firebug();
			$logger = new Zend_Log($writer);
			
			$request = new Zend_Controller_Request_Http();
			$response = new Zend_Controller_Response_Http();
			$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
			$channel->setRequest($request);
			$channel->setResponse($response);
			
			// Start output buffering
			ob_start();
			
			$logger->log($msg, Zend_Log::INFO);
			
			// Flush log data to browser
			$channel->flush();
			$response->sendHeaders();
		}
	}
	
}