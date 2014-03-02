<?php

require_once "Gazel/Form.php";

class LightNews_Form_Admin extends Gazel_Form
{
	public function prepareForm($val)
	{
		$elements = array();
		//for translate
			$translate= Zend_Registry::get('translate');	
		//
		$elements[] = array(
			'type'		=> 'text',
			'name'		=> 'news_title',
			'label'		=> $translate->_('Title'),
			'value'		=> $val['news_title'],
			'size'		=> 45,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'textarea',
			'name'		=> 'news_shortdesc',
			'label'		=> $translate->_('Short Desc'),
			'value'		=> $val['news_shortdesc'],
			'cols'		=> 45,
			'rows'		=> 5,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'date',
			'name'		=> 'news_date',
			'label'		=> $translate->_('Date'),
			'value'		=> $val['news_date'],
			'cols'		=> 45,
			'rows'		=> 5,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'richeditor',
			'name'		=> 'news_content',
			'label'		=> $translate->_('Content'),
			'value'		=> $val['news_content'],
			'size'		=> 45
		);
		
		$this->addNewElements($elements);
	}
}