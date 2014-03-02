<?php

require_once "Gazel/Model.php";

class Admin_Model_Page extends Gazel_Model
{
	public function getListArticle()
	{
		$sql='
		select 
			a.*,
			g.page_name
		from 
			'.$this->__page.' a
				left join page g on a.page_id=g.page_id
		';
		$res=$this->getDb()->fetchAssoc($sql);
		
		return $res;
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__page,$data,"page_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__page,$data);
	}
	
	public function get($id,$field=null)
	{
		if ( is_array($field) )
		{
			$res=$this->getDb()->fetchRow($this->getDb()->select()->from($this->__page,$field)->where('page_id=?',$id));
			return $res;
		}
		elseif ( null!==$field )
		{
			$res=$this->getDb()->fetchRow($this->getDb()->select()->from($this->__page,array($field))->where('page_id=?',$id));
			return $res[$field];
		}
		else
		{
			$res=$this->getDb()->fetchRow($this->getDb()->select()->from($this->__page)->where('page_id=?',$id));
			return $res;
		}
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__page,'page_id = '.$id);
		}
	}
	
	public function reorderBySection($sectionid)
	{
		$db=$this->getDb();
		
		$r=$db->fetchAll($db->select()->from($this->__page)->where('section_id=?',$sectionid)->order(array('section_id asc','page_order asc')));
		
		for ($i=0, $n=count( $r ); $i < $n; $i++)
		{
			if ($r[$i]['page_order'] != $i+1)
			{
				$order = $i+1;
				$db->update($this->__page,array('page_order' => $order),'page_id='.$r[$i]['page_id']);
			}
		}
	}
	
	public function reorder()
	{
		$db=$this->getDb();
		$r=$db->fetchAll($db->select()->from($this->__page,array('section_id'))->group('section_id'));
		foreach ( $r as $v )
		{
			$this->reorderBySection($v['section_id']);
		}
	}
}