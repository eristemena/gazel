<?php
require_once "Gazel/Controller/Action/Admin.php";
include_once "Zend/Acl.php";
include_once "Zend/Acl/Resource.php";
include_once "Zend/Acl/Role.php";

class Admin_IndexController extends Gazel_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->submenu=array();
	}
	
	public function logoutAction()
	{
		$this->unsetAuth();
		$this->redirect(array('action'=>'index'),'admin');
	}
	
	public function menuAction()
	{
		//for translate
		$translate= Zend_Registry::get('translate');	
		//
		
		$this->_helper->viewRenderer->setNoRender();
		
		$m=new Gazel_Menu_Xml();

		// run plugin to prepend menu
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$m = $pluginBroker->onPrependAdminMenu($m);

		$s=$m->addMenu($translate->_("System"));
		
		if ( $this->_config->multipleuser && $this->_authAdmin->auth->asUser ){
			$s->addSubMenu('Switch back to Master',$this->_helper->Url->url(array('action'=>'tomaster','controller'=>'mu','module'=>'admin'),'admin'))->end();
			$s->addSeparator();
		}
		
		if ( $this->isAllowed('admin.system.user') ) {
			$s->addSubMenu($translate->_("User Manager"),$this->_helper->Url->url(array('action'=>'index','controller'=>'user','module'=>'admin'),'admin'))->end();
		}
		if ( $this->isAllowed('admin.system.config') ) {
			$s->addSubMenu($translate->_("Global Configuration"),$this->_helper->Url->url(array('action'=>'index','controller'=>'config','module'=>'admin'),'admin'))->end();
		}
		if ( $this->isAllowed('admin.system.studio') ) {
			$s->addSubMenu($translate->_("Gazel Studio"),$this->_helper->Url->url(array('action'=>'index','controller'=>'studio','module'=>'admin'),'admin'))->end();
			$s->addSeparator();
		}
		
		$s->addSubMenu($translate->_("Logout"),$this->_helper->Url->url(array('action'=>'logout','controller'=>'index','module'=>'admin'),'admin'));
		
		if ( $this->isAllowed('admin.content') )
		{
			$s=$m->addMenu($translate->_("Content"));
			if ( $this->isAllowed('admin.content.page') ) {
				$s->addSubMenu($translate->_('Page Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'page','module'=>'admin'),'admin'))->end();
			}
			if ( $this->isAllowed('admin.content.section') ) {
				$s->addSubMenu($translate->_('Section Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'section','module'=>'admin'),'admin'))->end();
			}
		}
		
		if ( $this->isAllowed('admin.module') )
		{
			$m->addMenu($translate->_("Modules"));
			$res=$this->loadModel('module')->getAll();
			foreach ( $res as $r )
			{
				if ( $this->isAllowed('admin.module.'.$r['module_name']) ) {
					$m->addSubMenu($translate->_($r['page_title']),$this->_helper->Url->url(array('action'=>'index','controller'=>'admin','module'=>$r['module_name']),'admin'))->end();
				}
			}
		}
		
		// run plugin to add menu
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$a = $pluginBroker->getAdminSubMenu();
		if ( count($a)>0 )
		{
			$s=$m->addMenu('Plugins');
			foreach ( $a as $mm )
			{
				//echo '<pre>';print_r($mm);echo '</pre>';
				$url = $this->_helper->url->url(array(
					'action'		=> 'index',
					'controller'	=> 'plugin',
					'module'		=> 'admin',
					'action' 		=> 'setting',
					'plugin' 		=> $mm['name']
				),'admin');
				$s->addSubMenu($mm['title'], $url)->end();
			}
		}
		
		if ( $this->isAllowed('admin.widget') )
		{
			$res=$this->loadModel('widget')->getAll();
			if ( count($res)>0 )
			{
				$m->addMenu('Widgets');
				foreach ( $res as $r )
				{
					if ( $this->isAllowed('admin.widget.'.$r['widget_name']) ) {
						$m->addSubMenu($r['widget_title'],$this->_helper->Url->url(array('action'=>'admin','controller'=>'widget','module'=>'admin','widget'=>$r['widget_name']),'admin'))->end();
					}
				}
			}
		}
		
		if ( $this->isAllowed('admin.extension') ) 
		{
			$s=$m->addMenu($translate->_("Extensions"));
			if ( $this->isAllowed('admin.extension.install') ) {
				$s->addSubMenu($translate->_('Install'),$this->_helper->Url->url(array('action'=>'index','controller'=>'install','module'=>'admin'),'admin'))->end();
				$s->addSeparator();
			}
			if ( $this->isAllowed('admin.extension.module') ) {
				$s->addSubMenu($translate->_('Module Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'module','module'=>'admin'),'admin'))->end();
			}
			if ( $this->isAllowed('admin.extension.plugin') ) {
				$s->addSubMenu($translate->_('Plugin Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'plugin','module'=>'admin'),'admin'))->end();
			}
			if ( $this->isAllowed('admin.extension.theme') ) {
				$s->addSubMenu($translate->_('Theme Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'theme','module'=>'admin'),'admin'))->end();
			}
			if ( $this->isAllowed('admin.extension.router') ) {
				$s->addSubMenu($translate->_('Router Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'router','module'=>'admin'),'admin'))->end();
			}
			if ( $this->isAllowed('admin.extension.widget') ) {
				$s->addSubMenu($translate->_('Widget Manager'),$this->_helper->Url->url(array('action'=>'index','controller'=>'widget','module'=>'admin'),'admin'))->end();
			}
		}
		
		// run plugin to append menu
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();
		$m = $pluginBroker->onAppendAdminMenu($m);

		header("Content-type: text/xml");
		echo $m;
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function noaccessAction()
	{
		$this->view->submenu=array();
	}
}
