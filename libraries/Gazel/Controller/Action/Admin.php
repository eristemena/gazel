<?php

require_once "Gazel/Controller/Action.php";
require_once "Gazel/Menu/Xml.php";
require_once "Zend/Date.php";

class Gazel_Controller_Action_Admin extends Gazel_Controller_Action
{
	protected $_authAdmin;
	protected $_tableOrdering;
	protected $_acl;
	protected $_flashMessenger;
	
	public function initGazel()
	{
		// specifying view script paths (pls mind the order, last in first out)
		$this->_helper->layout->getView()->addScriptPath($this->_config->applicationdir.'/modules/admin/views/scripts');
		$this->_helper->layout->getView()->addScriptPath($this->_config->applicationdir.'/modules/'.$this->_getParam('module').'/views/scripts');
		$this->_helper->layout->getView()->addScriptPath($this->_config->applicationdir.'/modules/'.$this->_getParam('module').'/views/scripts');
		
		if ( $this->_config->themeadminname ) {
			$this->_helper->layout->getView()->addScriptPath($this->_config->publicdir.'/themes/'.$this->_config->themeadminname);
		}
		
		$this->_authAdmin = new Zend_Session_Namespace($this->_config->sessionAdminNamespace);
		$this->_tableAdmin = new Zend_Session_Namespace('Gazel_Admin_Table');
		
		// flashMessenger
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		$this->checkAuth();
		
		$this->_acl = $this->loadModel('acl','admin')->constructAcl($this->_authAdmin->auth->admingroupid);  // construct ACL
	}
	
	public function preDispatch()
	{	
		$this->view->submenu = array(
			'browse' => array('title' => 'Browse', 'url' => $this->_helper->Url->url(array('action'=>'index'),null), 'li-class' => 'admin-browse'),
			'add' => array('title'	=> 'Add', 'url' => $this->_helper->Url->url(array('action'=>'add'),null), 'li-class' => 'admin-add'),
			'delete' => array('title'	=> 'Delete', 'url' => 'javascript:admdelete()', 'li-class' => 'admin-delete')
		);
		
		if ( $this->_getParam('module')!='admin' )
		{
			$this->checkAdminAccess('admin.module.'.$this->_getParam('module'));
		}
		
		$dbselect=$this->_db
			->select()
			->from(array('m' => $this->__module),array('m.module_name'))
			->from(array('p' => $this->__page),array('p.page_title'))
			->where('m.module_name=p.page_module')
			->where('m.module_name=?',$this->_getParam('module'))
		;
		
		$res=$this->_db->fetchRow($dbselect);
		
		$this->moduletitle=$this->_translate->_($res['page_title']);
		
		$this->view->moduletitle=$this->moduletitle;
		
		$this->initAdmin();
	}
	
	public function initAdmin() 
	{
		
	}
	
	public function orderAction()
	{
		$module=$this->_getParam('module');
		$controller=$this->_getParam('controller');
		
		$id=$_POST['id'];
		$type=$_POST['type'];
		
		if ( is_array($this->_tableOrdering) ) {
			$sess=$this->_tableOrdering;
		} else {
			$sess=array();
		}
		
		$sess[$module][$controller]=array(
			'id'	=> $id,
			'type'=> $type
		);
		
		$this->_tableAdmin->ordering=$sess;
		exit;
	}
	
	public function searchAction()
	{
		$module=$this->_getParam('module');
		$controller=$this->_getParam('controller');
		
		$keyword = $this->_request->getPost('keyword');
		$fieldname = $this->_request->getPost('fieldname');
		$criteria = $this->_request->getPost('criteria');
		$mode = $this->_request->getPost('mode');
		
		if ( $mode=='date' )
		{
			$d=new Zend_Date($keyword,'dd/mm/yyyy');
			$keyw=$d->toString('yyyy-mm-dd');
		}
		else
		{
			$keyw=$keyword;
		}
		
		if ( $criteria=='contains' ) 
		{
			$where = "$fieldname like ".$this->_db->quote('%'.$keyw.'%');
		}
		elseif ( $criteria=='equal' )
		{
			$where = "$fieldname = ".$this->_db->quote($keyw);
		}
		elseif ( $criteria=='greater' )
		{
			$where = "$fieldname >= ".$this->_db->quote($keyw);
		}
		elseif ( $criteria=='less' )
		{
			$where = "$fieldname <= ".$this->_db->quote($keyw);
		}
		
		if ( $_POST['clear']==1 )
		{
			unset($this->_tableAdmin->search[$module][$controller]);
		}
		else
		{
			if ( is_array($this->_tableAdmin->search) ) 
			{
				$sess=$this->_tableAdmin->search;
			}
			else
			{
				$sess=array();
			}
			
			$sess[$module][$controller]=array(
				'keyword'	=> $keyword,
				'fieldname'=> $fieldname,
				'criteria' => $criteria,
				'mode' => $mode,
				'where'		=> $where
			);
			
			$this->_tableAdmin->search=$sess;
		}
		exit;
	}
	
	public function checkAuth($set=true)
	{
		if ( $set )
		{
			if ( $this->_authAdmin->auth->email && $this->_authAdmin->auth->password )
			{
				$dbselect=$this->_db
					->select()
					->from('admin')
					->where('admin_email=? or admin_username=?',$this->_authAdmin->auth->email,$this->_authAdmin->auth->email)
					->where('admin_password=?',$this->_authAdmin->auth->password)
				;
				if ( $res=$this->_db->fetchRow($dbselect) ) 
				{
					$this->_helper->layout->setLayout('master');
					
					if ( $modulename=$this->_request->getParam('module') )
					{
						$res=$this->_db->fetchRow('select * from page where page_module=?',$modulename);
						$this->view->moduletitle=$res['page_title'];
					}
				}
			}
			else 
			{
				$this->redirect(array(
					'action'=>'index',
					'controller'=>'login',
					'module'=>'admin'
				));
			}
		}
	}
	
	public function unsetAuth()
	{
		unset($this->_authAdmin->auth);
		unset($this->_tableAdmin);
	}
	
	public function getAuth()
	{
		return $this->_authAdmin;
	}
	
	public function getOrdering($module=null,$controller=null)
	{
		if ( !$module ) {
			$module=$this->_getParam('module');
		}
		if ( !$controller ) {
			$controller=$this->_getParam('controller');
		}
		
		if ( $order=$this->_tableAdmin->ordering[$module][$controller] )
		{
			return $order['id'].' '.$order['type'];
		}
		else
		{
			return '';
		}
	}
	
	public function getSearch()
	{
		if ( !$module ) {
			$module=$this->_getParam('module');
		}
		if ( !$controller ) {
			$controller=$this->_getParam('controller');
		}
		
		if ( $search=$this->_tableAdmin->search[$module][$controller] )
		{
			return $search['where'];
		}
		else
		{
			return '1';
		}
	}

	/**
	 * You can override this to modify the form
	 */
	protected function _formModify($form, $val)
	{
		return $form;
	}

	/**
	 * You can override this to accept value submitted during edit/add
	 */
	protected function _formReceive($form)
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');

		if ( $action=='edit' )
		{
			$values = $form->getValues();
			$this->loadModel($controller)->$action($values,$_POST['id']);
		
			$this->redirect(array('action'=>'index','page'=>$_POST['page']));
		}
		elseif ( $action=='add' )
		{
			$this->loadModel($controller)->$action($form->getValues());
		
			$this->redirect(array('action'=>'index'));
		}
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
			$val = $this->loadModel($controller)->get($this->_getParam('id'));
			$form->setFormValues($val);
		}

		$form = $this->_formModify($form,$val); // let the developer to override

		if( $this->_request->isPost() && $form->isValid($_POST) )
		{
			$this->_formReceive($form);
		}
		
		$this->view->form=$form;
	}
	
	public function addAction()
	{
		$this->_forward('form');
	}
	
	public function editAction()
	{
		$this->_forward('form');
	}
	
	public function deleteAction()
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');
		$module=$this->_getParam('module');
		
		if ( is_array($_POST['cb']) ) {
			try {
				$this->loadModel($controller)->delete($_POST['cb']);
				echo json_encode(array(
					'stat'=>'success',
					'msg'=>$this->_helper->Url->url(array('action'=>'index','page'=>$this->_getParam('page')))
				));
			} catch (Exception $e) {
				echo json_encode(array(
					'stat'=>'failed',
					'msg'=>$e->getMessage()
				));
			}
		}
		exit;
	}
	
	public function getForm()
	{
		return new Gazel_Form();
	}
	
	public function isAllowed($resource)
	{
		return $this->_acl->isAllowed('role',$resource);
	}
	
	public function checkAdminAccess($resource)
	{
		$res = $this->loadModel('acl','admin')->getAclResources();
		
		if ( !isset($res[$resource]) )
		{
			$this->_helper->Redirector->gotoRoute(array('action'=>'404','controller'=>'index','module'=>'admin'));
		}
		elseif ( !$this->isAllowed($resource) )
		{
			$this->_helper->Redirector->gotoRoute(array('action'=>'noaccess','controller'=>'index','module'=>'admin'));
		}
	}
	
	public function getAdminForm($form)
	{
		// id
		$id = $form->createElement('hidden','id')
			->setValue($this->_getParam('id'))
			->setIgnore(true)
		;
		$form->addElement($id);
		
		// page
		$page = $form->createElement('hidden','page')
			->setValue($this->_getParam('page'))
			->setIgnore(true)
		;
		$form->addElement($page);
		
		$form->gazelAddElementSubmit();
		
		//$form->addElementDecorators(array('HtmlTag',array('tag'=>'div')));
		//$form->gazelTableDecorator();
		
		$els=$form->getElements();
		foreach ( $els as $el )
		{
			if ( !in_array($el->getName(),array('id','page')) )
			{
				$el->addDecorators(array(
					array(array('t2' => 'HtmlTag'),array('tag'=>'div','placement'=>'append','style'=>'clear:both')),
					array(array('t' => 'HtmlTag'),array('tag'=>'div','class'=>'row'))
				));
			}
		}
		
		return $form;
	}

	/**
	 * $submenu = array(
	 *    'submenu-name' => array(
	 *         'title' => 'submenu-title',
	 *         'url' => 'submenu-url',
	 *         'li-class' => 'submenu-li-class', // optional
	 *     )
	 * );
	 *
	 * @params array $submenu
	 */
	public function appendSubMenu($submenu)
	{
		$this->view->submenu = array_merge($this->view->submenu, $submenu);
	}

	/**
	 * $submenu = array(
	 *    'submenu-name' => array(
	 *         'title' => 'submenu-title',
	 *         'url' => 'submenu-url',
	 *         'li-class' => 'submenu-li-class', // optional
	 *     )
	 * );
	 *
	 * @params array $submenu
	 */
	public function prependSubMenu($submenu)
	{
		$this->view->submenu = array_merge($submenu, $this->view->submenu);
	}

	/**
	 * $submenu = array(
	 *    'submenu-name' => array(
	 *         'title' => 'submenu-title',
	 *         'url' => 'submenu-url',
	 *         'li-class' => 'submenu-li-class', // optional
	 *     )
	 * );
	 *
	 * @params array $submenu
	 */
	public function setSubMenu($submenu)
	{
		$this->view->submenu = $submenu;
	}
	
	public function setSuccessStatus($msg)
	{
		unset($this->view->successmsg);
		$this->view->successmsg=$msg;
	}
}