<?php

require_once "Gazel/Controller/Action/Admin.php";

class LightNews_AdminController extends Gazel_Controller_Action_Admin
{
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__news)->order($this->getOrdering())->where($this->getSearch());
		$paginator=$this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
	}
}