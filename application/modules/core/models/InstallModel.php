<?php

require_once "Gazel/Model.php";

class Core_Model_Install extends Gazel_Model
{
	public $prefix='';
	
	public function installDb($prefix='')
	{
		$sql=array();
		
		$sql[]="
CREATE TABLE `".$prefix."admin` (
  `admin_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `admingroup_id` tinyint(4) NOT NULL,
  `admin_username` varchar(15) DEFAULT NULL,
  `admin_email` varchar(40) DEFAULT NULL,
  `admin_password` varchar(20) DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `idx_admingroup_id` (`admingroup_id`)
)
		";
		
		$sql[]="
insert  into `".$prefix."admin`(`admingroup_id`,`admin_username`,`admin_email`,`admin_password`,`admin_name`) values (1,'admin','root@localhost','admin','Admin');
		";
		
		$sql[]="
CREATE TABLE `".$prefix."admingroup` (
  `admingroup_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `admingroup_name` varchar(10) DEFAULT NULL,
  `admingroup_desc` varchar(100) DEFAULT NULL,
  `admingroup_acl` text NOT NULL,
  PRIMARY KEY (`admingroup_id`),
  KEY `idx_admingroup_name` (`admingroup_name`)
)
		";
		
		$sql[]="
insert  into `".$prefix."admingroup`(`admingroup_id`,`admingroup_name`,`admingroup_desc`,`admingroup_acl`) values (1,'admin','Administrator','')
		";
		
		$sql[]="
CREATE TABLE `".$prefix."config` (
  `config_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(60) NOT NULL,
  `config_value` longtext NOT NULL,
  PRIMARY KEY (`config_id`)
)
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('sitename','My Website')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('themename','cleansite')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('logo_width','')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('logo_height','')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('uselogo','n')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('logo','')
		";
		
		$sql[]="
insert  into `".$prefix."config`(`config_name`,`config_value`) values ('uselanguage','en')
		";
		
		$sql[]="
CREATE TABLE `".$prefix."module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100),
  `module_title` varchar(100),
  `module_installed` datetime,
  PRIMARY KEY (`module_id`)
);
		";
		
		$sql[]="
CREATE TABLE `".$prefix."page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_type` enum('static','module') NOT NULL DEFAULT 'static',
  `section_id` int(11),
  `page_order` float,
  `page_title` varchar(100),
  `page_alias` varchar(40),
  `page_content` longtext,
  `page_module` varchar(40),
  `page_crtdon` datetime,
  `page_edtdon` datetime,
  `page_admin_crtdby` tinyint(3),
  `page_admin_edtdby` tinyint(3),
  `page_default` enum('y','n') NOT NULL DEFAULT 'n',
  `page_published` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`page_id`),
  KEY `idx_page_alias` (`page_alias`)
)
		";
		
		$sql[]="
insert  into `".$prefix."page`(`page_type`,`section_id`,`page_order`,`page_title`,`page_alias`,`page_content`,`page_module`,`page_crtdon`,`page_edtdon`,`page_admin_crtdby`,`page_admin_edtdby`,`page_default`,`page_published`) values ('static',1,1,'Home','home','','','2009-10-04 13:04:08','2010-03-07 13:14:43',1,1,'y','y');
		";
		
		$sql[]="
CREATE TABLE `".$prefix."section` (
  `section_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_name` varchar(40),
  `section_desc` varchar(100),
  PRIMARY KEY (`section_id`),
  KEY `idx_section_name` (`section_name`)
)
		";
		
		$sql[]="
insert  into `".$prefix."section`(`section_id`,`section_name`,`section_desc`) values (1,'main','Main section')
		";
		
		$sql[]="
insert  into `".$prefix."section`(`section_id`,`section_name`,`section_desc`) values (3,'left','Left Section')
		";
		
		$sql[]="
insert  into `".$prefix."section`(`section_id`,`section_name`,`section_desc`) values (4,'footer','Footer')
		";
		
		$sql[]="
insert  into `".$prefix."section`(`section_id`,`section_name`,`section_desc`) values (5,'right','Right Section')
		";
		
		$sql[]="
CREATE TABLE `".$prefix."widget` (
  `widget_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `widget_pos` varchar(255),
  `widget_name` varchar(100) DEFAULT NULL,
  `widget_title` varchar(255) DEFAULT NULL,
  `widget_content` text,
  `widget_data` longtext,
  `widget_active` enum('y','n') NOT NULL DEFAULT 'y',
  `widget_installed` datetime DEFAULT NULL,
  `widget_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`widget_id`)
)
		";
		
		$sql[]="
CREATE TABLE `".$prefix."plugin` (                                  
	`plugin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
	`plugin_name` varchar(100) DEFAULT NULL,               
	`plugin_title` varchar(100) DEFAULT NULL,              
	`plugin_installed` datetime,
	`plugin_options` text,                  
	PRIMARY KEY (`plugin_id`)                              
)
		";
		
		$sql[]="
CREATE TABLE `".$prefix."router` (                                  
	`router_id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
	`router_name` varchar(100) DEFAULT NULL,               
	`router_title` varchar(100) DEFAULT NULL,              
	`router_installed` datetime,                  
	PRIMARY KEY (`router_id`)                              
)
		";
		
		try {
			foreach ( $sql as $q ) {
				$this->_db->query($q);
			}
		} catch (Exception $e) {
			return false;
		}
		
		return true;
	}
	
	public function copyDateFromTemplate($prefix='')
	{
		$sql=array();
		
		// config
		$sql[]="truncate table ".$prefix."config";
		$sql[]="insert into ".$prefix."config select * from template_config";
		
		// module
		$sql[]="truncate table ".$prefix."module";
		$sql[]="insert into ".$prefix."module select * from template_module";
		
		// page
		$sql[]="truncate table ".$prefix."page";
		$sql[]="insert into ".$prefix."page select * from template_page";
		
		try {
			foreach ( $sql as $q ) {
				$this->_db->query($q);
			}
		} catch (Exception $e) {
			return false;
		}
		
		return true;
	}
}
