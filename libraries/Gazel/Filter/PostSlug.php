<?php

require_once "Zend/Filter/Interface.php";

class Gazel_Filter_PostSlug implements Zend_Filter_Interface
{
	public function filter($value)
	{
		$pattern=array(
		'.',
		',',
		'?',
		'!',
		'/',
		'\\',
		'<',
		'>',
		'(',
		')',
		'\'',
		'"',
		' '
		);
		
		$replace=array(
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'-'
		);
		
		$out=strtolower(str_replace($pattern,$replace,$value));
		return $out;
	}
}