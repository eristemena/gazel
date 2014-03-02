<?php

/**
 * @see Zend_Db_Adapter_Pdo_Mysql
 */
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

/**
 * @see Zend_Db_Profiler_Firebug
 */
require_once 'Zend/Db/Profiler/Firebug.php';

/**
 * @category   Gazel
 * @package    Gazel_Db
 * @copyright  Copyright (c) 2000-2011 PT Inti Artistika Solusitama (http://www.inarts.co.id)
 */
class Gazel_Db
{
	// Hold an instance of the class
	private static $instance;
	private $db;

	// A private constructor; prevents direct creation of object
	private function __construct()
	{}

	// The singleton method
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	public function setConnection($dbparams)
	{
		try {
			if ( $dbparams instanceof Zend_Config )
			{
				$this->db = Zend_Db::factory($dbparams->database);
			}
			else
			{
				$this->db = Zend_Db::factory('Pdo_Mysql', $dbparams);
			}
			
			$this->db->getConnection();
			
			require_once "Gazel/Config.php";
			$config = Gazel_Config::getInstance();
			if ( $config->debug )
			{
				$this->db->getProfiler()->setEnabled(true);
			}
		} catch (Zend_Db_Adapter_Exception $e) {
			// perhaps a failed login credential, or perhaps the RDBMS is not running
			die($e->getMessage());
		} catch (Zend_Exception $e) {
			// perhaps factory() failed to load the specified Adapter class
			die($e->getMessage());
		}
	}

	public function getDb()
	{
		return $this->db;
	}

	// Prevent users to clone the instance
	public function __clone()
	{}
}