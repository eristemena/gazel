<?php

require_once "Gazel/Model.php";

class Admin_Model_Module extends Gazel_Model
{
	public function getListModule()
	{
		$dbselect=$this->_db->select()->from($this->__module);
		$res=$this->getDb()->fetchAssoc($dbselect);
		
		return $res;
	}
	
	public function getModuleAvailable()
	{
		$ignorepaths=array('core','admin'); // these are core modules, so need to be ignored
		
		$dirs=array(
			$this->_config->applicationdir.DIRECTORY_SEPARATOR.'modules'
		);
		
		$moduleinfo=array();
		foreach ( $dirs as $dir )
		{
			if ($handle = opendir($dir)) 
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
							
							if ( !$this->_config->mMaster && $file=='mu-master' ){
								continue;
							}else{
								$moduleinfo[$file]=$xml;
							}
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
		$dbselect=$this->_db
			->select()
			->from(array('m' => $this->__module),array('m.module_name'))
			->from(array('p' => $this->__page),array('p.page_title'))
			->where('m.module_name=p.page_module')
			->order('p.page_title asc')
		;
		
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
		$this->getDb()->update($this->__module,$data,"module_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__module,$data);
	}
	
	public function get($id)
	{
		if ( is_array($id) )
		{
			$dbselect=$this->_db->select()->from($this->__module);
			foreach ( $id as $i ) {
				$dbselect->where($i);
			}
			$res=$this->_db->fetchRow($dbselect);
		}
		else
		{
			$res=$this->getDb()->fetchRow('select * from module where module_id=?',$id);
		}
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__module,'module_id = '.$id);
		}
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from($this->__module)->order(array('module_title asc')));
		$out=array();
		$out['']='';
		foreach ( $res as $v )
		{
			$out[$v['module_name']]=$v['module_title'];
		}
		
		return $out;
	}
}