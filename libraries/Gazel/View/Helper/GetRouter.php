<?php

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

class Gazel_View_Helper_GetRouter
{
    /**
     * Get the request object
     *
     * @return Zend_Controller_Request_Abstract|null
     */
    public function getRouter()
    {
        return Zend_Controller_Front::getInstance()->getRouter();
    }
}