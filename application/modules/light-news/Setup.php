<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightNews_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName("news")."` (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_title` varchar(100) DEFAULT NULL,
  `news_date` date NOT NULL,
  `news_shortdesc` text,
  `news_content` text,
  PRIMARY KEY (`news_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName("news")."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->getTableName("news")."`
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