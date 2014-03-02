<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_ModuleController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Module Manager';
		unset($this->view->submenu);
		
		$this->checkAdminAccess('admin.extension.module');
	}
	
	public function indexAction()
	{
		require_once "Zend/Paginator/Adapter/Array.php";
		$adapter = new Zend_Paginator_Adapter_Array($this->loadModel('module')->getModuleAvailable());
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam($this->__page));
		$paginator->setItemCountPerPage(10);
		
		// installed modules
		$res=$this->_db->fetchAssoc($this->_db->select()->from($this->__module));
		$im=array();
		foreach ( $res as $r ) {
			$im[]=$r['module_name'];
		}
		$this->view->installedmodule=$im;
		
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
		$modulename=$this->_getParam('mod');
		if ( $xml=$this->getModuleXml($modulename) )
		{
			$this->view->form=$this->getForm($modulename);
			$this->render('form');
		}
		else
		{
			$this->_forward('404');
		}
	}
	
	public function uninstallAction()
	{
		$modulename = $this->_getParam('mod');
		
		$setup = $this->getModuleSetup($modulename);
		$setup->setDb($this->_db);
		$setup->uninstall();
			
		$this->_db->delete($this->__module,"module_name='".$modulename."'");
		$this->_db->delete($this->__page,"page_module='".$modulename."'");
		
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
			$module=$this->_getParam('mod');
			$dir=$this->_config->applicationdir.'/modules/'.$module;
			
			$filename=tempnam(sys_get_temp_dir(),'moduledownload');
			$this->_helper->Zip->zip($filename,$dir,$module.'/');
			$filesize = @filesize($ilename);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".$module.".zip;" );
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
			$module=$this->_getParam('mod');
			$this->_helper->removeDir($this->_config->applicationdir.'/modules/'.$module);
			$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'module'),null,true);
		}
	}
	
	public function getModuleXml($modulename)
	{
		$mxml1=$this->_config->gazeldir.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$modulename.DIRECTORY_SEPARATOR.$modulename.'.xml';
		$mxml2=$this->_config->applicationdir.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$modulename.DIRECTORY_SEPARATOR.$modulename.'.xml';
		
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
	
	public function formAction()
	{
		$modulename=$this->_getParam('mod');
		$form = $this->getForm($modulename);
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			if ( empty($values['page_alias']) )
			{
				require_once "Gazel/Filter/PostSlug.php";
				$f=new Gazel_Filter_PostSlug();
				$values['page_alias']=$f->filter($form->getUnfilteredValue('page_title'));
			}
			
			// setup module
			$setup = $this->getModuleSetup($modulename);
			$setup->setDb($this->_db);
			
			if ( !$setup->install() )
			{
				$this->view->errors = $setup->getErrors();
			}
			else
			{
				$r=$this->_db->fetchRow($this->_db->select()->from($this->__admin)->where('admin_email=?',$this->_authAdmin->auth->email)->orWhere('admin_username=?',$this->_authAdmin->auth->email));
				$values['page_type']='module';
				$values['page_admin_crtdby']=$r['admin_id'];
				$values['page_module']=$modulename;
				$values['page_crtdon']=new Zend_Db_Expr('now()');
				$this->loadModel('page')->add($values);
				
				// reorder
				$this->loadModel('page')->reorderBySection($values['section_id']);
				
				$this->_db->insert($this->__module,array(
					'module_name'				=> $modulename,
					'module_title'			=> $values['page_title'],
					'module_installed'	=> new Zend_Db_Expr('now()')
				));
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$this->view->form=$form;
	}
	
	public function getForm($modulename)
	{
		$xml=$this->getModuleXml($modulename);
		
		$form = new Gazel_Form();
		
		$form->setAction($this->_helper->url->url(array('action'=>'form')));
		
		// title
		$title=$form->createElement('text','page_title')
			->setLabel('Title')
			->setAttribs(array('size'=>45))
			->setRequired(true)
			->setValue($xml->name)
		;
		$form->addElement($title);
		
		// alias
		$alias=$form->createElement('text','page_alias')
			->setLabel('Alias')
			->setAttribs(array('size'=>35))
			->addFilter('PostSlug')
			->setValue($res['page_alias'])
		;
		$form->addElement($alias);
		
		// section
		$section=$form->createElement('select','section_id')
			->setLabel('Section')
			->addMultiOptions($this->loadModel('Section')->getOptions())
			->setValue($res['section_id'])
			->setRequired(true)
		;
		$form->addElement($section);
		
		// published
		$published=$form->createElement('radio','page_published')
			->setLabel('Publish')
			->addMultiOptions(array('y'=>'Yes','n'=>'No'))
			->setValue('y')
			->setRequired(true)
		;
		$form->addElement($published);
		
		$form->gazelAddElementSubmit();
		
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
	
	public function getModuleSetup($modulename)
	{
		require_once $this->_config->applicationdir.'/modules/'.$modulename.'/Setup.php';
		require_once "Zend/Filter/Word/DashToCamelCase.php";
		$filter=new Zend_Filter_Word_DashToCamelCase();
		$className=$filter->filter($modulename).'_Setup';
		$setup = new $className();
		
		return $setup;
	}
}