<?php

require_once "Gazel/Model.php";

class Admin_Model_Widget extends Gazel_Model
{
	public function getListWidget()
	{
		$res=$this->getDb()->fetchAssoc('select * from widget');
		
		return $res;
	}
	
	public function getWidgetAvailable()
	{
		$ignorepaths=array(); // these are core widgets, so need to be ignored
		
		$dirs=array(
			$this->_config->applicationdir.DIRECTORY_SEPARATOR.'widgets'
		);
		
		$moduleinfo=array();
		foreach ( $dirs as $dir )
		{
			if ($handle = @opendir($dir)) 
			{
				while (false !== ($file = readdir($handle)))
				{
					$mdir=$dir.DIRECTORY_SEPARATOR.$file;
					$mxml=$mdir.DIRECTORY_SEPARATOR.$file.'.xml';
					if ( is_dir($mdir) && $file!='.' && $file!='..' && !in_array($file,$ignorepaths) ) 
					{
						if ( file_exists($mxml) )
						{
							$xml = simplexml_load_file($mxml);
							$moduleinfo[$file]=$xml;
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return $moduleinfo;
	}
	
	public function getAll($asDbSelect=false)
	{
		$dbselect=$this->_db->select()->from($this->__widget);
		
		if ( $asDbSelect )
		{
			return $dbselect;
		}
		else
		{
			return $this->_db->fetchAll($dbselect);
		}
	}
	
	public function edit($data,$id)
	{
		$this->getDb()->update($this->__widget,$data,"widget_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__widget,$data);
	}
	
	public function get($id)
	{
		if ( is_array($id) )
		{
			$dbselect=$this->_db->select()->from($this->__widget);
			foreach ( $id as $i ) {
				$dbselect->where($i);
			}
			$res=$this->_db->fetchRow($dbselect);
		}
		else
		{
			$res=$this->getDb()->fetchRow('select * from widget where widget_id=?',$id);
		}
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__widget,'widget_id = '.$id);
		}
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from($this->__widget)->order(array('widget_title asc')));
		$out=array();
		$out['']='';
		foreach ( $res as $v )
		{
			$out[$v['widget_name']]=$v['widget_title'];
		}
		
		return $out;
	}
}