<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Db.php";
require_once "Zend/Controller/Front.php";

class Gazel_View_Helper_GetSection extends Zend_View_Helper_Abstract
{
	public function getSection($section)
	{
		$dbi=Gazel_Db::getInstance();
		$db=$dbi->getDb();
		
		$dbselect=$db->select()
			->from(array('p'=>'page'),array('page_alias','page_title'))
			->joinLeft(array('s'=>'section'),'p.section_id=s.section_id',array())
			->where('s.section_name=?',$section)
			->where('s.section_name is not null')
			->where('p.page_published=?','y')
			->order('p.page_order asc')
		;
		
		$res=$db->fetchAll($dbselect);
		
		$front=Zend_Controller_Front::getInstance();
		$router=$front->getRouter();
		$request=$front->getRequest();
		$sections=array();
		foreach ( $res as $r )
		{
			if ( $request->getParam('alias')==$r['page_alias'] ) {
				$status='active';
			} else {
				$status='';
			}
			
			$sections[]=array(
				'title'	=> $r['page_title'],
				'href'	=> $router->assemble(array('alias'=>$r['page_alias']),'page',true),
				'status'=> $status
			);
		}
		
		return $sections;
	}
}