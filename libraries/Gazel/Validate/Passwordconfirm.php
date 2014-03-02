<?php

require_once "Zend/Validate/Abstract.php";

class Gazel_Validate_Passwordconfirm extends Zend_Validate_Abstract
{
    /**
     * Validation key for not equal
     *
     */
    const NOT_SAME = 'notSame';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME => 'The passwords you typed are not the same',
    );

    /**
     * Field to validate with
     *
     * @var string
     */
    protected $_field;

    /**
     * Context
     *
     * @var string|array
     */
    protected $_context;

    /**
     * Construct
     *
     */
    public function __construct($field, $context = null)
    {
        $this->_field   = $field;
        $this->_context = $context;
    }

    /**
     * Validate to a context
     *
     * @param string $value
     * @param array|string $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        // Set value
        $this->_setValue($value);

        if ($context === null && $this->_context === null) {
            /**
             * @see Gazel_Validate_Exception
             */
            require_once 'Gazel/Validate/Exception.php';
            throw new Gazel_Validate_Exception(sprintf(
                'Validator "%s" contexts is not setup', get_class($this)
            ));
        }

        // Use instance context if not provided
        $context = ($context === null) ? $this->_context : $context;

        // Validate string
        if (is_string($context) && $value == $context) {
             return true;
        }

        // Validate from array
        if (is_array($context) && isset($context[$this->_field])
            && $value == $context[$this->_field]) {
            return true;
        }

        $this->_error(self::NOT_SAME);
        return false;
    }
}