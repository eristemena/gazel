<?php

require_once "Gazel/Controller/Action/Admin.php";

class Admin_PluginController extends Gazel_Controller_Action_Admin
{
	public function initAdmin()
	{
		$this->view->moduletitle='Plugin Manager';
		unset($this->view->submenu);
		
		$this->checkAdminAccess('admin.extension.plugin');
	}
	
	public function indexAction()
	{
		require_once "Zend/Paginator/Adapter/Array.php";
		$adapter = new Zend_Paginator_Adapter_Array($this->loadModel('plugin')->getPluginAvailable());
		$paginator = new Zend_Paginator($adapter);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'pagination_control.html'
		);
		
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage(10);
		
		// installed plugins
		$res=$this->_db->fetchAssoc($this->_db->select()->from('plugin'));
		$im=array();
		foreach ( $res as $r ) {
			$im[]=$r['plugin_name'];
		}
		$this->view->installedplugin=$im;
		
		if ( $this->_config->multipleuser && !$this->_config->mMaster )
		{
			$this->view->canDownload=false;
			$this->view->canDelete=false;
		}
		else
		{
			$this->view->canDownload=true;
			$this->view->canDelete=true;
		}
		
		$this->view->paginator=$paginator;
	}
	
	public function installAction()
	{
		$pluginname=$this->_getParam('mod');
		if ( $xml=$this->getPluginXml($pluginname) )
		{
			$this->_db->insert($this->__plugin,array(
				'plugin_name'				=> $this->_getParam('mod'),
				'plugin_title'			=> $xml->name,
				'plugin_installed'	=> new Zend_Db_Expr('now()')
			));
			
			if ( $this->_request->isXmlHttpRequest() )
			{
				$this->_helper->json(array('stat'=>'success','module'=>$pluginname));
			}
			else
			{
				$this->redirect(array('action'=>'index'));
			}
		}
		else
		{
			if ( $this->_request->isXmlHttpRequest() )
			{
				$this->_helper->json(array('stat'=>'failed','module'=>$pluginname));
			}
			else
			{
				$this->redirect(array('action'=>'index'));
			}
		}
		
	}
	
	public function uninstallAction()
	{
		$this->_db->delete($this->__plugin,"plugin_name='".$this->_getParam('mod')."'");
		if ( $this->getRequest()->isXmlHttpRequest() ) {
			$this->_helper->json(array('stat'=>'success','module'=>$this->_getParam('mod')));
		} else {
			$this->redirect(array('action'=>'index'));
		}
	}
	
	public function downloadAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$plugin=$this->_getParam('mod');
			$dir=$this->_config->applicationdir.'/plugins/'.$plugin;
			
			$filename=tempnam(sys_get_temp_dir(),'plugindownload');
			$this->_helper->Zip->zip($filename,$dir,$plugin.'/');
			$filesize = @filesize($ilename);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=".$plugin.".zip;" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$filesize);
			readfile("$filename");
			
			@unlink($filename);
			exit;
		}
	}
	
	public function deleteAction()
	{
		if ( !$this->_config->multipleuser || $this->_config->mMaster )
		{
			$plugin=$this->_getParam('mod');
			$this->_helper->removeDir($this->_config->applicationdir.'/plugins/'.$plugin);
			$this->_helper->Redirector->gotoRoute(array('action'=>'index','controller'=>'plugin'),null,true);
		}
	}
	
	public function getPluginXml($pluginname)
	{
		$mxml1=$this->_config->gazeldir.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pluginname.DIRECTORY_SEPARATOR.$pluginname.'.xml';
		$mxml2=$this->_config->applicationdir.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pluginname.DIRECTORY_SEPARATOR.$pluginname.'.xml';
		
		if ( !file_exists($mxml1) ) {
			if( !file_exists($mxml2) ) {
				return false;
			} else {
				return simplexml_load_file($mxml2);
			}
		} else {
			return simplexml_load_file($mxml1);
		}
	}
	
	public function adminAction() // backend for plugins
	{
		$plugin=$this->_getParam('plugin');
		$res=$this->_db->fetchRow($this->_db->select()->from($this->__plugin)->where('plugin_name=?',$plugin));
		if ( !$res )
		{
			$this->_forward('404');
		}
		else
		{
			$this->view->moduletitle='Plugin Config: '.$res['plugin_title'];
			
			require_once "Zend/Filter/Word/DashToCamelCase.php";
			$filter=new Zend_Filter_Word_DashToCamelCase();
			$fileName=$filter->filter($plugin);
			require_once $plugin.'/'.$fileName.'.php';
			$className=$fileName.'Plugin';
			$class=new $className();
			
			$data=unserialize($res['plugin_data']);
			$form=$this->getPluginForm($class->backendForm($data),$res);
			
			if ( $_POST && $form->isValid($_POST) )
			{
				$values=$form->getValues();
				
				$data=serialize($values);
				$pos=$values['pos'];
				//print_r($values);
				$this->_db->update($this->__plugin,array(
					'plugin_data'			=> $data,
					'plugin_pos'			=> $pos,
					'plugin_active'		=> $values['plugin_active'],
					'plugin_updated'	=> new Zend_Db_Expr('now()')
				),array('plugin_id=?' => $_POST['id']));
			}
			
			$this->view->form=$form;
		}
	}
	
	public function getPluginForm($form,$res)
	{
		// id
		$id = $form->createElement('hidden','id')
			->setValue($res['plugin_id'])
			->setIgnore(true)
		;
		$form->addElement($id);
		
		// position
		$el=$form->createElement('select','pos');
		$el->setLabel('Position')
			->addMultiOptions(array('left' => 'Left','right' => 'Right'))
			->setValue($res['plugin_pos'])
		;
		$form->addElement($el);
		
		// active
		$el=$form->createElement('select','plugin_active');
		$el->setLabel('Active')
			->addMultiOptions(array('y' => 'Yes','n' => 'No'))
			->setValue($res['plugin_active'])
		;
		$form->addElement($el);
		
		// submit
		$submit = $form->createElement('submit','Submit')
			->setIgnore(true)
		;
		$form->addElement($submit);
		
		return $form;
	}
	
	public function getForm()
	{
		if ( $this->_getParam('action')=='edit' ) {
			$res=$this->loadModel('module')->getPlugin($this->_getParam('id'));
		}
		
		$form = new Gazel_Form();
		
		$username = $form->createElement('text', 'module_title');
		$username->setLabel('Titlessssss')->setValue($res['module_title']);
		
		// Add elements to form:
		$form->addElement($username);
		
		return $form;
	}
	
	public function settingAction()
	{
		$pluginName = mysql_escape_string($this->getRequest()->getParam('plugin'));

		$dbs = $this->_db->select()->from($this->__plugin)->where('plugin_name=?', $pluginName);
		$res = $this->_db->fetchRow($dbs);
		if( !$res )
		{
			$this->redirect404();
		}

		$plugin_title = mysql_escape_string($this->getRequest()->getParam('name'));
		
		$this->view->moduletitle= 'Plugin Configuration: ' . $res['plugin_title'];
		
		require_once "Gazel/Plugin/Broker.php";
		$pluginBroker = Gazel_Plugin_Broker::getInstance();

		$data = $pluginBroker->getAdminSave($pluginName);

		$form = new Gazel_Form();

		// adding element from plugin
		$form = $pluginBroker->adminRenderPanel($pluginName, $form);

		if( !$form instanceof Gazel_Form ) // just in case
		{
			require_once "Gazel/Plugin/Exception.php";
			throw new Gazel_Plugin_Exception("Plugin \"".$pluginName."\" does not implement onAdminRenderPanel() correctly");
		}

		$form = $this->getAdminForm($form);

		// set values
		$form->setFormValues($pluginBroker->getPluginOptions($pluginName));

		if( $_POST && $form->isValid($_POST) )
		{
			$pluginBroker->savePluginOptions($pluginName, $form->getValues());

			$this->_helper->flashMessenger($this->_t->_('Data is updated'));
			$this->_helper->redirector->gotoRoute(array(),'admin');
		}

		$this->view->form=$form;
		$this->view->msg = $this->_helper->flashMessenger->getMessages();
	}
}
