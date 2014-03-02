<?php

require_once "Gazel/Model.php";

class Admin_Model_Menu extends Gazel_Model
{
	public function edit($data,$id)
	{
		$this->getDb()->update('menu',$data,"menu_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert('menu',$data);
	}
	
	public function get($id)
	{
		$res=$this->getDb()->fetchRow('select * from menu where menu_id=?',$id);
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete('menu','menu_id = '.$id);
		}
	}
}