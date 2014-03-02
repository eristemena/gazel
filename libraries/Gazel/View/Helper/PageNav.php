<?php

require_once "Zend/View/Helper/Abstract.php";
require_once "Gazel/Db.php";
require_once "Zend/Controller/Front.php";
require_once "Zend/Config.php";

class Gazel_View_Helper_PageNav extends Zend_View_Helper_Abstract
{
	public function pageNav($section,$attr=array())
	{
		$config=Gazel_Config::getInstance();
		
		$dbi=Gazel_Db::getInstance();
		$db=$dbi->getDb();
		
		$dbselect=$db->select()
			->from(array('p'=>$config->getTableName('page')),array('page_alias','page_title'))
			->joinLeft(array('s'=>$config->getTableName('section')),'p.section_id=s.section_id',array())
			->where('s.section_name=?',$section)
			->where('s.section_name is not null')
			->where('p.page_published=?','y')
			->order('p.page_order asc')
		;
		
		$res=$db->fetchAll($dbselect);
		
		$front=Zend_Controller_Front::getInstance();
		$router=$front->getRouter();
		$request=$front->getRequest();
		
		if ( !$attr['style'] ) {
			$attr['style']='ul-li-a';
		}
		
		if ( !$attr['a-active'] ) {
			$attr['a-active']=array('class'=>'active');
		}
		
		if ( $attr['style']=='ul-li-a' || $attr['style']=='ul-li-a-span' )
		{
			$out='<ul '.$this->_getAttr($attr['ul-attribs']).'>';
			foreach ( $res as $r )
			{
				if ( $request->getParam('alias')==$r['page_alias'] ) {
					if( isset($attr['li-active']) ){
						$out.='<li '.$this->_getAttr($attr['li-attribs']).' '.$this->_getAttr($attr['li-active']).'>';
					}else{
						$out.='<li '.$this->_getAttr($attr['li-attribs']).'>';
					}

					if ( $attr['a-attribs']['class'] ) {
						$a_attribs=array('class' => $attr['a-attribs']['class'].' '.$attr['a-active']['class']);
					} else {
						$a_attribs=array('class' => $attr['a-active']['class']);
					}
				} else {
					$out.='<li '.$this->_getAttr($attr['li-attribs']).'>';

					if ( $attr['a-attribs'] ) {
						$a_attribs=$attr['a-attribs'];
					} else {
						$a_attribs=array();
					}
				}
				$a_attribs=$this->_getAttr($a_attribs);
				
				if ( $config->configinstance->mu->active=="true" ) {
					$href=$router->assemble(array('alias'=>$r['page_alias'],'username'=>$request->getParam('username')),'page',true);
				} else {
					$href=$router->assemble(array('alias'=>$r['page_alias']),'page',true);
				}
				
				if ( $attr['style']=='ul-li-a-span' ) {
					$desc='<span>'.htmlspecialchars($r['page_title']).'</span>';
				} else {
					$desc=htmlspecialchars($r['page_title']);
				}
				
				$out.='<a '.$a_attribs.' href="'.$href.'">'.$desc.'</a>';
				
				$out.='</li>';
			}
			$out.='</ul>';
		}
		elseif ( $attr['style']=='a-|' )
		{
			$o=array();
			foreach ( $res as $r )
			{
				if ( $config->configinstance->mu->active=="true" ) {
					$href=$router->assemble(array('alias'=>$r['page_alias'],'username'=>$request->getParam('username')),'page',true);
				} else {
					$href=$router->assemble(array('alias'=>$r['page_alias']),'page',true);
				}
				$desc=htmlspecialchars($r['page_title']);
				
				if ( $request->getParam('alias')==$r['page_alias'] ) {
					$a_attribs=$this->_getAttr($attr['a-active']);
				} else {
					$a_attribs='';
				}
				
				$o[]='<a '.$a_attribs.' href="'.$href.'">'.$desc.'</a>';
			}
			
			$out=implode(' | ',$o);
		}
		
		return $out;
	}
	
	protected function _getAttr($attr)
	{
		if ( $attr===null ) {
			return '';
		} else {
			$latr=array();
			foreach ( $attr as $k => $v )
			{
				$latr[]="$k=\"$v\"";
			}
			
			return implode(' ',$latr);
		}
	}
	
}