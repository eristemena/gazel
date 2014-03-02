<?php

require_once "Gazel/Model.php";

class LightEvent_Model_Admin extends Gazel_Model
{
	public function get($id)
	{
		return $this->getDb()->fetchRow('select * from '.$this->__event.' where event_id=?',$id);
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__event,$data,"event_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__event,$data);
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__event,"event_id = '$id'");
		}
	}
}