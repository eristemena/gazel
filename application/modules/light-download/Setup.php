<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightDownload_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName("download")."` (
  `download_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `download_name` varchar(255) DEFAULT NULL,
  `download_desc` text,
  `download_type` varchar(100) DEFAULT NULL,
  `download_mime` varchar(100) DEFAULT NULL,
  `download_filename` varchar(255) DEFAULT NULL,
  `download_ext` char(5) DEFAULT NULL,
  `download_filesize` bigint(20) DEFAULT NULL,
  `download_upload_at` datetime DEFAULT NULL,
  `download_count` int(11) NOT NULL,
  PRIMARY KEY (`download_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName("download")."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->getTableName("download")."`
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				return false;
			}
		}
		
		return true;
	}
}