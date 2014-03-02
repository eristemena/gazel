<?php

require_once "Zend/Controller/Action.php";
require_once "Zend/Config/Xml.php";

class ConfigController extends Zend_Controller_Action
{
	private $_config=null;
	
	public function init()
	{
		require_once "Gazel/Config.php";
		$this->_config=Gazel_Config::getInstance();
		
		$this->view->addHelperPath('Gazel/View/Helper', 'Gazel_View_Helper');
		Zend_Controller_Action_HelperBroker::addPrefix('Gazel_Controller_Action_Helper');
		
		$this->_helper->viewRenderer->setViewSuffix('html');
		$this->_helper->layout->setViewSuffix('html');
		
		$this->_helper->layout->setLayout('config');
	}
	
	public function indexAction()
	{
		if ( $this->_config->isInstalled() )
		{
			$this->_helper->Redirector->gotoUrlAndExit($this->_config->baseurl);
		}
		
		$ok=true;
		
		if ( is_writeable($this->_config->configdir) )
		{
			$this->view->configdir_ok	= true;
		}
		else
		{
			$ok=false;
		}
		
		if ( !$ok )
		{
			$this->view->ok	= false;
		}
		else
		{
			$this->view->ok = true;
		}
		
		$this->render('config1');
	}
	
	public function index2Action()
	{
		if ( $this->_config->isInstalled() )
		{
			$this->_helper->Redirector->gotoUrlAndExit($this->_config->baseurl);
		}
		
		require_once "Gazel/Config.php";
		$config=Gazel_Config::getInstance();
		
		$request = $this->getRequest();
		
		$adminpath=($request->getPost('adminpath')) ? $request->getPost('adminpath') : 'admin';
		$db_host=($request->getPost('db_host')) ? $request->getPost('db_host') : 'localhost';
		$db_username=($request->getPost('db_username')) ? $request->getPost('db_username') : '';
		$db_password=($request->getPost('db_password')) ? $request->getPost('db_password') : '';
		$db_dbname=($request->getPost('db_dbname')) ? $request->getPost('db_dbname') : '';
		
		if ( $request->isPost() )
		{
			if ( $_POST['act']=='checkconnection' )
			{
				$success=true;
				if ( !@mysql_connect($db_host,$db_username,$db_password) )
				{
					if ( $request->isXmlHttpRequest() ) {
						echo json_encode(array('stat'=>'failed','msg'=>'Connection failed!'));
					} else {
						echo "Connection Failed";
					}
					$success=false;
				}
				else
				{
					if ( !@mysql_select_db($db_dbname) )
					{
						if ( $_POST['db_create'] )
						{
							$res = @mysql_query("create database `$db_dbname`");
							if ( !$res )
							{
								if ( $request->isXmlHttpRequest() ) {
									echo json_encode(array('stat'=>'failed','msg'=>'Can not create database'));
								} else {
									echo "Can not create database";
								}
								$success=false;
							}
						}
						else
						{
							if ( $request->isXmlHttpRequest() ) {
								echo json_encode(array('stat'=>'failed','msg'=>'Can not select database'));
							} else {
								echo "Can not select database";
							}
							$success=false;
						}
					}
					else
					{
						if ( $_POST['db_create'] )
						{
							$res = @mysql_query("drop database `$db_dbname`");
							if ( !$res )
							{
								if ( $request->isXmlHttpRequest() ) {
									echo json_encode(array('stat'=>'failed','msg'=>'Can not drop database'));
								} else {
									echo "Can not drop database";
								}
								$success=false;
							}
							else
							{
								$res = @mysql_query("create database `$db_dbname`");
								if ( !$res )
								{
									if ( $request->isXmlHttpRequest() ) {
										echo json_encode(array('stat'=>'failed','msg'=>'Can not create database'));
									} else {
										echo "Can not create database";
									}
									$success=false;
								}
							}
						}
					}
				}
				
				if ( $success )
				{
					// success di sini
					require_once "Zend/Config.php";
					require_once "Zend/Config/Writer/Xml.php";
					
					require_once "Gazel/Config.php";
					$config=Gazel_Config::getInstance();
					
					try {
						// open the existing one
						$configXml=new Zend_Config_Xml($config->configfile,null,array(
																		'skipExtends'        => true,
                                    'allowModifications' => true));
						$configArray=$configXml->toArray();
					} catch(Exception $e){
						$configArray=array();
					}
					
					// add a new config
					$namespace=$config->getNamespace();
					$configArray[$namespace]=array();
					$configArray[$namespace]['namespace']=$namespace;
					$configArray[$namespace]['adminpath']=$adminpath;
					$configArray[$namespace]['installed']='false';
					$configArray[$namespace]['debug']='false';
					$configArray[$namespace]['database']=array();
					$configArray[$namespace]['database']['adapter']='pdo_mysql';
					$configArray[$namespace]['database']['params']=array();
					$configArray[$namespace]['database']['params']['host']=$db_host;
					$configArray[$namespace]['database']['params']['username']=$db_username;
					$configArray[$namespace]['database']['params']['password']=$db_password;
					$configArray[$namespace]['database']['params']['dbname']=$db_dbname;
					$configArray[$namespace]['routers']=array();
					
					$configXML = new Zend_Config($configArray, true);
					
					$writer = new Zend_Config_Writer_Xml(array(
						'config'   => $configXML,
            'filename' => $config->configfile));
					$writer->write();
					
					// import database
					require_once "Zend/Config/Xml.php";
					$configinstance=new Zend_Config_Xml($this->_config->configfile,$this->_config->getNamespace(),array(
				                                    'allowModifications' => true));
					
					require_once "Gazel/Db.php";
					$dbinstance=Gazel_Db::getInstance();
					$dbinstance->setConnection($configinstance);
					
					// install db
					require_once dirname(__FILE__).'/../models/InstallModel.php';
					$model = new Core_Model_Install();
					
					if ( $model->installDb() === false )
					{
						if ( $request->isXmlHttpRequest() ) {
							echo json_encode(array('stat'=>'failed','msg'=>'Failed to import the database, please make sure the database is empty and try again'));
						} else {
							echo "Failed to import the database, please make sure the database is empty and try again";
						}
					}
					else
					{
						if ( $request->isXmlHttpRequest() ) {
							echo json_encode(array('stat'=>'success'));
						} else {
							//header("Location: ".$request->getScheme().'://'.$request->getHttpHost());
							header("Location: ".$this->_helper->url->url(array('action'=>'index3')));
							exit;
						}
					}
				}
			}
		}
		
		if ( $config->hasSetting() )
		{
			$this->view->hasConfig=true;
		}
		else
		{
			$this->view->hasConfig=false;
		}
		
		$this->view->config=array(
			'adminpath'		=> $adminpath,
			'db_host'			=> $db_host,
			'db_username'	=> $db_username,
			'db_password'	=> $db_password,
			'db_dbname'		=> $db_dbname
		);
		
		$this->render('config2');
	}
	
	public function index3Action()
	{
		if ( !$this->_config->hasSetting() )
		{
			$this->_helper->Redirector->gotoSimple('index');
		}
		
		require_once "Zend/Form.php";
		$form = new Zend_Form();
		$form->setAction($this->_helper->url->url(array('action'=>'index3')));
		
		$el = $form->createElement('text','admin_username');
		$el->setLabel('Administrator Username')
			->setRequired(true)
			->setAttribs(array('size'=>'45'))
			->addValidator('Alnum')
			->setValue('admin')
			;
		$form->addElement($el);
		
		$el = $form->createElement('text','admin_email');
		$el->setLabel('Administrator Email')
			->setRequired(true)
			->setAttribs(array('size'=>'45'))
			->addValidator('EmailAddress')
			->setValue('root@localhost')
			;
		$form->addElement($el);
		
		$el = $form->createElement('password','admin_password');
		$el->setLabel('Administrator Password')
			->setRequired(true)
			->setAttribs(array('size'=>'45'))
			->addValidator('StringLength',false,array(5,24))
			;
		$form->addElement($el);
		
		$el = $form->createElement('submit','submit');
		$form->addElement($el);
		
		if ( $_POST && $form->isValid($_POST) )
		{
			require_once "Zend/Config/Xml.php";
			$configinstance=new Zend_Config_Xml($this->_config->configfile,$this->_config->getNamespace(),array(
		                                    'allowModifications' => true));
			
			require_once "Gazel/Db.php";
			$dbinstance=Gazel_Db::getInstance();
			$dbinstance->setConnection($configinstance);
			$db = $dbinstance->getDb();
			
			$db->update('admin',array(
				'admin_username'	=> $form->admin_username->getValue(),
				'admin_email'	=> $form->admin_email->getValue(),
				'admin_password'	=> $form->admin_password->getValue()
			),array('admin_id=?' => '1'));
			
			$this->_helper->Redirector->gotoSimple('index4');
		}
		
		$this->view->form = $form;
		
		$this->render('config3');
	}
	
	public function index4Action()
	{
		if ( !$this->_config->hasSetting() )
		{
			$this->_helper->Redirector->gotoSimple('index');
		}
		
		$this->setConfigAsInstalled();
		
		require_once "Zend/Controller/Request/Http.php";
		$request = new Zend_Controller_Request_Http();
		$baseurl=$request->getScheme().'://'.$request->getHttpHost().$request->getBasePath();
		
		require_once "Zend/Config/Xml.php";
		$configinstance=new Zend_Config_Xml($this->_config->configfile,$this->_config->getNamespace(),array(
		                                    'allowModifications' => true));
		
		$this->view->baseurl = $baseurl;
		$this->view->adminurl = $baseurl.'/'.$configinstance->adminpath;
		
		$this->render('config4');
	}
	
	public function setConfigAsInstalled()
	{
		require_once "Zend/Config/Xml.php";
		$configinstance=new Zend_Config_Xml($this->_config->configfile,$this->_config->getNamespace(),array(
	                                    'allowModifications' => true));
		
		$configinstance->installed='true';
		require_once "Zend/Config/Writer/Xml.php";
		$writer = new Zend_Config_Writer_Xml(array('config'   => $configinstance,
                                         'filename' => $this->_config->configfile));
		$writer->write();
	}
}