<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_View_Helper_LogoHeader extends Zend_View_Helper_Abstract
{
	public function logoHeader($options=array('text-wrapper'=>'h1'))
	{
		$config=Gazel_Config::getInstance();
		
		if ( $config->uselogo=='y' && $config->logo )
		{
			if ( strtolower(substr($config->logo,-3))=='swf' )
			{
				return '
<object data="'.$config->baseurl.'/'.$config->logo.'" type="application/x-shockwave-flash" width="'.$config->logo_width.'" height="'.$config->logo_height.'" wmode="transparent">
<param name="movie" value="'.$config->baseurl.'/'.$config->logo.'">
<param name="quality" value="high">
<param name="wmode" value="transparent">
</object>
				';
			}
			else
			{
				return '<img src="'.$config->logo.'" alt="logo" border="0" />';
			}
		}
		else
		{
			if(isset($options['text-wrapper']))
			{
    			return '<'.$options['text-wrapper'].'>'.htmlentities($config->sitename).'</'.$options['text-wrapper'].'>';
    		}
    		else
    		{
    			return htmlentities($config->sitename);
    		}
    	}
	}
	
}