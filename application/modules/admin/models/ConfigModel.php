<?php

require_once "Gazel/Model.php";

class Admin_Model_Config extends Gazel_Model
{
	public function update($data)
	{
		foreach ( $data as $f => $v )
		{
			if ( !$this->_db->fetchRow($this->_db->select()->from($this->__config)->where('config_name=?',$f)) )
			{
				$this->_db->insert($this->__config,array('config_name' => $f,'config_value' => $v));
			}
			else
			{
				$this->_db->update($this->__config,array('config_value' => $v),array('config_name=?' => $f));
			}
		}
	}
}