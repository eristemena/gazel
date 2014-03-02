<?php

require_once "Zend/Form/Element.php";

class Gazel_Form_Element_Richeditor extends Zend_Form_Element
{
	public $helper = 'formTextarea';
	
	public function init()
	{
		$this->setAttribs(array('class'=>'richeditor'));
	}
}