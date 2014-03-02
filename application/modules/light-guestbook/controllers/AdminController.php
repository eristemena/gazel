<?php

require_once "Gazel/Controller/Action/Admin.php";

class LightGuestbook_AdminController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__guestbook)->order($this->getOrdering())->where($this->getSearch());
		$paginator=$this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
	}
	
	public function formAction()
	{
		$form=$this->getAdminForm($this->getForm());
		$action=$this->_getParam('action');
		
		if ( $_POST && $form->isValid($_POST) )
		{
			if ( $action=='edit' )
			{
				$values=$form->getValues();
				$this->loadModel('guestbook')->edit($values,$_POST['id']);
				
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			elseif ( $action=='add' )
			{
				$this->loadModel('guestbook')->add($form->getValues());
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$this->view->form=$form;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' )
		{
			$val=$this->loadModel('guestbook')->get($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		
		// name
		$el = $form->createElement('text','guestbook_name');
		$el->setRequired(true)
			->setLabel($this->_translate->_('Name'))
			->setAttribs(array('size' => 45))
			->setValue($val['guestbook_name'])
		;
		$form->addElement($el);
		
		// name
		$el = $form->createElement('text','guestbook_email');
		$el->setRequired(true)
			->setLabel($this->_translate->_('Email'))
			->setAttribs(array('size' => 45))
			->setValue($val['guestbook_email'])
		;
		$form->addElement($el);
		
		// msg
		$el = $form->createElement('richeditor','guestbook_msg');
		$el->setRequired(true)
			->setAttribs(array('cols'=>45,'rows'=>5))
			->setLabel($this->_translate->_('Message'))
			->setValue($val['guestbook_msg'])
		;
		$form->addElement($el);
		
		// approve
		$el=$form->createElement('select','guestbook_approve');
		$el->setLabel($this->_translate->_('Approve'))
			->addMultiOptions(array('y' => 'Ya','n' => 'Tidak'))
			->setValue($val['guestbook_approve'])
		;
		$form->addElement($el);
		
		return $form;
	}
}