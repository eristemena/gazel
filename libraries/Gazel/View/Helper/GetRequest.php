<?php

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

class Gazel_View_Helper_GetRequest
{
    /**
     * Get the request object
     *
     * @return Zend_Controller_Request_Abstract|null
     */
    public function getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }
}