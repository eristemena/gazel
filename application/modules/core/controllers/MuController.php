<?php

require_once "Gazel/Controller/Action.php";

class MuController extends Gazel_Controller_Action
{
	/**
	 * Default Page, usually home!
	 **/ 
	public function indexAction()
	{
		require_once "Zend/Controller/Request/Http.php";
		$request = new Zend_Controller_Request_Http();
		$this->_config->baseurl=$request->getScheme().'://'.$request->getHttpHost();
		
		$this->view->addScriptPath(realpath($this->_config->publicdir.'/muthemes/basic'));
		
		$this->renderScript('content_home.html');
	}
	
	/**
	 * Dispatching page request, huyeaa!
	 **/
	public function dispatchAction()
	{
		$alias=$this->_getParam('alias');
		$act = $this->_getParam('act');
		$user = $this->_getParam('user');
		
		if ( $user=='www' )
		{
			$this->_forward('index');
		}
		else
		{
			// check user existence
			if ( !$res=$this->_db->fetchRow($this->_db->select()->from('user')->where('user_id=?',$user)) )
			{
				$this->_forward('404');
			}
			else
			{
				if ( $alias=='index' )
				{
					// default
					$this->_forward('index','mu-page');
				}
				elseif ( $alias=='admin' )
				{
					$this->_forward($alias); // will trapped as an error 404, be it!!!
				}
				elseif ( $alias!='admin' )
				{
					$dbselect=$this->getDb()
						->select()
						->from($user.'_page')
						->where('page_alias=?',$alias)
					;
					$res=$this->_db->fetchRow($dbselect);
					
					if ( !$res['page_type'] ) 
					{
						$this->_forward($alias); // will trapped as an error 404, be it!!!
					}
					else
					{
						if ( $res['page_type']=='module' )
						{
							/**
							 * Module page
							 **/
							$this->_request->setParam('page_type','module');
							$this->_forward($act,'frontend',$res['page_module']);
						}
						else
						{
							/**
							 * Static page
							 **/
							$this->_request->setParam('page_type','static');
							$this->_forward('static');
						}
					}
				}
			}
		}
		
		$this->_helper->layout->disableLayout();
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
			->from('page')
			->where('page_alias=?',$alias)
		;
		
		$this->_helper->actionStack('nav','page','core');
		
		$res=$this->_db->fetchRow($dbselect);
		$this->view->page = $res;
	}
	
	public function navAction()
	{
		$dbselect=$this->getDb()
			->select()
			->from('page',array('page_alias','page_title'))
			->where("page_published='y'")
			->order('page_order asc')
		;
		$res=$this->_db->fetchAssoc($dbselect);
		
		$this->view->listpage=$res;
		$this->render('nav','nav');
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
			->from('page',array('page_title','page_alias','page_type','page_content'))
			->where("page_published='y'")
			->where("page_content like ?",new Zend_Db_Expr("'%$query%'"))
		;
		
		$res=$this->_db->fetchAssoc($dbselect);
		$this->view->result=$res;
		$this->view->searchkeyword=$query;
	}
}