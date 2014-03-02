<?php

require_once "Gazel/Controller/Action/Admin.php";

class MuMaster_AdminController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		unset($this->view->submenu['add']);
		$this->view->submenu['template']=array(
			'title'	=> 'Login as Template',
			'url'		=> $this->_helper->Url->url(
				array('action'=>'touser','as'=>'template')
			)
		);
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__users)->order($this->getOrdering())->where($this->getSearch())->where('user_login!=?','template');
		$paginator=$this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
	}
	
	public function touserAction()
	{
		//$this->_authAdmin->auth->asUser = $this->_getParam('as');
		$this->loadModel('auth','admin')->loginAsUser($this->_getParam('as'));
		
		$this->_helper->redirector->gotoSimpleAndExit('index','admin','admin');
	}
	
}