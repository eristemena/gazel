<?php

require_once "Gazel/Form.php";

class Admin_Form_Group extends Gazel_Form
{
	public function prepareForm($values)
	{
		$name=$this->createElement('text','admingroup_name');
		$name
			->setLabel('Group Name')
			->addFilter('alnum')
			->addFilter('stringtolower')
			->setAttribs(array('size'=>15,'maxlength'=>10))
			->setRequired(true)
			->setValue($res['admingroup_name'])
		;
		
		// desc
		$desc=$this->createElement('text','admingroup_desc');
		$desc->setLabel('Description')
			->setAttribs(array('size'=>35))
			->setValue($res['admingroup_desc'])
			->setRequired(true)
		;
		
		$this->addElement($name)
			->addElement($desc)
		;
	}
}
