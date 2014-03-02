<?php

require_once "Gazel/Model.php";

class Admin_Model_Theme extends Gazel_Model
{
	public function getThemeAvailable()
	{
		$dirs=array(
			$this->_config->themespath
		);
		
		$moduleinfo=array();
		foreach ( $dirs as $dir )
		{
			if ($handle = @opendir($dir)) 
			{
				while (false !== ($file = readdir($handle))) 
				{
					$mdir=$dir.DIRECTORY_SEPARATOR.$file;
					$mxml=$mdir.DIRECTORY_SEPARATOR.$file.'.xml';
					if ( is_dir($mdir) && $file!='.' && $file!='..' ) 
					{
						if ( file_exists($mxml) )
						{
							$xml = simplexml_load_file($mxml);
							if ( $this->_config->multipleuser && !$this->_config->mMaster ){
								if ( $xml->mu->masterOnly == "true" ){
									continue;
								}
							}elseif ( $xml->forAdmin == "true" ){
								continue;
							}
							$xml->themepath=realpath($mdir);
							$moduleinfo[$file]=$xml;
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return $moduleinfo;
	}
	
	public function getThemeAdminAvailable()
	{
		$dirs=array(
			$this->_config->themespath
		);
		
		$moduleinfo=array();
		foreach ( $dirs as $dir )
		{
			if ($handle = @opendir($dir)) 
			{
				while (false !== ($file = readdir($handle))) 
				{
					$mdir=$dir.DIRECTORY_SEPARATOR.$file;
					$mxml=$mdir.DIRECTORY_SEPARATOR.$file.'.xml';
					if ( is_dir($mdir) && $file!='.' && $file!='..' ) 
					{
						if ( file_exists($mxml) )
						{
							$xml = simplexml_load_file($mxml);
							if ( $this->_config->multipleuser && !$this->_config->mMaster ){
								if ( $xml->mu->masterOnly == "true" ){
									continue;
								}
							}elseif ( $xml->forAdmin != "true" ){
								continue;
							}
							$xml->themepath=realpath($mdir);
							$moduleinfo[$file]=$xml;
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return $moduleinfo;
	}
}