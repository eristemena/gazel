<?php

require_once "Gazel/Controller/Action.php";

class LightNews_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__news)->order('news_id desc');
		$paginator = $this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
		
		$this->render('shortdesc');
	}
	
	public function sidebarAction()
	{
		$dbselect=$this->_db
			->select()
			->from($this->__news)
			->order('news_id desc')
		;
		
		$paginator = $this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
	}
	
	public function detailAction()
	{
		$id=$this->_getParam('id');
		
		$dbselect=$this->_db->select()->from($this->__news)->where('news_id=?',$id);
		$res=$this->_db->fetchRow($dbselect);
		
		$this->view->news=$res;
	}
}