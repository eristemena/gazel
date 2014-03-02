<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_MenuController extends Gazel_Controller_Action_Admin
{
	protected $_mtype=0;
	
	public function initAdmin()
	{
		if ( !$this->_mtype=$this->_getParam('menutype_id') ) {
			$this->redirect('index','menutype');
		} else {
			$res=$this->_db->fetchRow($this->_db->select()->from('menutype')->where('menutype_id=?',$this->_mtype));
		}
		
		$this->view->moduletitle = 'Menu &raquo; '.$res['menutype_desc'];
		
		unset($this->view->submenu);
		$this->view->submenu = array(
			'browsemtype' => array('title' => 'Back to Menu Type', 'url' => $this->_helper->url('index','menutype')),
			'browse' => array('title'	=> 'Browse', 'url' => $this->_helper->Url->url(array('action'=>'index'))),
			'add' => array('title'	=> 'Add', 'url' => $this->_helper->Url->url(array('action'=>'add'))),
			'delete' => array('title'	=> 'Delete', 'url' => 'javascript:admdelete()')
		);
		
		$this->checkAdminAccess('admin.content.menu');
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db
			->select()
			->from(array('m'=>'menu'))
			->where('menutype_id=?',$this->_mtype)
			->order(array('m.menu_order asc','m.menu_name asc'))
		;
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;

	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->_db->fetchRow($this->_db->select()->from('menu')->where('menu_id=?',$this->_getParam('id')));
		}
		
		$form = new Gazel_Form();
		
		// name
		$name=$form->createElement('text','menu_name')
			->setLabel('Name')
			->setAttribs(array('size'=>45,'maxlength'=>40))
			->addValidator('stringLength', false, array(4, 40))
			->setRequired(true)
			->addFilter('PostSlug')
			->setValue($res['menu_name'])
		;
		$form->addElement($name);
		
		// link
		$link=$form->createElement('text','menu_link')
			->setLabel('Link')
			->setAttribs(array('size'=>55))
			->addValidator('stringLength', false, array(4, 100))
			->setValue($res['menu_link'])
		;
		$form->addElement($link);
		
		// published
		$published=$form->createElement('radio','menu_published')
			->setLabel('Published')
			->addMultiOptions(array('y'=>'Yes','n'=>'No'))
			->setValue($res['menu_published'])
			->setRequired(true)
		;
		$form->addElement($published);
		
		// mtype
		$mtype=$form->createElement('hidden','menutype_id')
			->setValue($this->_mtype)
		;
		$form->addElement($mtype);
		
		return $form;
	}
}