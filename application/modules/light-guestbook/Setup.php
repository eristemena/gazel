<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightGuestbook_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName("guestbook")."` (
  `guestbook_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `guestbook_name` varchar(255) DEFAULT NULL,
  `guestbook_email` varchar(100) DEFAULT NULL,
  `guestbook_msg` text,
  `guestbook_datetime` datetime DEFAULT NULL,
  `guestbook_approve` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`guestbook_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName("guestbook")."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->__guestbook."`
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