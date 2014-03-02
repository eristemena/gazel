<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_InstallController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Install';
		unset($this->view->submenu);
		
		$this->checkAdminAccess('admin.extension.install');
	}
	
	public function indexAction()
	{
		$formUpload = $this->getFormUpload();
		if ( $_POST && $formUpload->isValid($_POST) )
		{
			if ( $formUpload->package->receive() && $formUpload->package->isUploaded() )
			{
				$fn=substr(strtolower(basename($formUpload->package->getFileName())),0,-4);
				//$t=$this->getThemeAvailable();
				
				$zip = new ZipArchive();
				if ($zip->open($formUpload->package->getFileName()) !== TRUE) 
				{
					$formUpload->package->setErrors(array("Could not open archive"));
				}
				else
				{
					$tmp=sys_get_temp_dir();
					$zip->extractTo($tmp);
					
					if ( !file_exists($tmp.'/'.$fn.'/'.$fn.'.xml') )
					{
						$formUpload->package->setErrors(array('"'.$fn.'.xml" is not exist in the zip file'));
					}
					else
					{
						$tmpdir=$tmp.DIRECTORY_SEPARATOR.$fn;
						
						$xmlfile = $tmpdir.DIRECTORY_SEPARATOR.$fn.'.xml';
						$extinfo = $this->_helper->readExtensionXml($xmlfile);

						// check required version
						if( $extinfo['required']['gazel']['version'] )
						{
							require_once "Gazel/Version.php";
							$compare = (int) Gazel_Version::compareVersion($extinfo['required']['gazel']['version']);
							$thisVersion = Gazel_Version::VERSION;

							if( $compare>0 ){
								$formUpload->package->setErrors(array('Package "'.$fn.'" requires GAZEL version "'.$extinfo['required']['gazel']['version'].'", current installed version is "'.$thisVersion.'"'));
							}
						}

						if ( $extinfo['type']=='module' )
						{
							if ( is_dir($this->_config->applicationdir.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$fn) )
							{
								$formUpload->package->setErrors(array('Module "'.$fn.'" already exists'));
							}
							else
							{
								$zip->extractTo($this->_config->applicationdir.DIRECTORY_SEPARATOR.'modules');
								$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'module'),null,true);
							}
						}
						elseif ( $extinfo['type']=='theme' )
						{
							if ( is_dir($this->_config->themespath.DIRECTORY_SEPARATOR.$fn) )
							{
								$formUpload->package->setErrors(array('Theme "'.$fn.'" already exists'));
							}
							else
							{
								$zip->extractTo($this->_config->themespath);
								$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'theme'),null,true);
							}
						}
						elseif ( $extinfo['type']=='widget' )
						{
							if ( is_dir($this->_config->applicationdir.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$fn) )
							{
								$formUpload->package->setErrors(array('Widget "'.$fn.'" already exists'));
							}
							else
							{
								$zip->extractTo($this->_config->applicationdir.DIRECTORY_SEPARATOR.'widgets');
								$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'widget'),null,true);
							}
						}
						elseif ( $extinfo['type']=='plugin' )
						{
							if ( is_dir($this->_config->applicationdir.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$fn) )
							{
								$formUpload->package->setErrors(array('Plugin "'.$fn.'" already exists'));
							}
							else
							{
								$zip->extractTo($this->_config->applicationdir.DIRECTORY_SEPARATOR.'plugins');
								$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'plugin'),null,true);
							}
						}
						elseif ( $extinfo['type']=='router' )
						{
							if ( is_dir($this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'.DIRECTORY_SEPARATOR.$fn) )
							{
								$formUpload->package->setErrors(array('Router "'.$fn.'" already exists'));
							}
							else
							{
								$zip->extractTo($this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers');
								$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'router'),null,true);
							}
						}
						
						$this->_helper->removeDir($tmpdir);
					}
				}
			}
		}
		
		$this->view->formUpload = $formUpload;
	}
	
	public function getFormUpload()
	{
		$form = new Zend_Form();
		$form->setAction($_SERVER['REQUEST_URI']);
		
		$el=$form->createElement('file','package');
		$el->addValidator('Extension', false, 'zip')
			->setDestination(sys_get_temp_dir())
			->setLabel('Upload')
		;
		$form->addElement($el);
		
		$el=$form->createElement('submit','submit');
		$form->addElement($el);
		
		return $form;
	}
	
}