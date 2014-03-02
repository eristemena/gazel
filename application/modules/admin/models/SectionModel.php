<?php

require_once "Gazel/Model.php";

class Admin_Model_Section extends Gazel_Model
{
	public function edit($data,$id)
	{
		$this->getDb()->update('section',$data,"section_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert('section',$data);
	}
	
	public function get($id)
	{
		$res=$this->getDb()->fetchRow($this->getDb()->select()->from('section')->where('section_id=?',$id));
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete('section','section_id = '.$id);
		}
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from('section')->order(array('section_name asc')));
		$out=array();
		foreach ( $res as $v )
		{
			$out[$v['section_id']]=$v['section_desc'];
		}
		
		return $out;
	}

	public function getOptionsForWidget()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from('section')->order(array('section_name asc')));
		$out=array();
		foreach ( $res as $v )
		{
			$out[$v['section_name']]=$v['section_desc'];
		}
		
		return $out;
	}
}