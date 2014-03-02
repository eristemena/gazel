<?php

class Gazel_Controller_Action_Helper_ReadExtensionXml extends Zend_Controller_Action_Helper_Abstract
{
	public function readExtensionXml($xmlfile)
	{
		$xml = simplexml_load_file($xmlfile);
		
		$extinfo = array();
		$extinfo['type'] = (string) $xml->attributes();
		$extinfo['name'] = (string) $xml->name;
		$extinfo['description'] = (string) $xml->description;
		$extinfo['version'] = (string) $xml->version;
		$extinfo['author'] = (string) $xml->author;
		$extinfo['authorEmail'] = (string) $xml->authorEmail;
		$extinfo['authorUrl'] = (string) $xml->authorUrl;
		$extinfo['required']['gazel']['version'] = (string) $xml->required->gazel->version;
		
		return $extinfo;
	}
	
	public function direct($xmlfile)
	{
		return $this->readExtensionXml($xmlfile);
	}
}