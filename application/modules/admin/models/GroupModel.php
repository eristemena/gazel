<?php

require_once "Gazel/Model.php";

class Admin_Model_Group extends Gazel_Model
{
	public function getList()
	{
		$res=$this->getDb()->fetchAssoc('select * from admingroup');
		
		return $res;
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc('select * from admingroup');
		
		$out=array();
		foreach ( $res as $r )
		{
			$out[$r['admingroup_id']]=$r['admingroup_name'];
		}
		
		return $out;
	}
	
	public function isAdmin($id)
	{
		$dbselect=$this->getDb()->select()->from('admingroup')->where('admingroup_id=?',$id)->where('admingroup_name=?','admin');
		$res=$this->getDb()->fetchAll($dbselect);
		if ( count($res)>0 ) {
			return true;
		} else {
			return false;
		}
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__admingroup,$data,"admingroup_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__admingroup,$data);
	}
	
	public function get($id)
	{
		$res=$this->getDb()->fetchRow('select * from '.$this->__admingroup.' where admingroup_id=?',$id);
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			if ( $this->isAdmin($id) ) {
				throw new Exception('You can not delete admin');
			}
			$this->getDb()->delete($this->__admingroup,'admingroup_id = '.$id);
		}
	}
	
}