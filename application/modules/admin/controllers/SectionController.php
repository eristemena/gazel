<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_SectionController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle = 'Section Manager';
		
		$this->checkAdminAccess('admin.content.section');
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db
			->select()
			->from('section')
			->order(array('section_id desc'))
		;
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
	}
}