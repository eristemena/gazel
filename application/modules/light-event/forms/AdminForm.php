<?php

require_once "Gazel/Form.php";

class LightEvent_Form_Admin extends Gazel_Form
{
	public function prepareForm($val)
	{
		//for translate
			$translate= Zend_Registry::get('translate');	
		//
		$elements = array();
		
		$elements[] = array(
			'type'		=> 'text',
			'name'		=> 'event_title',
			'label'		=> $translate->_('Title'),
			'value'		=> $val['event_title'],
			'size'		=> 45,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'textarea',
			'name'		=> 'event_shortdesc',
			'label'		=> $translate->_('Short Desc'),
			'value'		=> $val['product_name'],
			'cols'		=> 45,
			'rows'		=> 5,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'date',
			'name'		=> 'event_date_from',
			'label'		=> $translate->_('Date Start'),
			'value'		=> $val['event_date_from'],
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'date',
			'name'		=> 'event_date_to',
			'label'		=> $translate->_('Date End'),
			'value'		=> $val['event_date_to'],
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'text',
			'name'		=> 'event_location',
			'label'		=> $translate->_('Location'),
			'value'		=> $val['event_location'],
			'size'		=> 45,
			'required'	=> true
		);
		
		$elements[] = array(
			'type'		=> 'richeditor',
			'name'		=> 'event_content',
			'label'		=> $translate->_('Content'),
			'value'		=> $val['event_content'],
			'required'	=> true
		);
		
		$this->addNewElements($elements);
	}
}