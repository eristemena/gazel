<?php

require_once "Gazel/Controller/Action.php";

class Admin_LoginController extends Gazel_Controller_Action
{
	public function initGazel()
	{
		$this->_authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
	}
	
	public function indexAction()
	{
		// to avoid conflict with script in theme directory
		$this->_helper->layout->getView()->setScriptPath($this->_config->applicationdir.'/modules/admin/views/scripts');
		$this->_helper->layout->getView()->addScriptPath($this->_config->publicdir.'/themes/'.$this->_config->themename.'_backend');
		
		$form=$this->getForm();
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$val=$form->getValues();
			$email=$val['email'];
			$password=$val['password'];
			
			if ( $this->loadModel('auth','admin')->setAuth($email,$password) )
			{
				$this->redirect(array('controller'=>'index'));
			}
			else
			{
				$this->view->err='Login failed, check your username/email and password';
				$this->view->email=$_POST['email'];
			}
			
		
			/*$dbselect=$this->_db->select()->from($this->__admin)->where('admin_email=? or admin_username=?',$email,$email)->where('admin_password=?',$password);
			if ( $res=$this->_db->fetchRow($dbselect) ) 
			{
				$this->_authAdmin->auth->id=$res['admin_id'];
				$this->_authAdmin->auth->email=$res['admin_email'];
				$this->_authAdmin->auth->name=$res['admin_name'];
				$this->_authAdmin->auth->username=$res['admin_username'];
				$this->_authAdmin->auth->password=$res['admin_password'];
				$this->_authAdmin->auth->admingroupid=$res['admingroup_id'];
				
				$this->redirect(array('controller'=>'index'));
			} else {
				$this->view->err='Login failed, check your username/email and password';
				$this->view->email=$_POST['email'];
			}*/
		}
		
		
	
		$this->view->form=$form;
		
		$this->render('login');
		
		$this->_helper->layout->setLayout('login');
	}
	
	public function getForm()
	{	
		//for translate
		//$translate= Zend_Registry::get('translate');	
		//
		$form = new Zend_Form();
		$form->setAction($this->_helper->Url->url(array('controller'=>'login')))
			->setMethod('post');
		
		$email=$form->createElement('text','email');
		$email->setLabel($this->_translate->_("login"))
			->setAttribs(array('class'=>'txt'))
			->setRequired(true);
		
		$password=$form->createElement('password','password');
		$password->setLabel('Password')
			->setAttribs(array('class'=>'txt'))
			->setRequired(true);
		
		$login=$form->createElement('submit','login');
		$login->setValue('Login')
			->setAttribs(array('class'=>'sbmt'))
		;
		
		$form->addElement($email)
			->addElement($password)
			->addElement($login)
			->setAttrib('name','login_form')
		;
		
		
		
		return $form;
	}
	
	public function setAuth($email,$password)
	{
		$dbselect=$this->_db->select()->from($this->__admin)->where('admin_email=? or admin_username=?',$email,$email)->where('admin_password=?',$password);
		
		$res=$this->_db->fetchRow($dbselect);
		$this->_authAdmin->auth->email=$email;
		$this->_authAdmin->auth->password=$password;
		$this->_authAdmin->auth->admingroupid=$res['admingroup_id'];
	}
	
}