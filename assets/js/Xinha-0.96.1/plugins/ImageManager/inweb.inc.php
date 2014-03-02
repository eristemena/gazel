<?php

//require_once dirname(__FILE__).'/../../../../../config.php';

function getUsername($masterdomain){
	$host=$_SERVER['HTTP_HOST'];
	$ex=explode('.'.$masterdomain,$host);
	return $ex[0];
}

$publicdir=realpath(dirname(__FILE__).'/../../../../..');
$incl=array(
	'.',
	$publicdir.'/libraries'
);
ini_set('include_path',implode(PATH_SEPARATOR,$incl));

require_once "Zend/Config/Xml.php";
$configfile=realpath($publicdir.'/application/configs/setting.xml');
$configi=new Zend_Config_Xml($configfile,'gazel');

require_once "Zend/Controller/Request/Http.php";
$request = new Zend_Controller_Request_Http();

$thispath='/assets/js/Xinha-0.96.1/plugins/ImageManager';
$basepath=$request->getBasePath();
$httphost=$request->getHttpHost();

if ( $configi->mu->active=="true" )
{
	$username = getUsername($configi->mu->domain);
}

if ( $configi->mu->active=="true" )
{
	$gazel_imagedir = $publicdir.'/data/user/'.$username;
}
else
{
	$gazel_imagedir = $publicdir.'/data/user';
}

if ( !file_exists($gazel_imagedir) ) {
	@mkdir($gazel_imagedir);
}

$c=substr($basepath,0,-1*strlen($thispath));

if ( $configi->mu->active=="true" )
{
	$gazel_imageurl=$c.'/data/user/'.$username;
}
else
{
	$gazel_imageurl=$c.'/data/user';
}
