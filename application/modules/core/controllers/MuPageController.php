<?php

require_once "Gazel/Controller/Action.php";

class MuPageController extends Gazel_Controller_Action
{
	/**
	 * Default Page central
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
	 * Default Page per user
	 **/ 
	public function indexUserAction()
	{
		$dbselect=$this->getDb()
			->select()
			->from($this->_config->tableName['page'])
			->where('page_default=?','y')
		;
		
		$res=$this->_db->fetchRow($dbselect);
		if ( !$res ) {
			echo "No page created, please go to the backroom and set one!";
			exit;
		}
		$this->view->page = $res;
		
		if ( $res['page_type']=='module' )
		{
			/**
			 * Default for module type
			 **/
			$this->_request->setParam('page_type','module');
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
				$this->view->muUser=$user;
				
				/** init table name **/
				$this->_config->initTableName($user.'_');
				
				if ( $alias=='index' )
				{
					// default
					$this->_forward('index-user');
				}
				elseif ( $alias=='admin' )
				{
					$this->_forward('404'); // will trapped as an error 404, be it!!!
				}
				elseif ( $alias!='admin' )
				{
					$dbselect=$this->getDb()
						->select()
						->from($this->_config->tableName['page'])
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
		
		$this->_helper->actionStack('nav');
		
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