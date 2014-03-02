<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_ConfigController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle="Global Configuration";
		$this->view->submenu=array();
		
		$this->checkAdminAccess('admin.system.config');
	}
	
	public function indexAction()
	{
		$msg=$this->_flashMessenger->getMessages();
		$this->view->successmsg = $msg[0];
		
		$form=$this->getAdminForm($this->getForm());
		
		if ( $form->logo->receive() && $form->logo->isUploaded() )
		{
			$isUploaded=true;
			require_once "Gazel/File/Info.php";
			$cinfo=new Gazel_File_Info($form->logo->getFileName());
			$finfo=$cinfo->getInfo();
			$i=$form->logo->getFileInfo();
			$finfo['size']=$i['download_filename']['size'];
			
			$info=getimagesize($form->logo->getFileName());
			$finfo['width']=$info[0];
			$finfo['height']=$info[1];
		}
		else
		{
			$isUploaded=false;
		}
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			if ( $isUploaded )
			{
				$nname=strtolower(str_replace(array(' '),array('_'),$finfo['name']));
				$nfile=$nname;
				$newfile=$this->_config->userdatadir.DIRECTORY_SEPARATOR.$nfile;
				
				if ( $finfo['name']!=$nname ){
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					rename($form->logo->getFileName(),$newfile);
				}
				$values['logo']=$this->_config->userdataurl.'/'.$nname;
				$values['logo_width']=$finfo['width'];
				$values['logo_height']=$finfo['height'];
			}
			else
			{
				unset($values['logo']);
			}
			
			$this->loadModel('config')->update($values);
			
			$this->_flashMessenger->addMessage('The configurations have been updated.');
			$this->_helper->redirector->gotoRouteAndExit(array('action'=>'index'));
		}
		
		$this->view->form=$form;
	}
	
	public function getForm()
	{	
		$ps = DIRECTORY_SEPARATOR;
		$content = $this->_config->applicationdir.$ps.'modules'.$ps.$this->_request->getModuleName().$ps.'languages'.$ps; 
		 // create a handler for the directory
		$handler = opendir($content);

		// open directory and walk through the filenames
		while ($file = readdir($handler)) {

		  // if file isn't this directory or its parent, add it to the results
		  if ( $file != "." && $file != "..") {
			
			$results[] = $file;
		  }

		}
		// tidy up: close the handler
		closedir($handler);
		
		for ($i=0;$i<count($results);$i++) {
			$fname = $results[$i];
			$f = explode('.', $fname);
			$filename = $f[0];
			$fileext = $f[1];

			// Type your code here
			$var[$filename]=$this->_translate->_($filename);
		}
		
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from($this->__config));
		foreach ( $res as $r )
		{
			$config[$r['config_name']]=$r['config_value'];
		}
		
		$form=new Gazel_Form();
		$form->setAction($this->_helper->Url->url(array('action'=>'index')))
			->setMethod('post')
		;
		
		$el=$form->createElement('text','sitename');
		$el->setLabel($this->_translate->_('Site Name'))
			->setRequired(true)
			->setAttribs(array('size'=>45))
			->setValue($config['sitename'])
		;
		$form->addElement($el);
		
		$el=$form->createElement('file','logo');
		$el->setLabel($this->_translate->_('Logo'))
			->setAttribs(array('size'=>45))
			->addValidator('Extension', false, 'jpg,png,gif,swf')
			->setDestination($this->_config->publicdir.'/data/user')
			->setDescription('File Type: jpg,png,gif,swf')
		;
		
		if ( $config['logo'] )
		{
			$el->addDecorator(
					array('tt1'=>'ViewScript'),
					array(
						'viewScript' => 'config/logo.html',
						'placement'=>'prepend',
						'ext'=>strtolower(substr($config['logo'],-3)),
						'src'=>$config['logo'],
						'logo_width' => $config['logo_width'],
						'logo_height' => $config['logo_height']
					)
			);
		}
		
		$form->addElement($el);
		
		$el=$form->createElement('select','uselogo');
		$el->setLabel($this->_translate->_('Use Logo'))
			->setMultiOptions(array('y' => 'Yes','n' => 'No'))
			->setValue($config['uselogo'])
		;
		$form->addElement($el);
		
		$submit=$form->createElement('submit','Submit')
			->setIgnore(true)
		;
		
		//------------------- set language----
		if($config['userlanguage']){
			
		}
		$lg=$form->createElement('select','uselanguage');
		$lg->setLabel($this->_translate->_('Set Language'))
			->setRequired(true)
			->setMultiOptions($var)
			->setValue($config['uselanguage'])
			->setAttrib("checked","checked")
			
		;
		$form->addElement($lg);
		//-----------------
		
		$form->addElement($submit);
		
		
		return $form;
	}
}