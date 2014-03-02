<?php

require_once "Gazel/Model.php";

class LightNews_Model_Admin extends Gazel_Model
{
	public function getListNews()
	{
		return $this->getDb()->fetchAssoc('select * from news order by news_id desc');
	}
	
	public function get($id)
	{
		return $this->getDb()->fetchRow('select * from news where news_id=?',$id);
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update('news',$data,"news_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert('news',$data);
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete('news','news_id = '.$id);
		}
	}
}