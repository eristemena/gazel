<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_PageController extends Gazel_Controller_Action_Admin
{
	
	public function initAdmin()
	{
		$this->view->moduletitle = 'Page Manager';
		
		$this->checkAdminAccess('admin.content.page');
	}
	
	public function indexAction()
	{
		//for translate
		//$translate= Zend_Registry::get('translate');	
		//
		if ( is_array($this->_tableAdmin->ordering['admin']['page']) ) {
			$orderid=$this->_tableAdmin->ordering['admin']['page']['id'];
			$ordertype=$this->_tableAdmin->ordering['admin']['page']['type'];
			
			if ( $orderid=='page_order' ) {
				$order=array('s.section_id asc','a.page_order asc');
			} elseif ( $orderid=='section_desc' ) {
				$order=array("s.section_id $ordertype",'a.page_order asc');
			} else {
				$order=array("$orderid $ordertype");
			}
		} else {
			$order=array('s.section_id asc','a.page_order asc');
		}
		
		$dbselect=$this->_db
			->select()
			->from(array('a' => $this->__page))
			->joinLeft(array('s' => $this->__section),'a.section_id=s.section_id',array('section_desc'))
			->joinLeft(array('c' => $this->__admin),'a.page_admin_crtdby=c.admin_id',array('admin_name as crtdby'))
			->joinLeft(array('e' => $this->__admin),'a.page_admin_edtdby=e.admin_id',array('admin_name as edtdby'))
			->order($order)
		;
		$paginator=$this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
		
		$this->view->submenu['makedefault']=array(
			'title'	=> $this->_translate->_('Make Default'),
			'url'	=> 'javascript:makeDefault()'
		);
	}
	
	public function togglepublishedAction()
	{
		$id=$this->_getParam('id');
		if ( $this->_getParam('p')=='y' ) {
			$np='n';
		} else {
			$np='y';
		}
		
		$this->_db->update($this->__page,array('page_published'=>$np),"page_id='$id'");
		$this->redirect(array('action'=>'index','page'=>$this->_getParam('page')));
	}
	
	public function makedefaultAction()
	{
		$id=$this->_getParam('id');
		$this->_db->update($this->__page,array('page_default'=>'n'));
		$this->_db->update($this->__page,array('page_default'=>'y'),array("page_id='$id'"));
		$this->redirect(array('action'=>'index'));
	}
	
	public function reorderAction()
	{
		if ( $to=$_POST['to'] )
		{
			$id=$_POST['id'];
			$order=$_POST['order'];
			
			if ($to=='up'){
				if ($order==1) {
					$order=0.1;
				}else{
					$order=$order-1.1;
				}
			}else{
				$order=$order+1.1;
			}
			
			$this->loadModel('page')->edit(array('page_order'=>$order),$id);
			$this->loadModel('page')->reorder();
		}
		else
		{
			$id=explode(',',$_POST['ids']);
			$val=explode(',',$_POST['vals']);
			
			foreach ( $id as $k=>$i )
			{
				$this->loadModel('Page')->edit(array('page_order'=>$val[$k]),$i);
			}
			
			$this->loadModel('Page')->reorder();
		}
		exit;
	}
	
	public function formAction()
	{
		
		$form = $this->getAdminForm($this->getForm());
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			// admin_id
			$r=$this->_db->fetchRow($this->_db->select()->from($this->__admin)->where('admin_email=?',$this->_authAdmin->auth->email)->orWhere('admin_username=?',$this->_authAdmin->auth->email));
			
			if ( empty($values['page_alias']) )
			{
				require_once "Gazel/Filter/PostSlug.php";
				$f=new Gazel_Filter_PostSlug();
				$values['page_alias']=$f->filter($form->getUnfilteredValue('page_title'));
			}
			
			if ( $this->_getParam('action')=='edit' )
			{
				$values['page_admin_edtdby']=$r['admin_id'];
				$values['page_edtdon']=new Zend_Db_Expr('now()');
				
				$this->loadModel('page')->edit($values,$this->_getParam('id'));
				
				// reorder
				$this->loadModel('page')->reorderBySection($values['section_id']);
				
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			else
			{
				$values['page_type']='static';
				$values['page_admin_crtdby']=$r['admin_id'];
				$values['page_crtdon']=new Zend_Db_Expr('now()');
				
				$this->loadModel('page')->add($values);
				
				// reorder
				$this->loadModel('page')->reorderBySection($values['section_id']);
				
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
			$res=$this->_db->fetchRow($this->_db->select()->from($this->__page)->where('page_id=?',$this->_getParam('id')));
		}
		
		$form = new Gazel_Form();
		
		// title
		$title=$form->createElement('text','page_title')
			->setLabel($this->_translate->_('Title'))
			->setAttribs(array('size'=>45))
			->setRequired(true)
			->setValue($res['page_title'])
		;
		$form->addElement($title);
		
		// alias
		$alias=$form->createElement('text','page_alias')
			->setLabel($this->_translate->_('Alias'))
			->setAttribs(array('size'=>35))
			->addFilter('PostSlug')
			->setValue($res['page_alias'])
		;
		$form->addElement($alias);
		
		// section
		$section=$form->createElement('select','section_id')
			->setLabel($this->_translate->_('Section'))
			->addMultiOptions($this->loadModel('Section')->getOptions())
			->setValue($res['section_id'])
			->setRequired(true)
		;
		$form->addElement($section);
		
		$type=$form->createElement('static','page_type');
		$type->setLabel($this->_translate->_('Type'));
		
		if ( $this->_getParam('action')=='edit' ) {
			$type->setValue($res['page_type']);
			$this->view->page_type=$res['page_type'];
		} else {
			$type->setValue('static');
			$this->view->page_type='static';
		}
		$form->addElement($type);
		
		// content
		$content = $form->createElement('richeditor','page_content');
		$content->setLabel($this->_translate->_('Content'))
			->setValue($res['page_content'])
		;
		
		if ( $this->_getParam('action')=='add' || ($this->_getParam('action')=='edit' && $res['page_type']=='static') ) {
			$form->addElement($content);
		}
		
		//$form->addDisplayGroup(array('page_content'),'static');
		
		// module
		$module = $form->createElement('select','page_module');
		$module->setLabel($this->_translate->_('Content'))
			->addMultiOptions($this->loadModel('Module')->getOptions())
			->setValue($res['page_module'])
		;
		//$form->addElement($module);
		
		//$form->addDisplayGroup(array('page_module'),'module');
		
		// published
		$published=$form->createElement('radio','page_published')
			->setLabel($this->_translate->_('Publish'))
			->addMultiOptions(array('y'=>$this->_translate->_('Yes'),'n'=>$this->_translate->_('No')))
			->setValue($res['page_published'])
			->setRequired(true)
		;
		$form->addElement($published);
		
		if ( $this->_getParam('action')=='add' ) {
			$order=$form->createElement('hidden','page_order')
				->setValue(1000)
			;
			$form->addElement($order);
		}
		
		return $form;
	}
}