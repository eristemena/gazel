<?php

require_once "Gazel/Controller/Action.php";

class ThemeassetsController extends Gazel_Controller_Action
{
	public function dispatchAction()
	{
		$folder=$this->_getParam(1); // css or js or images
		$path=$this->_getParam(2);
		
		// ambil informasi directory dari layout
		$dirs=$this->_helper->layout->getView()->getScriptPaths();
		
		foreach ( $dirs as $dir )
		{
			if ( is_readable($dir.'/'.$folder.'/'.$path) )
			{
				$file=$dir.'/'.$folder.'/'.$path;
				break;
			}
		}
		
		$_e=explode('.',basename($path));
		$ext=$_e[count($_e)-1];
		if ( file_exists($file) )
		{
			/*
			require_once "Zend/Date.php";
			$d=new Zend_Date('01-01-2012','dd-MM-YYYY');
			$expdate=$d->get(Zend_Date::RFC_1123);
			
			header("Expires: $expdate GMT");
			header("Cache-Control: max-age=3600, must-revalidate");
			*/
			if ( $ext=='css' || $ext=='js' || $ext=='html' || $ext=='htm' || $ext=='xml' )
			{
				if ( $ext=='js' ){
					header("Content-type: text/javascript");
				} elseif ( $ext=='css' ) {
					header("Content-type: text/css");
				} elseif ( $ext=='html' || $ext=='htm' ) {
					header("Content-type: text/html");
				} elseif ( $ext=='xml' ) {
					header("Content-type: text/xml");
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
			elseif ( $ext=='swf' )
			{
				header("Content-type: application/x-shockwave-flash");
				
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