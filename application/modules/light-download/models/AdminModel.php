<?php

require_once "Gazel/Model.php";

class LightDownload_Model_Admin extends Gazel_Model
{
	public function get($id,$fld=null)
	{
		$dbselect=$this->getDb()->select()->from($this->__download)->where('download_id=?',$id);
		$r=$this->getDb()->fetchRow($dbselect);
		
		if ( $fld!=null ){
			return $r[$fld];
		} else {
			return $r;
		}
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__download,$data,"download_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__download,$data);
		
		return $this->getDb()->lastInsertId();
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__download,'download_id = '.$id);
		}
	}
	
	public function size($size)
	{
		if($size!=0 && $size!=''){
			$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
			return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
		}else{
			return 0;
		}
	}
}