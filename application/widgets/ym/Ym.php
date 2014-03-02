<?php

require_once "Gazel/Widget.php";

class YmWidget extends Gazel_Widget
{
	public function frontEnd($r)
	{
		$data=unserialize($r['widget_data']);
		$out='
<a style="text-decoration:none;" href="ymsgr:sendim?'.$data['ym_username'].'">
<img border="0" src="http://opi.yahoo.com/online?u='.$data['ym_username'].'&amp;m=g&amp;t=14">
</a>
		';
		return $out;
	}
	
	public function backendForm($data)
	{
		$form = new Gazel_Form();
		
		// username
		$el = $form->createElement('text','ym_username');
		$el->setRequired(true)
			->setAttribs(array('size'=>45))
			->setLabel('YM Username')
			->setValue($data['ym_username'])
		;
		$form->addElement($el);
		
		return $form;
	}
}