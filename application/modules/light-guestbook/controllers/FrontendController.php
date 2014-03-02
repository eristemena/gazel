<?php

require_once "Gazel/Controller/Action.php";

class LightGuestbook_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__guestbook)->where('guestbook_approve=?','y');
		
		$paginator=$this->getPaginator($dbselect,null,40);
		$this->view->paginator=$paginator;
	}
}