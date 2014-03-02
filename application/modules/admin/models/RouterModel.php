<?php

require_once "Gazel/Model.php";

class Admin_Model_Router extends Gazel_Model
{
	public function getListRouter()
	{
		$dbselect=$this->_db->select()->from($this->__router);
		$res=$this->getDb()->fetchAssoc($dbselect);
		
		return $res;
	}
	
	public function getRouterAvailable()
	{
		$ignorepaths=array('page','admin','search','dimage'); // these are core routers, so need to be ignored
		
		$dirs=array(
			$this->_config->applicationdir.DIRECTORY_SEPARATOR.'routers'
		);
		
		$routerinfo=array();
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
								$routerinfo[$file]=$xml;
							}
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return $routerinfo;
	}
	
	public function getAll($asDbSelect=false)
	{
		$dbselect=$this->_db
			->select()
			->from(array('m' => $this->__router),array('m.router_name'))
			->from(array('p' => $this->__page),array('p.page_title'))
			->where('m.router_name=p.page_router')
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
		$this->getDb()->update($this->__router,$data,"router_id='$id'");
	}
	
	public function add($data)
	{
		$this->getDb()->insert($this->__router,$data);
	}
	
	public function get($id)
	{
		if ( is_array($id) )
		{
			$dbselect=$this->_db->select()->from($this->__router);
			foreach ( $id as $i ) {
				$dbselect->where($i);
			}
			$res=$this->_db->fetchRow($dbselect);
		}
		else
		{
			$res=$this->getDb()->fetchRow('select * from router where router_id=?',$id);
		}
		
		return $res;
	}
	
	public function delete($ids)
	{
		foreach ( $ids as $id )
		{
			$this->getDb()->delete($this->__router,'router_id = '.$id);
		}
	}
	
	public function getOptions()
	{
		$res=$this->getDb()->fetchAssoc($this->getDb()->select()->from($this->__router)->order(array('router_title asc')));
		$out=array();
		$out['']='';
		foreach ( $res as $v )
		{
			$out[$v['router_name']]=$v['router_title'];
		}
		
		return $out;
	}
}