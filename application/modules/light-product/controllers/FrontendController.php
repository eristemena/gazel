<?php

require_once "Gazel/Controller/Action.php";

class LightProduct_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		$dbselect=$this->getDb()
			->select()
			->from($this->__product)
			->order('product_id desc')
		;
		
		$paginator = $this->getPaginator($dbselect);
		
		$this->view->paginator=$paginator;
		
		$this->render('shortdesc');
	}
	
	public function dimageAction()
	{
		require_once "Gazel/Filter/ImageSize.php";
		
		$productpictdir=$this->_config->userdatadir.'/products/';
		$productthumbdir=$this->_config->userdatadir.'/products/thumbs/';
		
		$r=$this->loadModel('admin','light-product')->get($this->_getParam('id'));
		$fname=$r['product_pict'];
		$mime=$r['product_pictmime'];
		$fpath=$productpictdir.$fname;
		
		$filter=new Gazel_Filter_ImageSize();
		$filter->setHeight($this->_getParam('height'));
		$filter->setWidth($this->_getParam('width'));
		$filter->setOverwriteMode('cache_older');
		$filter->setThumbnailDirectory($productthumbdir);
		$out=$filter->filter($fpath);
		
		header('Content-Type: '.$mime);
		$fh = fopen($out, 'r');
		fpassthru($fh);
		fclose($fh);
		exit;
	}
	
	public function detailAction()
	{
		$id=$this->_getParam('id');
		
		$dbselect=$this->_db->select()->from($this->__product)->where('product_id=?',$id);
		$res=$this->_db->fetchRow($dbselect);
		
		$this->view->product=$res;
	}
	
	public function listAction()
	{
		$countPerPage = ($this->_getParam('count')) ? $this->_getParam('count') : 10;
		
		$dbselect=$this->getDb()
			->select()
			->from($this->__product)
			->order('product_id desc')
		;
		
		$paginator = $this->getPaginator($dbselect,'Sliding',$countPerPage);
		
		$this->view->paginator=$paginator;
	}
}