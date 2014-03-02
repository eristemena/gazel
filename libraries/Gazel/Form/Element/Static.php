<?php

require_once 'Zend/Form/Element/Xhtml.php';

class Gazel_Form_Element_Static extends Zend_Form_Element_Xhtml
{
	public $helper = 'formStaticText'; // don't be surprised, it's from Gazel_View_Helper_FormStaticText
	
	public function init()
	{
		$this->setIgnore(true);
	}
}