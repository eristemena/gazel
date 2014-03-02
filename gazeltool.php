<?php

class GAZELG
{
    /**
     * Providers path
     */
	protected $_providerPath;

	/**
     * main()
     *
     * @return void
     */
    public static function main()
    {
        $zf = new self();
        $zf->bootstrap();
        $zf->run();
    }

    /**
     * bootstrap()
     *
     * @return ZF
     */
    public function bootstrap()
    {
        // setting include_path
        $incl=array(
			'.',
			'./libraries'
		);

		ini_set('include_path',implode(PATH_SEPARATOR,$incl));

        $this->_providerPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'Gazel'.DIRECTORY_SEPARATOR.'Generator'.DIRECTORY_SEPARATOR.'Provider';
    }

    public function run()
    {
    	$arguments = $_SERVER['argv'];
        
    	if ( count($arguments)==1 || $arguments[0] == '--help') {
            $this->_runInfo();
        }else{
        	$this->_runTool();
        }

        //echo ini_get('include_path');

        return $this;
    }

    protected function _getPhpClasses($php_code)
    {
        $classes = array();
        $tokens = token_get_all($php_code);
        //var_dump($tokens);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if (   $tokens[$i - 2][0] == T_CLASS
            && $tokens[$i - 1][0] == T_WHITESPACE
            && $tokens[$i][0] == T_STRING ) 
            {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }
        return $classes;
    }

    /**
     * _runInfo() - Info
     *
     * @return void
     */
    protected function _runInfo()
    {
        echo 'GAZEL Tool *Beta*';
        echo PHP_EOL.PHP_EOL;
        //echo 'Commands'.PHP_EOL;
        //echo '--------'.PHP_EOL;

        // gather providers info
        $helpMessages = array();
        foreach(new DirectoryIterator($this->_providerPath) as $f)
        {
            if($f->isDot() || $f->getFileName()=='Abstract.php' || $f->getFileName()=='.svn') continue;
            
            $fname = $f->getFileName();
            $_f = explode('.', $fname);
            $providerName = $_f[0];
            $className = 'Gazel_Generator_Provider_'.$_f[0];
            //echo 'classname: '.$className;

            require_once $f->getPathName();
            $c = new $className();

            echo $providerName.PHP_EOL;
            //echo '----------------------'.PHP_EOL;

            foreach($c->getHelpMessages() as $m)
            {
                echo "  ".str_replace('{script}', 'php '.$_SERVER['SCRIPT_NAME'], $m[0]), PHP_EOL;
                echo "    ".$m[1].PHP_EOL.PHP_EOL;
            }
        }

        echo PHP_EOL;

    }

    protected function _runTool()
    {
    	$arguments = $_SERVER['argv'];

    	// simple dispatcher
    	$action = $arguments[1];
    	$controller = $arguments[2];

    	require_once 'Zend/Filter/Word/DashToCamelCase.php';
		$filter=new Zend_Filter_Word_DashToCamelCase();
		$className = $filter->filter($controller);

		$actionName = $filter->filter($action).'Action';

    	$classPath = $this->_providerPath.DIRECTORY_SEPARATOR.$className.'.php';
    	
    	if(!file_exists($classPath)){
    		echo 'Error: Provider with name "'.$className.'" does not exist';
    		echo PHP_EOL;
    	}else{
    		require_once $classPath;
			
			$cProvider='Gazel_Generator_Provider_'.$className;
			$c = new $cProvider();
			$c->setRootPath(dirname(__FILE__));

			if( method_exists($c, $actionName) )
			{
				$c->$actionName();
			}
			else
			{
				echo 'Error: Action with name "'.$actionName.'" does not exist in provider "'.$className.'"';
    			echo PHP_EOL;
			}
    	}
    }
}

if(php_sapi_name() == "cli") {
	GAZELG::main();
}