<?php

require_once "Zend/Form.php";

class Gazel_Form extends Zend_Form
{
	protected $_config;

	public function init()
	{
		$this->_config = Gazel_Config::getInstance();
		
		if (is_array($options)) {
			$this->setOptions($options);
		} elseif ($options instanceof Zend_Config) {
			$this->setConfig($options);
		}
		
		$this->addElementPrefixPath('Gazel_Filter','Gazel/Filter','filter');
		$this->addElementPrefixPath('Gazel_Validate','Gazel/Validate','validate');
		$this->addPrefixPath('Gazel_Form','Gazel/Form');
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'div', 'class' => 'gazel_form')),
			'Form'
		));
	}
	
	/**
	 * Override Zend_Form::getValues() to get 
	 * form values
	 * 
	 * @see Zend_Form::getValues()
	 */
	public function ______getValues()
	{
		$values=array();
		foreach ( $this->getElements() as $key => $element )
		{
			if ( !$element->getIgnore() )
			{
				$values[$key]=$element->getUnfilteredValue();
				foreach ($element->getFilters() as $fname => $filter) 
				{
					if ( strtolower($fname)=='gazel_filter_date2mysql' )
					{
						$values[$key] = $filter->toDateMysql($values[$key]);
					}
					else
					{
						$values[$key] = $filter->filter($values[$key]);
					}
				}
			}
		}
		
		return $values;
	}
	
	/**
     * Shortcut to add elements
     *
     * @param  array $options
     */
	public function addNewElement($options)
	{
		$type = $options['type'];
		$name = $options['name'];
		
		$el = $this->createElement($type,$name,$options);
		//$el->setLabel($label);
		
		$this->addElement($el);
	}
	
	/**
     * Shortcut to add new elements, accept array of options
     *
     * @param  array $options
     */
	public function addNewElements($aoptions)
	{
		foreach ( $aoptions as $options )
		{
			$el = $this->addNewElement($options);
		}
	}
	
	/**
     * Developer can override this to specify their own form
     *
     * @param  array $values
     */
	public function prepareForm($values)
	{
		
	}
	
	public function getForm($values = array())
	{
		$this->prepareForm($values);
		
		return $this;
	}
	
	public function setFormValues($values)
	{
		foreach ( $values as $key => $val )
		{
			$el = $this->getElement($key);
			if ( $el instanceof Zend_Form_Element ){
				$el->setValue($val);
			}
		}
	}
	
	public function gazelAddElementSubmit()
	{
		$submit = $this->createElement('submit','Submit')
			->setIgnore(true)
		;
		
		$this->addElement($submit);
	}
	
	public function gazelTableDecorator()
	{
		$this->setElementDecorators(array(
			'ViewHelper',
			'Errors',
			array('Description', array('tag' => 'p','escape'=>false,'placement'=>'prepend')),
			array(array('t' => 'HtmlTag'), array('tag' => 'td')),
			array('Label', array('tag' => 'td','class' => 'label')),
			array(array('t3' => 'HtmlTag'), array('tag' => 'tr')),
		),array('id','page','Submit'),false);
	}
	
}