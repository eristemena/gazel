<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Zend/Date.php";

class Gazel_View_Helper_Date extends Zend_View_Helper_Abstract
{
	public function date()
	{
		return $this;
	}
	
	protected function _format($date,$mysqlformat,$format)
	{
		$d=new Zend_Date($date,$mysqlformat);
		$out=$d->toString($format);
		
		return $out;
	}
	
	public function mysqldate2web($mysqldate,$format='dd MMM yyyy')
	{
		if ( $mysqldate!='0000-00-00' && $mysqldate!='' )
		{
			$out = $this->_format($mysqldate,'yyyy-MM-dd',$format);
		}
		else
		{
			$out='';
		}
		
		return $out;
	}
	
	public function mysqldatetime2web($mysqldatetime,$format='dd MMM yyyy HH:mm:ss')
	{
		if ( $mysqldatetime!='0000-00-00 00:00:00' && $mysqldatetime!='' )
		{
			$out = $this->_format($mysqldatetime,'yyyy-MM-dd HH:mm:ss',$format);
		}
		else
		{
			$out='';
		}
		
		return $out;
	}
	
}