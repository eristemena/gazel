<?php

class Zipper extends ZipArchive {
	public $basepath;
	public $firstdir='';
	
	public function addDir($path) {
		$bp=substr(realpath($path),strlen(realpath($this->basepath)));
	    //$this->addEmptyDir($bp);
	    $nodes = glob($path . '/*'); 
	    foreach ($nodes as $node) { 
	        if (is_dir($node)) {
	          $this->addDir($node);
	        } else if (is_file($node))  { 
	        	
	        	if ( strlen($bp)>0 ){
	        		$p=$this->firstdir.substr($bp,1).DIRECTORY_SEPARATOR.basename($node);
	        	} else {
	        		$p=$this->firstdir.basename($node);
	        	}
	        	
	        	//echo realpath($this->basepath)." = ".realpath($path)." = ".$p." <br/>";
	          $this->addFile($node,$p); 
	        }
	    }
	}
}

class Gazel_Controller_Action_Helper_Zip extends Zend_Controller_Action_Helper_Abstract
{
	public function zip($filename,$dir,$firstdir='')
	{
		$zip = new Zipper();
		$zip->basepath=$dir;
		$zip->firstdir=$firstdir;
		if ($zip->open($filename, ZIPARCHIVE::CREATE)===TRUE){
			$zip->addDir($dir);
			$zip->close();
		}
	}
}