<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightEvent_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName('event')."` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_title` varchar(100) DEFAULT NULL,
  `event_date_from` date NOT NULL,
  `event_date_to` date NOT NULL,
  `event_location` varchar(50) DEFAULT NULL,
  `event_shortdesc` text,
  `event_content` text,
  PRIMARY KEY (`event_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName('event')."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->getTableName('event')."`
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