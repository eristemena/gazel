<?php

require_once "Zend/Validate/Abstract.php";

class Gazel_Validate_Age extends Zend_Validate_Abstract
{
		const TOO_YOUNG 				= 'ageTooYoung';
    const TOO_OLD 					= 'ageTooOld';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
    		self::TOO_YOUNG 				=> 'Your age is %age% which is less than minimum allowed (%min% year old)',
        self::TOO_OLD 					=> 'Your age is %age% which is greater than maximum allowed (%max% year old)'
    );

		/**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min',
        'max' => '_max',
        'age'	=> '_age'
    );

    /**
     * Minimum age
     *
     * If null, there is no minimum age
     *
     * @var integer
     */
    protected $_min;

    /**
     * Maximum age
     *
     * If null, there is no maximum age
     *
     * @var integer|null
     */
    protected $_max;

    /**
     * Age
     *
     * @var integer
     */
    protected $_age;

		/**
     * Birth date
     *
     * @var Zend_Date
     */
    protected $_birthDate;

    /**
     * Sets validator options
     *
     * @param  integer|array|Zend_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options     = func_get_args();
            $temp['min'] = array_shift($options);
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }
            $options = $temp;
        }

        if (!array_key_exists('min', $options)) {
            $options['min'] = 0;
        }

        $this->setMin($options['min']);
        if (array_key_exists('max', $options)) {
            $this->setMax($options['max']);
        }
    }

    /**
     * Returns the min option
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  integer $min
     * @throws Zend_Validate_Exception
     * @return Gazel_Validate_Age Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->_max && $min > $this->_max) {
            /**
             * @see Zend_Validate_Exception
             */
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("The minimum age must be less than or equal to the maximum age, but $min >"
                                            . " $this->_max");
        }
        $this->_min = max(0, (integer) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return integer|null
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  integer|null $max
     * @throws Zend_Validate_Exception
     * @return Gazel_Validate_Age Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->_max = null;
        } else if ($max < $this->_min) {
            /**
             * @see Zend_Validate_Exception
             */
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("The maximum age must be greater than or equal to the minimum age, but "
                                            . "$max < $this->_min");
        } else {
            $this->_max = (integer) $max;
        }

        return $this;
    }

		/**
     * Returns the age (year)
     *
     * @return integer
     */
    public function getAge()
    {
    		$year_birth = $this->_birthDate->get(Zend_Date::YEAR);
        $month_birth = $this->_birthDate->get(Zend_Date::MONTH);
        $day_birth = $this->_birthDate->get(Zend_Date::DAY);
        
    		require_once "Zend/Date.php";
        $now=new Zend_Date();
        
        $year_diff = $now->get(Zend_Date::YEAR)-$year_birth;
        $month_diff = $now->get(Zend_Date::MONTH)-$month_birth;
        $day_diff = $now->get(Zend_Date::DAY)-$day_birth;
        
        if ($month_diff < 0) {
        	$age=$year_diff-1;
        } elseif (($month_diff==0) && ($day_diff < 0)) {
        	$age=$year_diff-1;
        } else {
        	$age=$year_diff;
        }
        
        return $age;
    }

    /**
     * Validate to a context
     *
     * @param string|Zend_Date birthdate
     * @param string $part
     * @return boolean
     */
    public function isValid($birthdate)
    {
        if ( $birthdate instanceof Zend_Date )
        {
        	$this->_birthDate=$birthdate;
        }
        
        $this->_age=$this->getAge();
        
        if ( $this->_age < $this->_min )
        {
        	$this->_error(self::TOO_YOUNG);
        	return false;
        }
        
        if ( $this->_age > $this->_max )
        {
        	$this->_error(self::TOO_OLD);
        	return false;
        }
        
        return true;
    }
}