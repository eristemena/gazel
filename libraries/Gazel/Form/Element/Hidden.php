<?php

require_once "Zend/Form/Element.php";

class Gazel_Form_Element_Hidden extends Zend_Form_Element
{
	public $helper = 'formHidden';
	
	public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Tooltip')
                 ->addDecorator('ViewHelper')
                 ;
        }
    }
}