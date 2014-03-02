<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_ThemeController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Frontend Theme Manager';
		unset($this->view->submenu);
		
		$this->view->submenu = array(
			'admin_theme' => array('title' => $this->_translate->_('Backend Theme Manager'), 'url' => $this->_helper->Url->url(array('controller'=>'theme-admin'),null))
		);
		
		$this->checkAdminAccess('admin.extension.theme');
	}
	
	public function indexAction()
	{
		require_once "Zend/Paginator/Adapter/Array.php";
		$adapter = new Zend_Paginator_Adapter_Array($this->loadModel('theme')->getThemeAvailable());
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(12);
		
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
	
	public function deleteAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$theme=$this->_getParam('theme');
			$this->_helper->removeDir($this->_config->themespath.'/'.$theme);
			$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'theme'),null,true);
		}
	}
	
	public function installAction()
	{
		$themename=$this->_getParam('theme');
		$this->_db->update($this->__config,array('config_value'=>$themename),"config_name='themename'");
		
		$this->redirect(array('action'=>'index'));
	}
	
	public function downloadAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$theme=$this->_getParam('theme');
			$dir=$this->_config->themespath.'/'.$theme;
			
			$filename=tempnam(sys_get_temp_dir(),'themedownload');
			$this->_helper->Zip->zip($filename,$dir,$theme.'/');
			$filesize = @filesize($ilename);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".$theme.".zip;" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$filesize);
			readfile("$filename");
			
			@unlink($filename);
			exit;
		}
	}
	
	public function getThemeXml($modulename)
	{
		$mxml=$this->_config->themedir.DIRECTORY_SEPARATOR.$modulename.'.xml';
		
		if ( !file_exists($mxml) ) {
			return false;
		} else {
			return simplexml_load_file($mxml);
		}
	}
}