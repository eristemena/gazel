<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_RouterController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Router Manager';
		unset($this->view->submenu);
		
		$this->checkAdminAccess('admin.extension.router');
	}
	
	public function indexAction()
	{
		require_once "Zend/Paginator/Adapter/Array.php";
		$adapter = new Zend_Paginator_Adapter_Array($this->loadModel('router')->getRouterAvailable());
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(10);
		
		// installed routers
		$res=$this->_db->fetchAssoc($this->_db->select()->from('router'));
		$im=array();
		foreach ( $res as $r ) {
			$im[]=$r['router_name'];
		}
		$this->view->installedrouter=$im;
		
		if ( $this->_config->multipleuser && !$this->_config->mMaster )
		{
			$this->view->canDownload=false;
			$this->view->canDelete=false;
		}
		else
		{
			$this->view->canDownload=true;
			$this->view->canDelete=true;
		}
		
		$this->view->paginator=$paginator;
	}
	
	public function installAction()
	{
		$routername=$this->_getParam('mod');
		if ( $xml=$this->getRouterXml($routername) )
		{
			$this->_db->insert($this->__router,array(
				'router_name'				=> $this->_getParam('mod'),
				'router_title'			=> $xml->name,
				'router_installed'	=> new Zend_Db_Expr('now()')
			));
			
			if ( $this->_request->isXmlHttpRequest() )
			{
				$this->_helper->json(array('stat'=>'success','module'=>$routername));
			}
			else
			{
				$this->redirect(array('action'=>'index'));
			}
		}
		else
		{
			if ( $this->_request->isXmlHttpRequest() )
			{
				$this->_helper->json(array('stat'=>'failed','module'=>$routername));
			}
			else
			{
				$this->redirect(array('action'=>'index'));
			}
		}
		
	}
	
	public function uninstallAction()
	{
		$this->_db->delete($this->__router,"router_name='".$this->_getParam('mod')."'");
		if ( $this->getRequest()->isXmlHttpRequest() ) {
			$this->_helper->json(array('stat'=>'success','module'=>$this->_getParam('mod')));
		} else {
			$this->redirect(array('action'=>'index'));
		}
	}
	
	public function downloadAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$router=$this->_getParam('mod');
			$dir=$this->_config->applicationdir.'/routers/'.$router;
			
			$filename=tempnam(sys_get_temp_dir(),'routerdownload');
			$this->_helper->Zip->zip($filename,$dir,$router.'/');
			$filesize = @filesize($ilename);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".$router.".zip;" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$filesize);
			readfile("$filename");
			
			@unlink($filename);
			exit;
		}
	}
	
	public function deleteAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$router=$this->_getParam('mod');
			$this->_helper->removeDir($this->_config->applicationdir.'/routers/'.$router);
			$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'router'),null,true);
		}
	}
	
	public function getRouterXml($routername)
	{
		$mxml1=$this->_config->gazeldir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routername.DIRECTORY_SEPARATOR.$routername.'.xml';
		$mxml2=$this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$routername.DIRECTORY_SEPARATOR.$routername.'.xml';
		
		if ( !file_exists($mxml1) ) {
			if( !file_exists($mxml2) ) {
				return false;
			} else {
				return simplexml_load_file($mxml2);
			}
		} else {
			return simplexml_load_file($mxml1);
		}
	}
	
	public function adminAction() // backend for routers
	{
		$router=$this->_getParam('router');
		$res=$this->_db->fetchRow($this->_db->select()->from($this->__router)->where('router_name=?',$router));
		if ( !$res )
		{
			$this->_forward('404');
		}
		else
		{
			$this->view->moduletitle='Router Config: '.$res['router_title'];
			
			require_once "Zend/Filter/Word/DashToCamelCase.php";
			$filter=new Zend_Filter_Word_DashToCamelCase();
			$fileName=$filter->filter($router);
			require_once $router.'/'.$fileName.'.php';
			$className=$fileName.'Router';
			$class=new $className();
			
			$data=unserialize($res['router_data']);
			$form=$this->getRouterForm($class->backendForm($data),$res);
			
			if ( $_POST && $form->isValid($_POST) )
			{
				$values=$form->getValues();
				
				$data=serialize($values);
				$pos=$values['pos'];
				//print_r($values);
				$this->_db->update($this->__router,array(
					'router_data'			=> $data,
					'router_pos'			=> $pos,
					'router_active'		=> $values['router_active'],
					'router_updated'	=> new Zend_Db_Expr('now()')
				),array('router_id=?' => $_POST['id']));
			}
			
			$this->view->form=$form;
		}
	}
	
	public function getRouterForm($form,$res)
	{
		// id
		$id = $form->createElement('hidden','id')
			->setValue($res['router_id'])
			->setIgnore(true)
		;
		$form->addElement($id);
		
		// position
		$el=$form->createElement('select','pos');
		$el->setLabel('Position')
			->addMultiOptions(array('left' => 'Left','right' => 'Right'))
			->setValue($res['router_pos'])
		;
		$form->addElement($el);
		
		// active
		$el=$form->createElement('select','router_active');
		$el->setLabel('Active')
			->addMultiOptions(array('y' => 'Yes','n' => 'No'))
			->setValue($res['router_active'])
		;
		$form->addElement($el);
		
		// submit
		$submit = $form->createElement('submit','Submit')
			->setIgnore(true)
		;
		$form->addElement($submit);
		
		return $form;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->loadModel('module')->getRouter($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		
		$username = $form->createElement('text', 'module_title');
		$username->setLabel('Titlessssss')->setValue($res['module_title']);
		
		// Add elements to form:
		$form->addElement($username);
		
		return $form;
	}
}