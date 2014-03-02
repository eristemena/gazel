<?php

require_once "Gazel/Form.php";

class Admin_Form_Theme extends Gazel_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$view = $this->getView();
		
		$this->setAction($_SERVER['REQUEST_URI'])
    	->setMethod('post');
	}
}
