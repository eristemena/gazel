<?php

require_once "Gazel/Model.php";

class LightGuestbook_Model_Guestbook extends Gazel_Model
{
	public function get($id,$fld=null)
	{
		$r=$this->_db->fetchRow($this->_db->select()->from($this->__guestbook)->where('guestbook_id=?',$id));
		return $r;
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__guestbook,$data,array('guestbook_id=?' => $id));
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__guestbook,$data);
		
		return $this->getDb()->lastInsertId();
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__guestbook,'member_id = '.$id);
		}
	}
}