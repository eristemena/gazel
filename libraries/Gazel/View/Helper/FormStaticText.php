<?php

/** Zend_View_Helper_FormElement **/
require_once 'Zend/View/Helper/FormElement.php';

class Zend_View_Helper_FormStaticText extends Zend_View_Helper_FormElement
{
    public function formStaticText($name, $value = null, array $attribs = array())
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable, escape

        // build the element
        if ($disable) {
            // disabled; display nothing
            return  '';
        }

        $value = ($escape) ? $this->view->escape($value) : $value;
        $for   = (empty($attribs['disableFor']) || !$attribs['disableFor'])
               ? ' for="' . $this->view->escape($id) . '"'
               : '';
        if (array_key_exists('disableFor', $attribs)) {
            unset($attribs['disableFor']);
        }

        // enabled; display label
        $xhtml = '<strong'
                . $for
                . $this->_htmlAttribs($attribs)
                . '>' . $value . '</strong>';

        return $xhtml;
    }
}
