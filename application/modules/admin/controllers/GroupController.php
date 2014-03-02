<?php
require_once "Gazel/Controller/Action/Admin.php";
include_once "Zend/Acl.php";
include_once "Zend/Acl/Resource.php";
include_once "Zend/Acl/Role.php";
//include_once "Connection3.php";

class Admin_GroupController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle="Group Manager";
		
		$this->view->submenu['user']=array(
			'title'	=> 'User',
			'url'		=> $this->_helper->Url->url(array('action'=>'index','controller'=>'user','module'=>'admin'))
		);
		
		$this->checkAdminAccess('admin.system.user');
	}
	
	public function indexAction()
	{
		$dbselect=$this->getDb()->select()->from($this->__admingroup)->order('admingroup_id desc');
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
	}
	
	public function aclxmlAction()
	{
		$id=$this->_getParam('id');
		
		$bar=$this->_db->fetchRow('select * from '.$this->__admingroup.' where admingroup_id=?',$id);
		$ack =unserialize($bar[admingroup_acl]);
		
		if ( !is_array($ack) ) {
			$ack=array();
		}
		
		$xml = '<tree id="0">';
		$xml.= '<item text="Admin" id="admin" open="1" im0="tombs.gif" im1="tombs.gif" im2="iconSafe.gif" call="1" select="1">';
		
		/** system **/
		$checked=(in_array('admin.system',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="System" id="admin.system" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' >';
		
		$checked=(in_array('admin.system.user',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="User Manager" id="admin.system.user" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$checked=(in_array('admin.system.config',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Global Configuration" id="admin.system.config" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$xml.= '</item>';
		/** end system **/
		
		/** content **/
		$checked=(in_array('admin.content',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Content" id="admin.content" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' >';
		
		$checked=(in_array('admin.content.page',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Page Manager" id="admin.content.page" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$checked=(in_array('admin.section',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Section Manager" id="admin.section" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$xml.= '</item>';
		/** end content **/
		
		/** extension **/
		$checked=(in_array('admin.extension',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Extensions" id="admin.extension" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' >';
		
		$checked=(in_array('admin.extension.install',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Install" id="admin.extension.install" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$checked=(in_array('admin.extension.module',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Module Manager" id="admin.extension.module" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$checked=(in_array('admin.extension.widget',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Widget Manager" id="admin.extension.widget" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$checked=(in_array('admin.extension.theme',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Theme Manager" id="admin.extension.theme" im0="book_titel.gif" im1="book.gif" im2="book_titel.gif" '.$checked.' />';
		
		$xml.= '</item>';
		/** end extension **/
		
		$checked=(in_array('admin.module',$ack)) ? 'checked="1"' : '';
		$xml.= '<item text="Module" id="admin.module" open="1" im0="tombs.gif" im1="tombs.gif" im2="iconSafe.gif" call="1" '.$checked.'>';
		
		$res=$this->_db->fetchAssoc('select m.module_name,p.page_title from '.$this->__module.' m,'.$this->__page.' p where p.page_module=m.module_name order by module_id asc');
		foreach ($res as $r)
		{
			if(in_array('admin.module.'.$r['module_name'], $ack)) {
				$checked = "1";
			} else {
				$checked = "";
			}
			$xml.= '<item text="'.htmlentities($r[page_title]).'" id="admin.module.'.$r[module_name].'" im0="book_titel.gif" im1="book_titel.gif" im2="book_titel.gif" checked="'.$checked.'"/>';
		}
		
		$xml.= '</item>';
		
		
		$xml.= '</item></tree>';
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo $xml;
		exit;
	}
	
	public function aclAction()
	{
		$this->view->moduletitle="Access Control List";

		if ( $_POST['act']=='edit' )
		{
			$id=$this->_getParam('id');
			$acl = $_POST['acl'];
			
			$acl=explode(',', $acl);
			//echo "<pre>";print_r($acl);echo "</pre>";exit;
			
			$qwer = serialize($acl);
			$data = array('admingroup_acl'=>$qwer);
			$this->getDb()->update('admingroup',$data,"admingroup_id=$id");
				
			//$this->loadModel()->edit(array('admingroup_acl'=>$acl),$this->_getParam('id'));
			$this->redirect(array('action'=>'index'));
		}
		
		//echo "<pre>";print_r($this->loadModel('acl')->getAclResources());echo "</pre>";
		
		$this->view->acl=$this->loadModel()->get($this->_getParam('id'));
		$this->view->listmodules=$this->getDb()->fetchAssoc($this->getDb()->select()->from('module'));
	}
	
	public function formAction()
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');
		
		// get form
		$form = $this->loadForm($controller);
		$form = $this->getAdminForm($form);

		

		$val = array();
		if ( $action == 'edit' )
		{
			$val = $this->loadModel('group')->get($this->_getParam('id'));
			$form->setFormValues($val);

			$form->admingroup_name->setValidators(array(
				array('Db_NoRecordExists', false, array(
					'adapter' => $this->_db,
					'table'	=> $this->__admingroup,
					'field'	=> 'admingroup_name',
					'exclude' => array(
						'field'	=> 'admingroup_name',
						'value' => $val['admingroup_name']
					)
				))
			));
		}
		elseif( $action == 'add' )
		{
			$form->admingroup_name->setValidators(array(
				array('Db_NoRecordExists', false, array(
					'adapter' => $this->_db,
					'table'	=> $this->__admingroup,
					'field'	=> 'admingroup_name'
				))
			));
		}
		
		if ( $_POST && $form->isValid($_POST) )
		{
			if ( $action=='edit' )
			{
				$values = $form->getValues();
				$this->loadModel('group')->edit($values,$_POST['id']);

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
}
