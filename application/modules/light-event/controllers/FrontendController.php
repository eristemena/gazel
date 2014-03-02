
<?php

require_once "Gazel/Controller/Action.php";

class LightEvent_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__event)->order('event_id desc');
		
		$paginator = $this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
		
		$this->render('shortdesc');
	}
	
	public function detailAction()
	{
		$id=$this->_getParam('id');
		
		$dbselect=$this->_db->select()->from($this->__event)->where('event_id=?',$id);
		$res=$this->_db->fetchRow($dbselect);
		
		$this->view->event=$res;
	}
}