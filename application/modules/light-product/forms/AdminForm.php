<?php

require_once "Gazel/Form.php";

class LightProduct_Form_Admin extends Gazel_Form
{
	public function prepareForm($val)
	{
		$elements = array();
		//for translate
			$translate= Zend_Registry::get('translate');	
		//
		$elements[] = array(
			'type'		=> 'text',
			'name'		=> 'product_name',
			'label'		=> $translate->_('Name'),
			'value'		=> $val['product_name'],
			'size'		=> 45,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'file',
			'name'		=> 'product_pict',
			'label'		=> $translate->_('Picture'),
			'value'		=> $val['product_pict'],
			'validators'=> array(
				array('Extension',false,'jpg')
			)
		);
		
		$elements[] = array(
			'type'		=> 'richeditor',
			'name'		=> 'product_desc',
			'label'		=> $translate->_('Description'),
			'value'		=> $val['product_desc'],
			'required'	=> true
		);
		
		$this->addNewElements($elements);
	}
}