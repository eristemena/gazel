<?php

require_once "Zend/Filter/Interface.php";

class Gazel_Filter_Date2Mysql implements Zend_Filter_Interface
{
	public function toDateMysql($value)
	{
		$d=explode('/',$value);
		
		return $d[2].'-'.$d[1].'-'.$d[0];
	}
	
	public function filter($value)
	{
		if ( preg_match('|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}|',$value) )
		{
			$d=explode('/',$value);
			$out=$d[2].'-'.$d[1].'-'.$d[0];
		}
		elseif ( preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$value) )
		{
			$d=explode('-',$value);
			$out=$d[2].'/'.$d[1].'/'.$d[0];
		}
		else
		{
			$out='';
		}
		
		return $out;
	}
}