<?php

require_once "Gazel/Form.php";

class Admin_Form_Section extends Gazel_Form
{
	public function prepareForm($values)
	{
		// name
		$name=$this->createElement('text','section_name')
			->setLabel('Name')
			->setAttribs(array('size'=>30,'maxlength'=>40))
			->setRequired(true)
			->addFilter('PostSlug')
			->setValue($res['section_name'])
		;
		$this->addElement($name);
		
		// desc
		$title=$this->createElement('text','section_desc')
			->setLabel('Description')
			->setAttribs(array('size'=>45))
			->setRequired(true)
			->setValue($res['section_desc'])
		;
		$this->addElement($title);
	}
}
