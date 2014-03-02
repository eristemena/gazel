<?php

require_once "Gazel/Module/Setup/Abstract.php";

class LightProduct_Setup extends Gazel_Module_Setup_Abstract
{
	public function install()
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$this->getTableName("product")."` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) DEFAULT NULL,
  `product_desc` text,
  `product_pict` varchar(255) DEFAULT NULL,
  `product_pictmime` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
)
		";
		
		foreach ( $sql as $q )
		{
			try {
				$this->_db->query($q);
			} catch(Exception $e) {
				$this->setErrors(array("Unable to create table '".$this->getTableName("product")."'"));
				return false;
			}
		}
		
		return true;
	}
	
	public function uninstall()
	{
		$sql=array();
		
		$sql[]="
DROP TABLE IF EXISTS `".$this->getTableName("product")."`
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