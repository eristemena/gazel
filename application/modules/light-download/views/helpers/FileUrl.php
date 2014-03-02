<?php

require_once "Gazel/View/Helper/Abstract.php";


class LightDownload_View_Helper_FileUrl extends Gazel_View_Helper_Abstract
{
	public function fileUrl($id)
	{
		require_once "Zend/Controller/Front.php";
		$front=Zend_Controller_Front::getInstance();
		
		$routerName=$front->getRouter()->getCurrentRouteName();
		$moduleName='light-download';
		
		$res=$this->_db->fetchRow($this->_db->select()->from($this->__page)->where('page_module=?',$moduleName));
		
		$url=$this->routerAssemble(array('alias' => $res['page_alias'],'act' => 'download', 'id' => $id),'page');
		
		return $url;
	}
}