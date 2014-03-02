<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_UserController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		//for translate
		//$translate= Zend_Registry::get('translate');	
		//
		$this->view->moduletitle="User Manager";
		$this->view->submenu['group']=array(
			'title'	=> $this->_translate->_('Group'),
			'url'		=> $this->_helper->Url->url(
				array('action'=>'index','controller'=>'group','module'=>'admin')
			)
		);
		
		$this->checkAdminAccess('admin.system.user');
	}
	
	public function indexAction()
	{
		$dbselect=$this->loadModel($controller)->getAll(true);
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
	}
	
	public function formAction()
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');
		
		// get form
		$form=$this->getAdminForm($this->getForm());
		
		if ( $_POST && $form->isValid($_POST) )
		{
			if ( $action=='edit' )
			{
				$values=$form->getValues();
				if ( empty($values['admin_password']) ) {
					unset($values['admin_password']);
				}
				
				$this->loadModel($controller)->$action($values,$_POST['id']);
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			elseif ( $action=='add' )
			{
				$this->loadModel($controller)->$action($form->getValues());
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$this->view->form=$form;
	}
	
	public function getForm()
	{
		//for translate
		//$translate= Zend_Registry::get('translate');	
		//
	
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->loadModel('user')->get($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		
		// username
		$el=$form->createElement('text','admin_username');
		$el->setLabel($this->_translate->_('Username'))
			->setRequired(true)
			->setAttribs(array('size'=>45,'maxlength'=>15))
			->setValue($res['admin_username'])
			->addValidator('StringLength',false,array('min'=>5,'max'=>15))
			->addValidator('Alnum')
		;
		$form->addElement($el);
		
		// email
		$email=$form->createElement('text','admin_email');
		$email->setLabel($this->_translate->_('Email'))
			->setRequired(true)
			->setAttribs(array('size'=>45))
			->setValue($res['admin_email'])
			->addValidator('EmailAddress')
		;
		$form->addElement($email);
		
		if ( $this->_getParam('action')=='add' )
		{
			// password
			$password = $form->createElement('password','admin_password');
			$password->setLabel($this->_translate->_('Password'))
				->addValidator('stringLength', false, array(6, 20))
				->setRequired(true)
			;
			$form->addElement($password);
		}
		
		// name
		$name=$form->createElement('text','admin_name');
		$name->setLabel($this->_translate->_('Name'))
			->setAttribs(array('size'=>35))
			->setValue($res['admin_name'])
		;
		$form->addElement($name);
		
		// group
		$group=$form->createElement('select','admingroup_id');
		$group->setLabel($this->_translate->_('Group'))
			->setMultiOptions($this->loadModel('group')->getOptions());
		;
		$form->addElement($group);
		
		if ( $this->_getParam('action')=='edit' ) 
		{
			// new password
			$npassword = $form->createElement('password','admin_password');
			$npassword->setLabel($this->_translate->_('New Password'))
			->addValidator('stringLength', false, array(6, 20))
			->addValidator('passwordconfirm',false, array('rpassword'))
			;
			$form->addElement($npassword);
			
			// retype password
			$rpassword = $form->createElement('password','rpassword');
			$rpassword->setLabel($this->_translate->_('Re-type Password'))
				->addValidator('stringLength', false, array(6, 20))
				->setIgnore(true)
			;
			$form->addElement($rpassword);
		}
		
		return $form;
	}
}