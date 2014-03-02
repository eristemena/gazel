<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightArticle_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName("article")."` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_title` varchar(255) DEFAULT NULL,
  `article_shortdesc` tinytext,
  `article_writer` varchar(255) DEFAULT NULL,
  `article_date` datetime DEFAULT NULL,
  `article_content` text,
  PRIMARY KEY (`article_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName("article")."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->getTableName("article")."`
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