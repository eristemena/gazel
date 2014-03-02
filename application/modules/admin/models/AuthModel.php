<?php

require_once "Gazel/Model.php";

class Admin_Model_Auth extends Gazel_Model
{
	public function setAuth($email,$password)
	{
		$authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
		
		$dbselect=$this->_db->select()->from($this->__admin)->where('admin_email=? or admin_username=?',$email,$email)->where('admin_password=?',$password);
		if ( $res=$this->_db->fetchRow($dbselect) ) 
		{
			$authAdmin->auth->id=$res['admin_id'];
			$authAdmin->auth->email=$res['admin_email'];
			$authAdmin->auth->name=$res['admin_name'];
			$authAdmin->auth->username=$res['admin_username'];
			$authAdmin->auth->password=$res['admin_password'];
			$authAdmin->auth->admingroupid=$res['admingroup_id'];
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * we use this for Multiple User
	 * surely need some modification in the future
	 **/
	public function loginAsUser($user)
	{
		$prefix = $user.'_';
		
		$authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
		
		$dbselect=$this->_db->select()->from($prefix.'admin')->where('admin_email=? or admin_username=?',$user,$user);
		if ( $res=$this->_db->fetchRow($dbselect) ) 
		{
			$authAdmin->auth->id=$res['admin_id'];
			$authAdmin->auth->email=$res['admin_email'];
			$authAdmin->auth->name=$res['admin_name'];
			$authAdmin->auth->username=$res['admin_username'];
			$authAdmin->auth->password=$res['admin_password'];
			$authAdmin->auth->admingroupid=$res['admingroup_id'];
			
			$authAdmin->auth->asUser = $user; // differentiate with login through login box
			
			return true;
		} else {
			return false;
		}
	}
	
	public function loginAsMaster()
	{
		$authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
		
		$dbselect=$this->_db->select()->from('admin')->where('admingroup_id=?','1')->limit(1);
		if ( $res=$this->_db->fetchRow($dbselect) ) 
		{
			$authAdmin->auth->id=$res['admin_id'];
			$authAdmin->auth->email=$res['admin_email'];
			$authAdmin->auth->name=$res['admin_name'];
			$authAdmin->auth->username=$res['admin_username'];
			$authAdmin->auth->password=$res['admin_password'];
			$authAdmin->auth->admingroupid=$res['admingroup_id'];
			
			unset($authAdmin->auth->asUser); // unset asUser
			
			return true;
		} else {
			return false;
		}
	}
}