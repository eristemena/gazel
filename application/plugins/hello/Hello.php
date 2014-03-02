<?php

require_once "Gazel/Plugin/Abstract.php";

class HelloPlugin extends Gazel_Plugin_Abstract
{
	public function onFrontendRenderBody($text)
	{
		return preg_replace('/hello/i','hello world',$text);
	}
}
