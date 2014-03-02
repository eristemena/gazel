<?php

require_once "Zend/Form/Element.php";

class Gazel_Form_Element_Date extends Zend_Form_Element
{
	public function init()
	{
		$this->addFilter('Date2Mysql')
			->setAttribs(array('class'=>'date','size'=>10))
		;
	}
}