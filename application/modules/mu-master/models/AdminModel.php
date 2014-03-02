<?php

require_once "Gazel/Model.php";

class MuMaster_Model_Admin extends Gazel_Model
{
	public function get($id,$fld=null)
	{
		$r=$this->_db->fetchRow($this->_db->select()->from($this->__users)->where('guestbook_id=?',$id));
		return $r;
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__users,$data,array('guestbook_id=?' => $id));
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__users,$data);
		
		return $this->getDb()->lastInsertId();
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$res=$this->_db->fetchRow("select * from ".$this->__users." where user_id=?",$id);
			
			$this->_db->setFetchMode(Zend_Db::FETCH_NUM);
			$res2=$this->_db->fetchAll("show tables like '".$res['user_login']."%'");
			foreach ( $res2 as $r )
			{
				$this->_db->query("drop table ".$r[0]);
			}
			$this->_db->setFetchMode(Zend_Db::FETCH_ASSOC);
			
			$this->getDb()->delete($this->__users,'user_id = '.$id);
		}
	}
}