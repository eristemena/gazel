<?php

require_once "Gazel/Controller/Action/Admin.php";

class LightDownload_AdminController extends Gazel_Controller_Action_Admin
{
	private $_download_file_dir;
	private $_download_thumb_dir;
	
	public function initAdmin()
	{
		$downloaddir=$this->_config->userdatadir.'/downloads/';
		if ( !file_exists($downloaddir) ) {
			mkdir($downloaddir);
		}
		$this->_download_file_dir=$downloaddir;
		
		$downloadthumbdir=$this->_config->userdatadir.'/downloads/thumbs/';
		if ( !file_exists($downloadthumbdir) ) {
			mkdir($downloadthumbdir);
		}
		$this->_download_thumb_dir=$downloadthumbdir;
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__download)->order($this->getOrdering())->where($this->getSearch());
		$paginator=$this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
		//echo $this->_helper->Url->url(array(),'admin');exit;
		//echo $this->_helper->Url->url(array(),'page');exit;
	}
	
	public function formAction()
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');
		
		// get form
		$form=$this->getAdminForm($this->getForm());
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			if ( $form->download_filename->receive() && $form->download_filename->isUploaded() )
			{
				$isUploaded=true;
				require_once "Gazel/File/Info.php";
				$cinfo=new Gazel_File_Info($form->download_filename->getFileName());
				$finfo=$cinfo->getInfo();
				$i=$form->download_filename->getFileInfo();
				$finfo['size']=$i['download_filename']['size'];
				//print_r($form->download_filename->getFileInfo());exit;
				//echo '<pre>';print_r($finfo);echo '</pre>';exit;
			}
			else
			{
				$isUploaded=false;
			}
			
			if ( $action=='edit' )
			{
				if ( $isUploaded ) 
				{
					$values['download_filename']=$finfo['name'];
					$values['download_mime']=$finfo['mime'];
					$values['download_type']=$finfo['type'];
					$values['download_ext']=$finfo['ext'];
					$values['download_filesize']=$finfo['size'];
					$values['download_upload_at']=new Zend_Db_Expr('now()');
					
					$newfile=$this->_download_file_dir.$_POST['id'].'.'.strtolower($finfo['ext']);
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					rename($form->download_filename->getFileName(),$newfile);
				}
				else
				{
					$r=$this->loadModel('admin')->get($_POST['id']);
					unset($values['download_filename']);
					unset($values['download_mime']);
				}
				
				$this->loadModel('admin')->edit($values,$_POST['id']);
				
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			elseif ( $action=='add' )
			{
				$id = $this->loadModel('admin')->add($values);
				
				if ( $isUploaded )
				{
					$data=array();
					$data['download_filename']=$finfo['name'];
					$data['download_mime']=$finfo['mime'];
					$data['download_type']=$finfo['type'];
					$data['download_ext']=$finfo['ext'];
					$data['download_filesize']=$finfo['size'];
					$data['download_upload_at']=new Zend_Db_Expr('now()');
					
					$newfile=$this->_download_file_dir.$id.'.'.strtolower($finfo['ext']);
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					rename($form->download_filename->getFileName(),$newfile);
					
					$this->loadModel('admin')->edit($data,$id);
				}
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$this->view->form=$form;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' )
		{
			$val=$this->loadModel('admin')->get($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		$form->setAttrib('enctype', 'multipart/form-data');
		
		// name
		$name = $form->createElement('text','download_name');
		$name->setRequired(true);
		$name->setAttribs(array('size'=>45));
		$name->setLabel($this->_translate->_('Name'));
		$name->setValue($val['download_name']);
		$form->addElement($name);
		
		// file
		$el = $form->createElement('file','download_filename');
		$el->setAttribs(array('size'=>45));
		$el->setLabel($this->_translate->_('File'));
		$el->addValidator('Extension', false, 'jpg,mp3,avi,wav,ppt,pdf,flv,zip,doc,docx');
		$el->setDestination($this->_download_file_dir);
		$el->addDecorators(array(
			array('Description', array('escape'=>false,'placement'=>'prepend')),
		));
		
		if ( $this->_getParam('action')=='edit' )
		{
			if ( $val['download_type']=='image' )
			{
				$imgurl=$this->_helper->dimage->getDimageUrl('light-download',200,200,$val['download_id'],'jpg');
				//$imgurl=$this->_helper->url->url(array('alias'=>'download','act'=>'pict','id'=>$val['download_id']),'page');
				$el->setDescription('<img src="'.$imgurl.'" />');
			}
		}
		$form->addElement($el);
		
		// desc
		$desc = $form->createElement('richeditor','download_desc');
		$desc->setLabel($this->_translate->_('Description'))
			->setValue($val['download_desc'])
		;
		$form->addElement($desc);
		
		return $form;
	}
}