<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_MenutypeController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle = 'Menu Manager';
		
		$this->checkAdminAccess('admin.content.page');
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db
			->select()
			->from(array('m'=>'menutype'))
			->order(array('m.menutype_name asc'))
		;
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->_db->fetchRow($this->_db->select()->from('menutype')->where('menutype_id=?',$this->_getParam('id')));
		}
		
		$form = new Gazel_Form();
		
		// name
		$name=$form->createElement('text','menutype_name')
			->setLabel('Name')
			->setAttribs(array('size'=>45,'maxlength'=>40))
			->addValidator('stringLength', false, array(4, 40))
			->setRequired(true)
			->addFilter('PostSlug')
			->setValue($res['menutype_name'])
		;
		$form->addElement($name);
		
		// desc
		$alias=$form->createElement('text','menutype_desc')
			->setLabel('Description')
			->setAttribs(array('size'=>55))
			->addValidator('stringLength', false, array(4, 255))
			->setValue($res['menutype_desc'])
		;
		$form->addElement($alias);
		
		// published
		$published=$form->createElement('radio','menutype_published')
			->setLabel('Published')
			->addMultiOptions(array('y'=>'Yes','n'=>'No'))
			->setValue($res['menutype_published'])
			->setRequired(true)
		;
		$form->addElement($published);
		
		return $form;
	}
}