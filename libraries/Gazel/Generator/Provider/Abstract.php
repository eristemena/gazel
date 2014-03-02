<?php

abstract class Gazel_Generator_Provider_Abstract
{
	protected $_rootPath;
	protected $_helpMessage = array();

	public function setRootPath($path)
	{
		$this->_rootPath = $path;
	}

	public function throwError($msg)
	{
		echo 'Error: '.$msg.PHP_EOL;
		echo PHP_EOL;
		exit;
	}

	protected function _addHelpMessage($command, $message)
	{
		$this->_helpMessage[] = array($command, $message);
	}

	public function getHelpMessages()
	{
		return $this->_helpMessage;
	}
}