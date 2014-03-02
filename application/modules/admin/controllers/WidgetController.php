<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_WidgetController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Widget Manager';
		unset($this->view->submenu);
		
		$this->checkAdminAccess('admin.extension.widget');
	}
	
	public function indexAction()
	{
		require_once "Zend/Paginator/Adapter/Array.php";
		$adapter = new Zend_Paginator_Adapter_Array($this->loadModel('widget')->getWidgetAvailable());
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(10);
		
		// installed widgets
		$res=$this->_db->fetchAssoc($this->_db->select()->from('widget'));
		$im=array();
		foreach ( $res as $r ) {
			$im[]=$r['widget_name'];
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
		$widgetname=$this->_getParam('mod');
		if ( $xml=$this->getWidgetXml($widgetname) )
		{
			$this->_db->insert($this->__widget,array(
				'widget_name'				=> $this->_getParam('mod'),
				'widget_title'			=> $xml->name,
				'widget_installed'	=> new Zend_Db_Expr('now()')
			));
			
			if ( $this->_request->isXmlHttpRequest() )
			{
				$this->_helper->json(array('stat'=>'success','module'=>$widgetname));
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
				$this->_helper->json(array('stat'=>'failed','module'=>$widgetname));
			}
			else
			{
				$this->redirect(array('action'=>'index'));
			}
		}
		
	}
	
	public function uninstallAction()
	{
		$this->_db->delete($this->__widget,"widget_name='".$this->_getParam('mod')."'");
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
			$widget=$this->_getParam('mod');
			$dir=$this->_config->applicationdir.'/widgets/'.$widget;
			
			$filename=tempnam(sys_get_temp_dir(),'widgetdownload');
			$this->_helper->Zip->zip($filename,$dir,$widget.'/');
			$filesize = @filesize($ilename);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".$widget.".zip;" );
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
			$widget=$this->_getParam('mod');
			$this->_helper->removeDir($this->_config->applicationdir.'/widgets/'.$widget);
			$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'widget'),null,true);
		}
	}
	
	public function getWidgetXml($widgetname)
	{
		$mxml1=$this->_config->gazeldir.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$widgetname.DIRECTORY_SEPARATOR.$widgetname.'.xml';
		$mxml2=$this->_config->applicationdir.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$widgetname.DIRECTORY_SEPARATOR.$widgetname.'.xml';
		
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
	
	public function adminAction() // backend for widgets
	{
		$widget=$this->_getParam('widget');
		$res=$this->_db->fetchRow($this->_db->select()->from($this->__widget)->where('widget_name=?',$widget));
		if ( !$res )
		{
			$this->_forward('404');
		}
		else
		{
			$this->view->moduletitle='Widget Configuration: '.$res['widget_title'];
			
			require_once "Zend/Filter/Word/DashToCamelCase.php";
			$filter=new Zend_Filter_Word_DashToCamelCase();
			$fileName=$filter->filter($widget);
			require_once $widget.'/'.$fileName.'.php';
			$className=$fileName.'Widget';
			$class=new $className();
			
			$data = unserialize($res['widget_data']);
			$form = $this->getWidgetForm($class->backendForm($data), $res);
			
			if ( $_POST && $form->isValid($_POST) )
			{
				$values=$form->getValues();
				
				$data=serialize($values);
				$pos=$values['pos'];
				//print_r($values);
				$this->_db->update($this->__widget,array(
					'widget_data'			=> $data,
					'widget_pos'			=> $pos,
					'widget_active'			=> $values['widget_active'],
					'widget_updated'		=> new Zend_Db_Expr('now()')
				),array('widget_id=?' => $_POST['id']));

				$this->_helper->flashMessenger($this->_t->_('Data is updated'));
				$this->_helper->Redirector->gotoRoute(array(),'admin');
			}
			
			$this->view->form=$form;
			$this->view->msg = $this->_helper->flashMessenger->getMessages();
		}
	}
	
	public function getWidgetForm($form, $res)
	{
		if( !$form instanceof Gazel_Form )
		{
			require_once "Gazel/Widget/Exception.php";
			throw new Gazel_Widget_Exception("Widget \"".$widget."\" does not implement backendForm() correctly");
		}

		// position
		$el=$form->createElement('select','pos');
		$el->setLabel('Section')
			->addMultiOptions($this->loadModel('section','admin')->getOptionsForWidget())
			->setValue($res['widget_pos'])
		;
		$form->addElement($el);
		
		// active
		$el=$form->createElement('select','widget_active');
		$el->setLabel('Active')
			->addMultiOptions(array('y' => 'Yes','n' => 'No'))
			->setValue($res['widget_active'])
		;
		$form->addElement($el);
		
		$form = $this->getAdminForm($form);
		$form->id->setValue($res['widget_id']);
		
		return $form;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->loadModel('module')->getWidget($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		
		$username = $form->createElement('text', 'module_title');
		$username->setLabel('Titlessssss')->setValue($res['module_title']);
		
		// Add elements to form:
		$form->addElement($username);
		
		return $form;
	}
}