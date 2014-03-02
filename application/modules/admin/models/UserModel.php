<?php

require_once "Gazel/Model.php";

class Admin_Model_User extends Gazel_Model
{
	public function getAll($asDbSelect=false)
	{
		$dbselect=$this->getDb()
			->select()
			->from(array('a'=> $this->__admin))
			->joinLeft(array('g'=> $this->__admingroup),'a.admingroup_id=g.admingroup_id')
			->order('a.admin_id desc');
		
		if ( $asDbSelect ) {
			return $dbselect;
		} else {
			$res=$this->getDb()->fetchAssoc($sql);
			return $res;
		}
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__admin,$data,"admin_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__admin,$data);
	}
	
	public function get($id)
	{
		$res=$this->getDb()->fetchRow('select * from '.$this->__admin.' where admin_id=?',$id);
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__admin,'admin_id = '.$id);
		}
	}
}