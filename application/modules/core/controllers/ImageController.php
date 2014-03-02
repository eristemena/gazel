<?php

require_once "Gazel/Controller/Action.php";

class ImageController extends Gazel_Controller_Action
{
	public function dispatchAction()
	{
		$module=$this->_getParam(1);
		$width=$this->_getParam(2);
		$height=$this->_getParam(3);
		$id=$this->_getParam(4);
		$ext=$this->_getParam(5);
		
		$params=array(
			'module'	=> $module,
			'width'		=> $width,
			'height'	=> $height,
			'id'			=> $id,
			'ext'			=> $ext
		);
		
		$this->_forward('dimage','frontend',$this->_getParam('mod'));
	}
	
}