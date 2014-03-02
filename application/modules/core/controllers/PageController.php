<?php

require_once "Gazel/Controller/Action.php";

class PageController extends Gazel_Controller_Action
{
	/**
	 * Default Page, usually home!
	 **/ 
	public function indexAction()
	{
		if ( $_SERVER['SERVER_NAME']==$this->_config->configinstance->mu->domain || $_SERVER['SERVER_NAME']=='www.'.$this->_config->configinstance->mu->domain )
		{
			echo $_SERVER['SERVER_NAME'].' '.$this->_config->configinstance->mu->domain;
		}
		$dbselect=$this->getDb()
			->select()
			->from($this->__page)
			->where('page_default=?','y')
		;
		
		$res=$this->_db->fetchRow($dbselect);
		if ( !$res ) {
			echo "No page created yet, please go to the administrator page and set one!";
			exit;
		}
		$this->view->page = $res;
		
		if ( $res['page_type']=='module' )
		{
			/**
			 * Default for module type
			 **/
			$this->_request->setParam('page_type','module');
			$this->_request->setParam('alias',$res['page_alias']);
			$this->_forward('index','frontend',$res['page_module']);
		}
		elseif ( $res['page_type']=='static' )
		{
			/**
			 * Default for static type
			 **/
			$this->_request->setParam('page_type','static');
			$this->_request->setParam('alias',$res['page_alias']);
			$this->_forward('static');
		}
		else
		{
			echo 'Not implemented yet';
			exit;
		}
	}
	
	/**
	 * Dispatching page request, huyeaa!
	 **/
	public function dispatchAction()
	{
		$alias=$this->_getParam('alias');
		$act = $this->_getParam('act');
		
		if ( $this->_config->multipleuser && $this->_getParam('username')=='template' )
		{
			$this->_forward('error404');
		}
		else
		{
			if ( $alias=='admin' )
			{
				$this->_forward($alias); // will trapped as an error 404, be it!!!
			}
			else
			{
				if ( $alias=='default' ) 
				{
					$dbselect=$this->getDb()
						->select()
						->from($this->__page)
						->where('page_default=?','y')
					;
				}
				else
				{
					$dbselect=$this->getDb()
						->select()
						->from($this->__page)
						->where('page_alias=?',$alias)
					;
				}
				$res=$this->_db->fetchRow($dbselect);
				
				if ( !$res ) 
				{
					// check if it's a module that is not assign to page yet
					if ( $res=$this->_db->fetchRow('select * from module where module_name=?',$alias) )
					{
						$this->_forward('index','frontend',$alias);
					}
					else
					{
						$this->_forward('error404'); // will trapped as an error 404, be it!!!
					}
				}
				else
				{
					if ( $res['page_type']=='module' )
					{
						/**
						 * Module page
						 **/
						$this->view->page = $res;
						$this->_request->setParam('page_type','module');
						$this->_forward($act,'frontend',$res['page_module']);
					}
					else
					{
						/**
						 * Static page
						 **/
						$this->view->page = $res;
						$this->_request->setParam('page_type','static');
						$this->_forward('static');
					}
				}
			}
		}
		
		$this->_helper->viewRenderer->setNoRender();
	}
	
	/**
	 * Static page action
	 **/
	public function staticAction()
	{
		$alias=$this->_getParam('alias');
		
		$dbselect=$this->getDb()
			->select()
			->from($this->__page)
			->where('page_alias=?',$alias)
		;
		
		$res=$this->_db->fetchRow($dbselect);
		$this->view->content = $res['page_content'];
	}
	
	public function searchAction()
	{
		$query=$this->_getParam('query');
		if ( !$query )
		{
			$query=$this->_getParam('keyword');
		}
		
		$dbselect=$this->getDb()
			->select()
			->from($this->__page,array('page_title','page_alias','page_type','page_content'))
			->where("page_published='y'")
			->where("page_content like ?",new Zend_Db_Expr("'%$query%'"))
		;
		
		$res=$this->_db->fetchAssoc($dbselect);
		$this->view->result=$res;
		$this->view->searchkeyword=$query;
	}
}
