<?php

require_once "Gazel/Model.php";

class Admin_Model_Menutype extends Gazel_Model
{
	public function edit($data,$id)
	{
		$this->getDb()->update('menutype',$data,"menutype_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert('menutype',$data);
	}
	
	public function get($id)
	{
		$res=$this->getDb()->fetchRow('select * from menutype where menutype_id=?',$id);
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete('menutype','menutype_id = '.$id);
		}
	}
}