<?php

/**
 * @see Zend_View_Helper_Abstract
 */
require_once "Zend/View/Helper/Abstract.php";

/**
 * This is a view helper to show a check box
 * 
 * @category   Gazel
 * @package    Gazel_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2000-2011 PT Inti Artistika Solusitama (http://www.inarts.co.id)
 */
class Gazel_View_Helper_Checked extends Zend_View_Helper_Abstract
{
	/**
	 * Adding checked functionalities on view helper
	 * 
	 * @param Boolean $bool
	 * @param Array $arr
	 */
	public function checked($bool,$arr=null)
	{
		if ( is_array($arr) )
		{
			if ( in_array($bool,$arr) ) {
				return 'checked="checked"';
			} else {
				return '';
			}
		}
		else
		{
			if ( $bool ) {
				return 'checked="checked"';
			} else {
				return '';
			}
		}
	}
}