<?php

class Gazel_Generator_File
{
	public static function createControllerFile($modulePath, $moduleName, $controllerName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$controllerClassName = $filter->filter($moduleName).'_'.$filter->filter($controllerName).'Controller';
		$controllerFileName = $filter->filter($controllerName).'Controller.php';

		// FrontendController.php
		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $controllerClassName
		));

		$bodyIndexAction = '// $dbs = $this->loadModel("modelName", "moduleName")->getAsDbs();'."\n".
        	'// $paginator=$this->getPaginator($dbs);'."\n".
        	'// $this->view->paginator=$paginator;';

		$class->setName($controllerClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Controller_Action')
			->setMethods(array(
				array(
					'name' => 'initGazel',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Initialization'
                	))
				),
				array(
					'name' => 'indexAction',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'index action'
                	)),
                	'body' => $bodyIndexAction
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Controller/Action.php'))
			->setClass($class)
		;

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createAdminControllerFile($modulePath, $moduleName, $controllerName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$controllerClassName = $filter->filter($moduleName).'_'.$filter->filter($controllerName).'Controller';
		$controllerFileName = $filter->filter($controllerName).'Controller.php';

		// FrontendController.php
		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $controllerClassName
		));

		$bodyIndexAction = '// $dbs=$this->_db->select()->from($this->__tableName)->order($this->getOrdering())->where($this->getSearch());'."\n".
			'// $paginator=$this->getPaginator($dbs);'."\n".
			'// $this->view->paginator=$paginator;';

		$class->setName($controllerClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Controller_Action_Admin')
			->setMethods(array(
				array(
					'name' => 'initAdmin',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Initialization'
                	))
				),
				array(
					'name' => 'indexAction',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'index action'
                	)),
                	'body' => $bodyIndexAction
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Controller/Action/Admin.php'))
			->setClass($class)
		;

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createFormFile($modulePath, $moduleName, $formName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$formClassName = $filter->filter($moduleName).'_Form_'.$filter->filter($formName);
		$formFileName = $filter->filter($formName).'Form.php';

		// FrontendController.php
		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $formClassName
		));

		$class->setName($formClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Form')
			->setMethods(array(
				array(
					'name' => 'prepareForm',
					'parameters' => array(
		                array('name' => 'val'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Construct Form'
                	)),
                	'body'	=> '// $t = Zend_Registry::get("translate");'."\n".'// $this->setTranslator($t);'
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Form.php'))
			->setClass($class)
		;

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.$formFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createModelFile($modulePath, $moduleName, $modelName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$modelClassName = $filter->filter($moduleName).'_Model_'.$filter->filter($modelName);
		$modelFileName = $filter->filter($modelName).'Model.php';

		// FrontendController.php
		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $modelClassName
		));

		$class->setName($modelClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Model')
			->setMethods(array(
				array(
					'name' => 'getAsDbs',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Get dbs'
                	)),
                	'body' => '// $dbs = $this->_db->select()->from($this->__tableName);'."\n".'// return $dbs;',
				),
				array(
					'name' => 'get',
					'parameters' => array(
		                array('name' => 'id'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Get data',
                		'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'id',
								'datatype'  => 'int'
							)),
		                ),
                	)),
                	'body' => '// $dbs = $this->getAsDbs()->where("id=?",$id);'."\n".'// $res = $this->_db->fetchRow($dbs);'."\n".'// return $res;',
				),
				array(
					'name' => 'edit',
					'parameters' => array(
						array('name' => 'data'),
		                array('name' => 'id'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Edit data',
                		'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'data',
								'datatype'  => 'array'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'id',
								'datatype'  => 'int'
							)),
		                ),
                	)),
                	'body' => '// $this->_db->update($this->__tableName, $data, array("id=?" => $id));',
				),
				array(
					'name' => 'add',
					'parameters' => array(
						array('name' => 'data'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Add data',
                		'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'data',
								'datatype'  => 'array'
							)),
		                ),
                	)),
                	'body' => '// $this->_db->insert($this->__tableName, $data);',
				),
				array(
					'name' => 'delete',
					'parameters' => array(
						array('name' => 'ids'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Delete some data based on id',
						'tags'             => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'ids',
								'datatype'  => 'array'
							)),
		                ),
                	)),
                	'body' => '// foreach($ids as $id) {'."\n"."\t".'// $this->_db->delete($this->__tableName,array("id=?" => $id));'."\n"."// }"
				),
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Model.php'))
			->setClass($class)
		;

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.$modelFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createViewHelperFile($modulePath, $moduleName, $viewHelperName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$viewHelperClassName = $filter->filter($moduleName).'_View_Helper_'.$filter->filter($viewHelperName);
		$viewHelperFileName = $filter->filter($viewHelperName).'.php';

		// FrontendController.php
		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $viewHelperClassName
		));

		$class->setName($viewHelperClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_View_Helper_Abstract')
			->setMethods(array(
				array(
					'name' => lcfirst($filter->filter($viewHelperName)),
					'parameters' => array(
		                array('name' => 'val'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Shortcut view helper'
                	)),
                	'body'	=> 'return $val;'
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/View/Helper/Abstract.php'))
			->setClass($class)
		;

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.$viewHelperFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createRouterFile($routerPath, $routerName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$routerClassName = 'Gazel_Router_'.$filter->filter($routerName);
		$routerFileName = $filter->filter($routerName).'.php';

		$class    = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $routerClassName
		));

		$class->setName($routerClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Controller_Router_Abstract')
			->setMethods(array(
				array(
					'name' => 'init',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Router description here'
                	)),
                	'body'	=> '$router = new Zend_Controller_Router_Route('."\n".
					"	'".$routerName."/*',"."\n".
					"	array("."\n".
					"		'module' => 'your-module',"."\n".
					"		'controller' => 'your-controller',"."\n".
					"		'action' => 'your-action'"."\n".
					"	)"."\n".
					");"."\n".
					'$this->_front->getRouter()->addRoute(\''.$routerName.'\', $router);'
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Controller/Router/Abstract.php'))
			->setClass($class)
		;

		$filePath = $routerPath.DIRECTORY_SEPARATOR.$routerFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createWidgetFile($widgetPath, $widgetName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$widgetClassName = $filter->filter($widgetName).'Widget';
		$widgetFileName = $filter->filter($widgetName).'.php';

		$class    = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $widgetClassName
		));

		$class->setName($widgetClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Widget')
			->setMethods(array(
				array(
					'name' => 'frontEnd',
					'parameters' => array(
		                array('name' => 'data'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Handle frontend',
                		'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'data',
								'datatype'  => 'array',
								'description' => 'Widget data from database'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'string'
							)),
		                ),
                	)),
                	'body'	=> '$data=unserialize($data[\'widget_data\']);'."\n".
					'$out="";'."\n".
					'return $out;'
				),
				array(
					'name' => 'backendForm',
					'parameters' => array(
		                array('name' => 'data'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Handle backend',
                		'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'data',
								'datatype'  => 'array',
								'description' => 'Widget data from database'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'Gazel_Form'
							)),
		                ),
                	)),
                	'body'	=> '$form = new Gazel_Form();'."\n".
                	'// username'."\n".
					'$el = $form->createElement("text","widget_data");'."\n".
					'$el->setRequired(true)'."\n".
					'	->setAttribs(array(\'size\'=>45))'."\n".
					'	->setLabel(\'Widget Data\')'."\n".
					'	->setValue($data[\'widget_data\'])'."\n".
					';'."\n".
					'$form->addElement($el);'."\n".
					'return $form;'
				),
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Widget.php'))
			->setClass($class)
		;

		$filePath = $widgetPath.DIRECTORY_SEPARATOR.$widgetFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createPluginFile($pluginPath, $pluginName)
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$pluginClassName = $filter->filter($pluginName).'Plugin';
		$pluginFileName = $filter->filter($pluginName).'.php';

		$class    = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => $pluginClassName
		));

		$class->setName($pluginClassName)
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Plugin_Abstract')
			->setMethods(array(
				array(
					'name' => 'onFrontendRenderBody',
					'parameters' => array(
		                array('name' => 'body'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'When frontend render page body (<body></body>)',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'body',
								'datatype'  => 'string',
								'description' => 'Page content'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'string'
							)),
						),
					)),
					'body'	=> '// do something here'."\n\n".
						'return $body;'
				),
				array(
					'name' => 'onFrontendRenderHead',
					'parameters' => array(
		                array('name' => 'head'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'When frontend render page head (<head></head>)',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'head',
								'datatype'  => 'string',
								'description' => 'Head content'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'string'
							)),
						),
					)),
					'body'	=> '// do something here'."\n\n".
						'return $head;'
				),
				array(
					'name' => 'onPrependAdminMenu',
					'parameters' => array(
		                array('name' => 'menu','type' => 'Gazel_Menu_Xml'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'Prepend admin menu',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'menu',
								'datatype'  => 'Gazel_Menu_Xml',
								'description' => 'admin menu'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'Gazel_Menu_Xml'
							)),
						),
					)),
					'body'	=> '// do something here'."\n\n".
						'return $menu;'
				),
				array(
					'name' => 'onAppendAdminMenu',
					'parameters' => array(
		                array('name' => 'menu','type' => 'Gazel_Menu_Xml'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'Append admin menu',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'menu',
								'datatype'  => 'Gazel_Menu_Xml',
								'description' => 'admin menu'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'Gazel_Menu_Xml'
							)),
						),
					)),
					'body'	=> '// do something here'."\n\n".
						'return $menu;'
				),
				array(
					'name' => 'onAdminRenderPanel',
					'parameters' => array(
		                array('name' => 'form','type' => 'Gazel_Form'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'Render admin panel',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'form',
								'datatype'  => 'Gazel_Form',
								'description' => 'form to render'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
								'datatype'  => 'Gazel_Form'
							)),
						),
					)),
					'body'	=> '// do something here'."\n\n".
						'return $form;'
				),
				array(
					'name' => 'onApplicationError',
					'parameters' => array(
		                array('name' => 'code'),
		                array('name' => 'exception', 'type' => 'Exception'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'When application error happen',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'code',
								'datatype'  => 'string',
								'description' => 'HTTP Error Code'
							)),
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'exception',
								'datatype'  => 'Exception',
								'description' => 'Exception'
							)),
						),
					)),
					'body'	=> '// do something here'
				),
				array(
					'name' => 'onRouteShutdown',
					'parameters' => array(
		                array('name' => 'request', 'type' => 'Zend_Controller_Request_Abstract'),
		            ),
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
						'shortDescription' => 'Execute when route shutdown on MVC cycle',
						'tags' => array(
							new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
								'paramName' => 'request',
								'datatype'  => 'Zend_Controller_Request_Abstract',
								'description' => 'Request'
							)),
						),
					)),
					'body'	=> '// do something here'
				),
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Plugin/Abstract.php'))
			->setClass($class)
		;

		$filePath = $pluginPath.DIRECTORY_SEPARATOR.$pluginFileName;
		file_put_contents($filePath, $setupFile->generate());
	}

	public static function createLanguageDictionary($modulePath, $langName)
	{
		$out = 'English,English';

		$filePath = $modulePath.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$langName.'.csv';
		file_put_contents($filePath, $out);
	}
}