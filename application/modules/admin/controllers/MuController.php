<?php
require_once "Gazel/Controller/Action/Admin.php";

class Admin_MuController extends Gazel_Controller_Action_Admin
{
	/**
	 * Go back to master
	 **/
	public function tomasterAction()
	{
		$this->loadModel('auth','admin')->loginAsMaster();
		//unset($this->_authAdmin->auth->asUser);
		$this->_helper->redirector->gotoSimpleAndExit('index','admin','admin');
	}
	
}