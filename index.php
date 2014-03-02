<?php

/**
 * Bootstrap file
 */

/**
 * Directory setting (change here)
 **/

// public directory (usually your webroot where this bootstrap file accessible from the world)
$publicdir=realpath(dirname(__FILE__));

// application directory is the place where we put all logic such as modules, routers, plugins and themes for user
$applicationdir=realpath(dirname(__FILE__).'/application');

// gazel directory is the directory of gazel
$gazeldir=realpath(dirname(__FILE__).'/gazel');

// include paths
$incl=array(
	'.',
	$publicdir,
	$applicationdir.DIRECTORY_SEPARATOR.'widgets',
	$publicdir.DIRECTORY_SEPARATOR.'libraries'
);
ini_set('include_path',implode(PATH_SEPARATOR,$incl));

require_once "Gazel/Mvc.php";

$mvc=Gazel_Mvc::getInstance();
$mvc->config->publicdir=$publicdir;
$mvc->config->applicationdir=$applicationdir;
$mvc->config->namespace='gazel';
$mvc->config->adminpath='admin';
$mvc->config->configfile=$applicationdir.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'setting.xml';

if ( $_SERVER['SERVER_ADDR']=='127.0.0.1' || $_SERVER['SERVER_ADDR']=='::1' ) {
	$mvc->config->debug=true;
} else {
	$mvc->config->debug=false;
}

$mvc->start();
