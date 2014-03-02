<?php

require_once "Gazel/Generator/Provider/Abstract.php";

class Gazel_Generator_Provider_Module extends Gazel_Generator_Provider_Abstract
{
	protected $_modulePath;
	protected $_moduleName;
	protected $_moduleClassName;

	public function __construct()
	{
		$this->_addHelpMessage('create module module-name', 'Create a new module');
		$this->_addHelpMessage('delete module module-name', 'Delete a module');
	}

	/**
	 * Delete a module
	 * usage: SCRIPT_NAME delete module {moduleName}
	 */
	public function deleteAction()
	{
		$this->_init();

		if(!isset($this->_moduleName) || strlen($this->_moduleName)==0)
		{
			$this->throwError('Usage: php '.$_SERVER['SCRIPT_NAME'].' delete module "modulename"');
		}

		if( !file_exists($this->_modulePath) || !is_dir($this->_modulePath) )
		{
			$this->throwError('Module "'.$this->_moduleName.'" does not exist');
		}

		$this->_delTree($this->_modulePath.DIRECTORY_SEPARATOR);
	}

	/**
	 * Create a module
	 * usage: SCRIPT_NAME create module {moduleName}
	 */
	public function createAction()
	{
		$this->_init();

		if(!isset($this->_moduleName) || strlen($this->_moduleName)==0)
		{
			$this->throwError('Usage: php '.$_SERVER['SCRIPT_NAME'].' create module "modulename"');
		}

		// check if module exists
		if( file_exists($this->_modulePath) )
		{
			$this->throwError('Module with name "'.$this->_moduleName.'" already exists');
		}
		else
		{
			$structDir = array(
				$this->_modulePath,
				$this->_modulePath.DIRECTORY_SEPARATOR.'controllers',
				$this->_modulePath.DIRECTORY_SEPARATOR.'languages',
				$this->_modulePath.DIRECTORY_SEPARATOR.'models',
				$this->_modulePath.DIRECTORY_SEPARATOR.'forms',
				$this->_modulePath.DIRECTORY_SEPARATOR.'views',
				$this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'helpers',
				$this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts',
				$this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'admin',
				$this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'frontend',
			);

			foreach($structDir as $dir)
			{
				if( !mkdir($dir, 0755, true) ){
					$this->throwError('Oops, can not create directory: '.$dir);
				}
			}

			$this->_createManifestFile();

			$this->_createSetupFile();

			$this->_createControllers();

			$this->_createViewScriptAdmin();

			$this->_createViewScriptFrontend();

			// admin form
			require_once "Gazel/Generator/File.php";
			Gazel_Generator_File::createFormFile($this->_modulePath, $this->_moduleName, 'admin');

			// admin model
			require_once "Gazel/Generator/File.php";
			Gazel_Generator_File::createModelFile($this->_modulePath, $this->_moduleName, 'admin');

			// lang: en
			require_once "Gazel/Generator/File.php";
			Gazel_Generator_File::createLanguageDictionary($this->_modulePath, 'en');

			// lang: id
			require_once "Gazel/Generator/File.php";
			Gazel_Generator_File::createLanguageDictionary($this->_modulePath, 'id');
		}
	}

	protected function _createViewScriptFrontend()
	{
		$out = '';
		$filePath = $this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'index.html';
		file_put_contents($filePath, $out);
	}

	protected function _createViewScriptAdmin()
	{
		// index.html
		$out = '
<div id="Layer1" style="position:absolute; left:561px; top:143px; width:544px; height:28px; z-index:1">
<?php
	
	$fields = array (
		array("field1",$this->translate->_("Field Name 1")),
		array("field2",$this->translate->_("Field Name 2")),
	);
	
	echo $this->admin()->searchForm($fields);
?>
</div>
<table class="tadmin">
	<thead>   
		<tr>
			<th width="5" class="cb"><input type="checkbox" id="cbcall" /></th>
			<?php echo $this->admin()->th("field1",$this->translate->_("Field Name 1")); ?>
			<?php echo $this->admin()->th("field2",$this->translate->_("Field Name 2")); ?>
		</tr>
	</thead>
	<tbody>
		<?php //foreach ( $this->paginator as $k=>$n ): ?>
		<tr>
			<td><input type="checkbox" name="cb[]" value="<?php echo $n1 ?>" /></td>
			<td><a href="<?php echo $this->admin()->editLink($id) ?>"><?php echo $this->escape($name) ?></a></td>
			<td><?php echo $this->escape($name2) ?></td>
		</tr>
		<?php //endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo $this->paginator ?></td>
		</tr>
	</tfoot>
</table>';

		$filePath = $this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'index.html';
		file_put_contents($filePath, $out);

		// form.html
		$out = '<?php echo $this->form ?>';

		$filePath = $this->_modulePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'form.html';
		file_put_contents($filePath, $out);
	}

	protected function _createControllers()
	{
		// AdminController.php
		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createAdminControllerFile($this->_modulePath, $this->_moduleName, 'admin');

		// FrontendController.php
		require_once "Gazel/Generator/File.php";
		Gazel_Generator_File::createControllerFile($this->_modulePath, $this->_moduleName, 'frontend');
	}

	protected function _createManifestFile()
	{
		$out = '<?xml version="1.0" encoding="utf-8"?>
<install type="module" version="0.0.1">
	<name>'.htmlentities($this->_moduleName).'</name>
	<description>Module: '.htmlentities($this->_moduleName).'</description>
	<author>Your Name</author>
	<creationDate></creationDate>
	<copyright>Copyright (C) 2009 Open Source Matters. All rights reserved.</copyright>
	<license>http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL</license>
	<authorEmail>user@example.com</authorEmail>
	<authorUrl>www.example.com</authorUrl>
	<version>0.0.1</version>
</install>';

		$xmlPath = $this->_modulePath.DIRECTORY_SEPARATOR.$this->_moduleName.'.xml';
		file_put_contents($xmlPath, $out);
	}

	protected function _createSetupFile()
	{
		require_once "Zend/CodeGenerator/Php/File.php";
		require_once "Zend/CodeGenerator/Php/Class.php";

		$class      = new Zend_CodeGenerator_Php_Class();
		$docblock = new Zend_CodeGenerator_Php_Docblock(array(
		    'shortDescription' => 'Setup File'
		));

		$class->setName($this->_moduleClassName.'_Setup')
			->setDocblock($docblock)
			->setExtendedClass('Gazel_Module_Setup_Abstract')
			->setMethods(array(
				array(
					'name' => 'install',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Do something when module installed'
                	)),
					'body' => '
/**
$sql=array();
		
$sql[]="
CREATE TABLE `".$this->__tableName."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
";

foreach ( $sql as $q )
{
	try {
		$this->_db->query($q);
	} catch(Exception $e) {
		$this->setErrors(array("Unable to create tables"));
		return false;
	}
}

**/

return true;',
				),
				array(
					'name' => 'uninstall',
					'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
                		'shortDescription' => 'Do something when module uninstalled'
                	)),
					'body' => '
/**
$sql=array();

$sql[]="
DROP TABLE IF EXISTS `".$this->__tableName."`
";

foreach ( $sql as $q )
{
	try {
		$this->_db->query($q);
	} catch(Exception $e) {
		$this->setErrors(array("Unable to drop tables"));
		return false;
	}
}
**/

return true;',
				)
			))
		;

		$setupFile = new Zend_CodeGenerator_Php_File();
		$setupFile->setRequiredFiles(array('Gazel/Module/Setup/Abstract.php'))
			->setClass($class)
		;

		$filePath = $this->_modulePath.DIRECTORY_SEPARATOR.'Setup.php';

		file_put_contents($filePath, $setupFile->generate());
	}

	protected function _delTree($dir)
	{
		$files = glob( $dir . '*', GLOB_MARK ); 
		foreach( $files as $file ){ 
			if( substr( $file, -1 ) == '/' ){
				$this->_delTree( $file ); 
			} else {
				//echo "unlink($file)"."\n";
				unlink( $file );
			}
		}
		//echo "rmdir($dir)"."\n";
		rmdir( $dir );
	}

	protected function _init()
	{
		$args = $_SERVER['argv'];

		$moduleName = $args[3];
		
		if( preg_match('/[^a-z\-]/', $moduleName) ){
			$this->throwError('Module name is not valid: use only alphanumeric and dash, all lower case');
		}

		$this->_moduleName = $moduleName;

		require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();

		$this->_moduleClassName = $filter->filter($this->_moduleName);

		$path = $this->_rootPath.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->_moduleName;
		$this->_modulePath = $path;
	}

}