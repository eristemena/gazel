<?php

function configForm()
{
	$form = new Gazel_Form();
	
	// title
	$title = $form->createElement('text','article_title');
	$title->setRequired(true)
		->setAttribs(array('size'=>45))
		->setLabel('Title')
		->setValue($val['article_title'])
	;
	$form->addElement($title);
	
	return $form;
}