<?php

require_once "Gazel/Controller/Action.php";

class ErrorController extends Gazel_Controller_Action
{
	protected $_config;
	
	public function initGazel()
	{
		require_once "Gazel/Config.php";
		
		$this->_config=Gazel_Config::getInstance();
	}
	
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		
		switch ($errors->type)
		{
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()
							->setRawHeader('HTTP/1.1 404 Not Found');

				// plugin
				require_once "Gazel/Plugin/Broker.php";
				$pluginBroker = Gazel_Plugin_Broker::getInstance();
				$pluginBroker->onApplicationError('404', $errors->exception);

				if ( $this->_config->debug )
				{
					$this->view->err = $errors->exception->__toString();
				}

				if ( is_readable($this->_config->themepath.'/content_404.html') )
				{
					$this->renderScript('content_404.html');
					$this->_helper->layout->setLayout('master');
				}
				else
				{
					$this->render('404');
					$this->_helper->layout->setLayout('404');
				}

				break;

			default:
				$this->getResponse()
							->setRawHeader('HTTP/1.1 500 Internal Server Error');

				// plugin
				require_once "Gazel/Plugin/Broker.php";
				$pluginBroker = Gazel_Plugin_Broker::getInstance();
				$pluginBroker->onApplicationError('500', $errors->exception);

				if ( $this->_config->debug )
				{
					$this->view->err = $errors->exception->__toString();
				}

				if ( is_readable($this->_config->themepath.'/content_500.html') )
				{
					$this->renderScript('content_500.html');
					$this->_helper->layout->setLayout('master');
				}
				else
				{
					$this->render('500');
					$this->_helper->layout->setLayout('500');
				}

				break;
		}
	}

}