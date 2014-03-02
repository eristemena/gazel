<?php

require_once "Gazel/Controller/Action.php";

class LightDownload_FrontendController extends Gazel_Controller_Action
{
	public function indexAction()
	{
		$dbselect=$this->getDb()
			->select()
			->from($this->__download)
			->order('download_id desc')
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
	
	public function downloadAction()
	{
		$id=$this->_getParam('id');
		
		$r=$this->loadModel('admin','light-download')->get($id);
		if ( $r )
		{
			$filepath=$this->_config->userdatadir.'/downloads/'.$r['download_id'].'.'.$r['download_ext'];
			//echo $filepath;exit;
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: ".$r['download_mime']);
			header("Content-Disposition: attachment; filename=".$r['download_filename'].";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$r['download_filesize']);
			//readfile("$file");
			
			
			$download_speed = 1024*100;
			$fp = @fopen($filepath, 'rb') or trigger_error("var err ='Could not read the file';",E_USER_ERROR);
			while (!feof($fp)){
			  echo fread($fp, $download_speed);
			  set_time_limit(0);
			}
			
			fclose($fp);
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
		}
		else
		{
			$this->_forward('nofile');
		}
	}
	
	public function nofileAction()
	{
		
	}
	
}