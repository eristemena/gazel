<?php

require_once "Gazel/Controller/Action/Admin.php";

class LightProduct_AdminController extends Gazel_Controller_Action_Admin
{
	private $_product_pict_dir;
	private $_product_thumb_dir;
	
	public function initAdmin()
	{
		$productdir=$this->_config->userdatadir.'/products/';
		if ( !file_exists($productdir) ) {
			mkdir($productdir);
		}
		$this->_product_pict_dir=$productdir;
		
		$productthumbdir=$this->_config->userdatadir.'/products/thumbs/';
		if ( !file_exists($productthumbdir) ) {
			mkdir($productthumbdir);
		}
		$this->_product_thumb_dir=$productthumbdir;
	}
	
	public function indexAction()
	{
		$dbselect=$this->_db->select()->from($this->__product)->order($this->getOrdering())->where($this->getSearch());
		$paginator=$this->getPaginator($dbselect);
		$this->view->paginator=$paginator;
	}
	
	public function formdsdsAction()
	{
		$controller=$this->_getParam('controller');
		$action=$this->_getParam('action');
		
		// get form
		$form=$this->getAdminForm($this->getForm());
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			if ( $action=='edit' )
			{
				if ( $form->product_pict->receive() && $form->product_pict->isUploaded() ) 
				{
					$newfile=$this->_product_pict_dir.$_POST['id'].'.'.$this->getImageType($form->product_pict->getFileName());
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					$values['product_pict']=basename($newfile);
					$values['product_pictmime']=$this->getMime($newfile);
					rename($form->product_pict->getFileName(),$newfile);
				}
				else
				{
					$r=$this->loadModel('admin')->get($_POST['id']);
					$values['product_pict']=$r['product_pict'];
					$values['product_pictmime']=$r['product_pictmime'];
				}
				
				$this->loadModel('admin')->edit($values,$_POST['id']);
				
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			elseif ( $action=='add' )
			{
				$id = $this->loadModel('admin')->add($values);
				
				if ( $form->product_pict->receive() )
				{
					$newfile=$this->_product_pict_dir.$id.'.'.$this->getImageType($form->product_pict->getFileName());
					
					$data=array();
					$data['product_pict']=basename($newfile);
					$data['product_pictmime']=$this->getMime($newfile);
					
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					rename($form->product_pict->getFileName(),$newfile);
					
					$this->loadModel('admin')->edit($data,$id);
				}
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$this->view->form=$form;
	}
	
	protected function _formModify($form,$val)
	{
		$action = $this->_getParam('action');
		
		if ( $_POST && $form->isValid($_POST) )
		{
			$values=$form->getValues();
			
			if ( $action=='edit' )
			{
				if ( $form->product_pict->receive() && $form->product_pict->isUploaded() ) 
				{
					$newfile=$this->_product_pict_dir.$_POST['id'].'.'.$this->getImageType($form->product_pict->getFileName());
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					$values['product_pict']=basename($newfile);
					$values['product_pictmime']=$this->getMime($newfile);
					rename($form->product_pict->getFileName(),$newfile);
				}
				else
				{
					$r=$this->loadModel('admin')->get($_POST['id']);
					$values['product_pict']=$r['product_pict'];
					$values['product_pictmime']=$r['product_pictmime'];
				}
				
				$this->loadModel('admin')->edit($values,$_POST['id']);
				
				$this->redirect(array('action'=>'index','page'=>$_POST['page']));
			}
			elseif ( $action=='add' )
			{
				$id = $this->loadModel('admin')->add($values);
				
				if ( $form->product_pict->receive() )
				{
					$newfile=$this->_product_pict_dir.$id.'.'.$this->getImageType($form->product_pict->getFileName());
					
					$data=array();
					$data['product_pict']=basename($newfile);
					$data['product_pictmime']=$this->getMime($newfile);
					
					if ( file_exists($newfile) ) {
						@unlink($newfile);
					}
					rename($form->product_pict->getFileName(),$newfile);
					
					$this->loadModel('admin')->edit($data,$id);
				}
				
				$this->redirect(array('action'=>'index'));
			}
		}
		
		$pictElement = $form->getElement('product_pict');
		$pictElement->setDestination($this->_product_pict_dir)
			->addDecorators(array(
				array('Description', array('escape'=>false,'placement'=>'prepend','tag'=>'div')),
			))
		;
		
		if ( $action == 'edit' )
		{
			if ( $val['product_pict'] )
			{
				$imgurl=$this->_helper->dimage->getDimageUrl('light-product',200,200,$val['product_id'],'jpg');
				//$imgurl=$this->_helper->url->url(array('alias'=>'product','act'=>'pict','id'=>$val['product_id']),'page');
				$pictElement->setDescription('<img src="'.$imgurl.'" />');
			}
		}
		
		return $form;
	}
	
	public function getdsdsForm()
	{
		if ( $this->_getParam('action')=='edit' )
		{
			$val=$this->loadModel('admin')->get($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		$form->setAttrib('enctype', 'multipart/form-data');
		
		// name
		$name = $form->createElement('text','product_name')
			->setRequired(true)
			->setAttribs(array('size'=>45))
			->setLabel('Name')
			->setValue($val['product_name']);
		$form->addElement($name);
		
		// pict
		$pict = $form->createElement('file','product_pict')
			->setAttribs(array('size'=>45))
			->setLabel('Picture')
			->addValidator('Extension', false, 'jpg')
			->setDestination($this->_product_pict_dir)
			->addDecorators(array(
				array('Description', array('escape'=>false,'placement'=>'prepend','tag'=>'div')),
			))
		;
		
		if ( $this->_getParam('action')=='edit' )
		{
			if ( $val['product_pict'] )
			{
				$imgurl=$this->_helper->dimage->getDimageUrl('light-product',200,200,$val['product_id'],'jpg');
				//$imgurl=$this->_helper->url->url(array('alias'=>'product','act'=>'pict','id'=>$val['product_id']),'page');
				$pict->setDescription('<img src="'.$imgurl.'" />');
			}
		}
		$form->addElement($pict);
		
		// desc
		$desc = $form->createElement('richeditor','product_desc');
		$desc->setLabel('Description')
			->setValue($val['product_desc'])
		;
		$form->addElement($desc);
		
		return $form;
	}
}