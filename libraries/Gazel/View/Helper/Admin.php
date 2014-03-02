<?php

/**
 * @see Gazel_View_Helper_Abstract
 */
require_once "Gazel/View/Helper/Abstract.php";

/**
 * @category   Gazel
 * @package    Gazel_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2000-2011 PT Inti Artistika Solusitama (http://www.inarts.co.id)
 */
class Gazel_View_Helper_Admin extends Gazel_View_Helper_Abstract
{
	protected $_front;

	public function admin()
	{
		$this->_front   = Zend_Controller_Front::getInstance();

		return $this;
	}

	public function addLink()
	{
		$front   = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();

		return $router->assemble(array('action'=>'add'));
	}

	public function editLink($id)
	{
		$front   = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();

		return $router->assemble(array('action'=>'edit','id'=>$id));
	}

	/**
	 * Construct table header for backend
	 *
	 * @param string $id field name
	 * @param string $title 
	 * @param array $attr attributes
	 */
	public function th($id, $title, $attr=array())
	{
		$sessTblAdmin = new Zend_Session_Namespace('Gazel_Admin_Table');

		$request = $this->_front->getRequest();
		if ($request instanceof Zend_Controller_Request_Abstract) {
			$module     = $request->getModuleName();
			$controller = $request->getControllerName();
		}

		$baseUrl = $this->_front->getBaseUrl();

		// Remove scriptname eg... index.php from baseUrl
		$baseUrl = $this->_removeScriptName($baseUrl);

		if ( $sessid=$sessTblAdmin->ordering[$module][$controller]['id'] ) {
			$order=$sessTblAdmin->ordering[$module][$controller]['type'];
				
			if ( $id==$sessid ) {
				if ( $order=='asc' ) {
					$img='<img src="'.$baseUrl.'/assets/images/arrow_up.gif" border="0"></a>';
				} else {
					$img='<img src="'.$baseUrl.'/assets/images/arrow_down.gif" border="0"></a>';
				}
				$th='<th sort="'.$id.'" order="'.$order.'" class="'.$order.'" '.$this->_getAttr($attr).'>'.htmlspecialchars($title).' '.$img.'</th>';
			} else {
				$img='<img src="'.$baseUrl.'/assets/images/arrow.gif" border="0"></a>';
				$th='<th sort="'.$id.'" '.$this->_getAttr($attr).'>'.htmlspecialchars($title).' '.$img.'</th>';
			}
		} else {
			$img='<img src="'.$baseUrl.'/assets/images/arrow.gif" border="0"></a>';
			$th='<th sort="'.$id.'" '.$this->_getAttr($attr).'>'.htmlspecialchars($title).' '.$img.'</th>';
		}

		return $th;
	}

	public function getOrdering()
	{
		$sessTblAdmin = new Zend_Session_Namespace('Gazel_Admin_Table');

		$request = $this->_front->getRequest();
		if ($request instanceof Zend_Controller_Request_Abstract) {
			$module     = $request->getModuleName();
			$controller = $request->getControllerName();
		}

		$ordering=array();
		if ( $sessid=$sessTblAdmin->ordering[$module][$controller] ) {
			$ordering=$sessid;
		}

		return $ordering;
	}

	public function getSearchParams()
	{
		$sessTblAdmin = new Zend_Session_Namespace('Gazel_Admin_Table');

		$request = $this->_front->getRequest();
		if ($request instanceof Zend_Controller_Request_Abstract) {
			$module     = $request->getModuleName();
			$controller = $request->getControllerName();
		}

		$search=array();
		if ( $sessid=$sessTblAdmin->search[$module][$controller] ) {
			$search=$sessid;
		}

		return $search;
	}

	/**
	 * $fields array array of all field definition, each in array(value,name,rel)
	 */
	public function searchForm($fields)
	{
		//for translate
			$translate= Zend_Registry::get('translate');	
		//
		
		$searchparams = $this->getSearchParams();
		if ( $searchparams['mode'] ) {
			$mode=$searchparams['mode'];
		}else{
			$mode='text';
		}

		$form='<form method="post" action="" id="searchForm">';
		$form.='<select name="fieldname">';
		foreach ( $fields as $r )
		{
			if ( $searchparams['fieldname']==$r[0] ) {
				$form.='<option value="'.htmlspecialchars($r[0]).'" selected="selected" rel="'.$r[2].'">'.htmlspecialchars($r[1]).'</option>';
			} else {
				$form.='<option value="'.htmlspecialchars($r[0]).'" rel="'.$r[2].'">'.htmlspecialchars($r[1]).'</option>';
			}
		}
		$form.='</select>';

		$form.=' <select id="search_criteria" name="criteria">';
		${'sel_'.$searchparams['criteria']}='selected="selected"';
		$form.='<option value="equal" '.$sel_equal.'>'.$translate->_('Equal to').'</option>';
		if ( $mode=='date' )
		{
			$form.='<option value="greater" '.$sel_greater.'>'.$translate->_('Greater than').'</option>';
			$form.='<option value="less" '.$sel_less.'>'.$translate->_('Less than').'</option>';
		}
		else
		{
			$form.='<option value="contains" '.$sel_contains.'>'.$translate->_('Contains').'</option>';
		}
		$form.='</select>';

		$form.=' <input type="text" id="search_keyword" name="keyword" class="keyword" value="'.$searchparams['keyword'].'" />';

		$form.=' <input type="hidden" name="mode" value="'.$mode.'" />';
		$form.=' <input type="submit" name="search" value="Search" class="search" />';
		if ( count($searchparams)>0 ) {
			$form.=' <input type="button" name="clear" value="Clear" class="clearsearch" />';
		}
		$form.='</form>';

		return $form;
	}

	private function _removeScriptName($url)
	{
		if (!isset($_SERVER['SCRIPT_NAME'])) {
			// We can't do much now can we? (Well, we could parse out by ".")
			return $url;
		}

		if (($pos = strripos($url, basename($_SERVER['SCRIPT_NAME']))) !== false) {
			$url = substr($url, 0, $pos);
		}

		return $url;
	}

	/**
	 * Construct attribute from array to key value pair
	 *
	 * @param array $attr attributes
	 */
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