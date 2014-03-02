<?php

require_once "Gazel/Controller/Action.php";

class AssetsController extends Gazel_Controller_Action
{
	public function dispatchAction()
	{
		$folder=$this->_getParam(1); // css or js or images
		$path=$this->_getParam(2);
		$file=$this->_config->gazeldir.'/assets/'.$folder.'/'.$path;
		
		$_e=explode('.',basename($path));
		$ext=$_e[count($_e)-1];
		if ( file_exists($file) )
		{
			header("Cache-control: public");
			header("Expires: Mon, 26 Jul 2013 05:00:00 GMT"); 
			if ( $ext=='css' || $ext=='js' || $ext=='htm' || $ext=='html' )
			{
				if ( $ext=='js' ){
					header("Content-type: text/javascript");
				} elseif ( $ext=='css' ) {
					header("Content-type: text/css");
				} elseif ( $ext=='htm' || $ext=='html' ) {
					header("Content-type: text/html");
				}
				
				$handle=fopen($file,'r');
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);
						echo $buffer;
					}
					fclose($handle);
				}
				exit;
			}
			elseif ( in_array($ext,array('png','jpg','gif')) )
			{
				header("Content-type: image/$ext");
				
				$handle=fopen($file,'r');
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);
						echo $buffer;
					}
					fclose($handle);
				}
				exit;
			}
		}
		else
		{
			$this->_forward('error404');
		}
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
}