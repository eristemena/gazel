<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Config.php";

class Gazel_View_Helper_PageUrl extends Zend_View_Helper_Abstract
{
	public function pageUrl($module, $action, array $params = array(), $reset = false)
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		$request=$front->getRequest();
		
		require_once "Gazel/Db.php";
		$dbi=Gazel_Db::getInstance();
		$db=$dbi->getDb();
		$res=$db->fetchRow($db->select()->from('page')->where('page_module=?',$module));
		
    $params['alias'] 	= $res['page_alias'];
    $params['act'] 		= $action;
    
    return $front->getRouter()->assemble($params, 'page', $reset);
	}
	
}