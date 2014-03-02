<?php

require_once "Gazel/View/Helper/Abstract.php";

class Gazel_View_Helper_Widget extends Gazel_View_Helper_Abstract
{
	protected $_front;
	
	public function widget($pos)
	{
		$dbi=Gazel_Db::getInstance();
		$db=$dbi->getDb();
		
		$res = $db->fetchAll($db->select()->from($this->__widget)->where('widget_pos=?',$pos)->where('widget_active=?','y'));
		$out='';
		foreach ( $res as $r )
		{
			$widget=$r['widget_name'];
			require_once "Zend/Filter/Word/DashToCamelCase.php";
			$filter=new Zend_Filter_Word_DashToCamelCase();
			$fileName=$filter->filter($widget);
			
			require_once $widget.'/'.$fileName.'.php';
			$className=$fileName.'Widget';
			$class=new $className();
			
			$out.='<div class="widget">'.$class->frontEnd($r).'</div>';
		}
		
		return $out;
	}
}