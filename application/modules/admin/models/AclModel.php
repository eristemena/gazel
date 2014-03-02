<?php

require_once "Gazel/Model.php";

class Admin_Model_Acl extends Gazel_Model
{
	public function getAclResources()
	{
		$acl=array();
		$acl['admin']	= array('Admin',null);
		$acl['admin.system']	= array('System','admin');
		$acl['admin.system.user']	= array('User Manager','admin.system');
		$acl['admin.system.config']	= array('Global Configuration','admin.system');
		$acl['admin.system.studio']	= array('Gazel Studio','admin.system');
		$acl['admin.content']	= array('Content','admin');
		$acl['admin.content.page']	= array('Page Manager','admin.content');
		$acl['admin.content.section']	= array('Section Manager','admin.content');
		$acl['admin.module'] = array('Modules','admin');
		
		$res=$this->_db->fetchAll('select m.module_name,p.page_title from '.$this->__module.' m,'.$this->__page.' p where m.module_name=p.page_module order by p.page_title asc');
		foreach ( $res as $r )
		{
			$acl['admin.module.'.$r['module_name']]=array($r['page_title'],'admin.module');
		}
		
		$acl['admin.widget'] = array('Widgets','admin');
		$res=$this->_db->fetchAll('select * from '.$this->__widget.' order by widget_title asc');
		foreach ( $res as $r )
		{
			$acl['admin.widget.'.$r['widget_name']]=array($r['widget_title'],'admin.widget');
		}
		
		$acl['admin.extension'] = array('Extensions','admin');
		$acl['admin.extension.install'] = array('Install','admin.extension');
		$acl['admin.extension.module'] = array('Module Manager','admin.extension');
		$acl['admin.extension.plugin'] = array('Plugin Manager','admin.extension');
		$acl['admin.extension.theme'] = array('Theme Manager','admin.extension');
		$acl['admin.extension.router'] = array('Router Manager','admin.extension');
		$acl['admin.extension.widget'] = array('Widget Manager','admin.extension');
		
		return $acl;
	}
	
	public function constructAcl($admingroupid)
	{
		require_once "Zend/Acl.php";
		require_once "Zend/Acl/Role.php";
		require_once "Zend/Acl/Resource.php";
		
		$acl = new Zend_Acl();
		$acl->addRole(new Zend_Acl_Role('role'));
		
		// resources
		$resources = $this->getAclResources();
		
		// construct ACL resources
		foreach ( $resources as $res => $inf )
		{
			$acl->addResource(new Zend_Acl_Resource($res),$inf[1]);
		}
		
		$res = $this->_db->fetchRow('select admingroup_acl from '.$this->__admingroup.' where admingroup_id=?',$admingroupid);
		if ( $res['admingroup_acl']=='')
		{
			$acl->allow('role');
		}
		else 
		{
			$admingroup_acl=unserialize($res['admingroup_acl']);
			
			if ( !is_array($admingroup_acl) ) 
			{
				$admingroup_acl=array();
			}
			
			foreach ( $resources as $res => $inf )
			{
				if ( in_array($res,$admingroup_acl) )
				{
					$acl->allow('role',$res);
					$acl->allow('role',$inf[1]);
				}
				else
				{
					$acl->deny('role',$res);
				}
			}
		}
		
		if ( $this->_config->multipleuser && !$this->_config->mMaster )
		{
			$acl->deny('role','admin.extension.install');
			$acl->deny('role','admin.system.user');
		}
		
		return $acl;
	}
}