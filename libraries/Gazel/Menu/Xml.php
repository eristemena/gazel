<?php

class Gazel_Menu_Xml
{
	private $xml;
	private $mc=1; // count menu element (for id)
	private $uc=0; // count unclosed element
	private $ns=1; // count separator
	
	public function __construct()
	{
		$this->xml = new xmlWriter();
		$this->xml->openMemory();
		
		$this->xml->startElement('menu');
	}
	
	public function addMenu($text,$id=0)
	{
		$this->closeElement();
		
		$this->xml->startElement('item');
		$this->writeAttribute($text,$id);
		
		$this->uc++;
		$this->mc++;
		
		return $this;
	}
	
	public function addSubMenu($text,$id=0)
	{
		$this->xml->startElement('item');
		$this->writeAttribute($text,$id);
		
		$this->uc++;
		$this->mc++;
		
		return $this;
	}
	
	public function writeAttribute($text,$id=0)
	{
		$i='mm'.$this->mc;
		if ( $id ) {
			$this->xml->writeAttribute('id',$i.'|'.htmlspecialchars($id));
		} else {
			$this->xml->writeAttribute('id',$i);
		}
		$this->xml->writeAttribute('text',$text);
	}
	
	private function closeElement()
	{
		for ( $k=0;$k<$this->uc;$k++ ) {
			$this->xml->endElement();
		}
		$this->uc=0;
	}
	
	public function end()
	{
		$this->xml->endElement();
		$this->uc--;
		return $this;
	}
	
	public function addSeparator()
	{
		$id='sep'.$this->ns;
		$this->xml->startElement('item');
		$this->xml->writeAttribute('id',$id);
		$this->xml->writeAttribute('type','separator');
		$this->xml->endElement();
		$this->ns++;
		
		return $this;
	}
	
	public function __toString() 
	{
		$this->closeElement();
		$this->xml->endElement(); // close menu
		
		return $this->xml->outputMemory(true);
	}
}