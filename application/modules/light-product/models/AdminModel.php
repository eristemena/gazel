<?php

require_once "Gazel/Model.php";

class LightProduct_Model_Admin extends Gazel_Model
{
	public function get($id,$fld=null)
	{
		$dbselect=$this->getDb()->select()->from($this->__product)->where('product_id=?',$id);
		$r=$this->getDb()->fetchRow($dbselect);
		
		if ( $fld!=null ){
			return $r[$fld];
		} else {
			return $r;
		}
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__product,$data,"product_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__product,$data);
		
		return $this->getDb()->lastInsertId();
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__product,'product_id = '.$id);
		}
	}
}