<?php

require_once "Gazel/Controller/Action.php";

class MuMaster_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		
	}
	
	public function registerAction()
	{
		if ( $_POST['a']=='register' )
		{
			$_POST['username'] = strtolower($_POST['username']);
			
			if ( !$_POST['fullname'] )
			{
				$this->_helper->json(array('stat'=>'failed','msg'=>'Please fill your full name!','focus'=>'fullname'));
			}
			
			require_once "Zend/Validate/Alnum.php";
			$val=new Zend_Validate_Alnum();
			if ( !$val->isValid($_POST['username']) ){
				echo $this->_helper->json(array('stat'=>'failed','msg'=>'You must use alpha numeric only for username','focus'=>'username'));
			}
			
			$forbiddenusername = array('master','template','admin','gazel');
			if ( in_array($_POST['username'],$forbiddenusername) ){
				echo $this->_helper->json(array('stat'=>'failed','msg'=>'Sorry, this username is not allowed','focus'=>'username'));
			}
			
			require_once "Zend/Validate/StringLength.php";
			$val=new Zend_Validate_StringLength(array('min'=>5,'max'=>15));
			if ( !$val->isValid($_POST['username']) ){
				echo $this->_helper->json(array('stat'=>'failed','msg'=> current($val->getMessages()),'focus'=>'username'));
			}
			
			if ( $res=$this->_db->fetchRow('select * from users where user_login=?',$_POST['username']) )
			{
				echo $this->_helper->json(array('stat' => 'failed','msg' => 'Username is already taken','focus'=>'username'));
			}
			
			if ( !$_POST['email'] )
			{
				$this->_helper->json(array('stat'=>'failed','msg'=>'Oh come on, please fill your email!','focus'=>'email'));
			}
			
			require_once "Zend/Validate/EmailAddress.php";
			$val=new Zend_Validate_EmailAddress();
			if ( !$val->isValid($_POST['email']) ){
				$this->_helper->json(array('stat'=>'failed','msg'=>current($val->getMessages()),'focus'=>'email'));
			}
			
			if ( $res=$this->_db->fetchRow('select * from users where user_email=?',$_POST['email']) )
			{
				$this->_helper->json(array('stat'=>'failed','msg'=>'Email is already used','focus'=>'email'));
			}
			
			if ( !$_POST['password'] )
			{
				$this->_helper->json(array('stat'=>'failed','msg'=>'Please fill your password!','focus'=>'password'));
			}
			
			/** all validation is passed **/
			
			// put to users
			$this->_db->insert('users',array(
				'user_login'		=> $_POST['username'],
				'user_email'		=> $_POST['email'],
				'user_fullname'	=> $_POST['fullname']
			));
			
			// install db
			require_once dirname(__FILE__).'/../../core/models/Install.php';
			$model = new Model_Core_Install();
			if ( $model->installDb($_POST['username'].'_') === false )
			{
				echo json_encode(array('stat'=>'failed','msg'=>'Failed to import the database, please make sure the database is empty and try again'));
			}
			
			// copy from template
			$model->copyDateFromTemplate($_POST['username'].'_');
			
			// creating user dir
			$userdir=$this->_config->userdatadir.'/'.$_POST['username'];
			if ( !file_exists($userdir) ) {
				mkdir($userdir);
			}
			
			// updating admin credential
			$this->_db->update($_POST['username'].'_admin',array(
				'admin_username'	=> $_POST['username'],
				'admin_email'			=> $_POST['email'],
				'admin_password'	=> $_POST['password']
			),array('admin_username=?' => 'admin'));
			
			// updating config sitename
			$this->_db->update($_POST['username'].'_config',array(
				'config_value'	=> $_POST['fullname']
			),array('config_name=?'	=> 'sitename'));
			
			// done
			$website = 'http://'.$_POST['username'].'.'.$this->_config->configinstance->mu->domain;
			$this->_helper->json(array('stat'=>'success','website'=>$website,'username'=>$_POST['username'],'password'=>$_POST['password']));
		}
		else
		{
			$this->_forward('error404');
		}
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
}