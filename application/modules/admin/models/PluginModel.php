<?php

require_once "Gazel/Model.php";

class Admin_Model_Plugin extends Gazel_Model
{
	public function getListPlugin()
	{
		$dbselect=$this->_db->select()->from($this->__plugin);
		$res=$this->getDb()->fetchAssoc($dbselect);
		
		return $res;
	}
	
	public function getPluginAvailable()
	{
		$ignorepaths=array(); // these are core plugins, so need to be ignored
		
		$dirs=array(
			$this->_config->applicationdir.DIRECTORY_SEPARATOR.'plugins'
		);
		
		$plugininfo=array();
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
								$plugininfo[$file]=$xml;
							}
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return $plugininfo;
	}
	
	public function getAll($asDbSelect=false)
	{
		$dbselect=$this->_db
			->select()
			->from(array('m' => $this->__plugin),array('m.plugin_name'))
			->from(array('p' => $this->__page),array('p.page_title'))
			->where('m.plugin_name=p.page_plugin')
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
		$this->getDb()->update($this->__plugin,$data,"plugin_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__plugin,$data);
	}
	
	public function get($id)
	{
		if ( is_array($id) )
		{
			$dbselect=$this->_db->select()->from($this->__plugin);
			foreach ( $id as $i ) {
				$dbselect->where($i);
			}
			$res=$this->_db->fetchRow($dbselect);
		}
		else
		{
			$res=$this->getDb()->fetchRow('select * from plugin where plugin_id=?',$id);
		}
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__plugin,'plugin_id = '.$id);
		}
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from($this->__plugin)->order(array('plugin_title asc')));
		$out=array();
		$out['']='';
		foreach ( $res as $v )
		{
			$out[$v['plugin_name']]=$v['plugin_title'];
		}
		
		return $out;
	}
}