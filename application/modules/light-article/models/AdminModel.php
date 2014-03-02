<?php

require_once "Gazel/Model.php";

class LightArticle_Model_Admin extends Gazel_Model
{
	public function getListNews()
	{
		return $this->getDb()->fetchAssoc('select * from '.$this->__article.' order by article_id desc');
	}
	
	public function get($id)
	{
		return $this->getDb()->fetchRow('select * from '.$this->__article.' where article_id=?',$id);
	}
	
	public function edit($data,$id)
	{
		$data['article_date']=new Zend_Db_Expr('now()');
		
		$this->getDb()->update($this->__article,$data,"article_id='$id'");
	}
	
	public function add($data)
	{	
		$data['article_date']=new Zend_Db_Expr('now()');
		
		$this->getDb()->insert($this->__article,$data);
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__article,'article_id = '.$id);
		}
	}
}