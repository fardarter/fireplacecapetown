<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/** 
 * Object Registry 
 * 
 */ 
class BASEL_Registry { 
    /** 
     * Holds an instance of the class 
     */ 
    private static $instance; 

    /** 
     * Short names of some know objects 
     * 
     * @var array 
     */ 
    private $knownObjects = array(); 
     
    /** 
     * Restrict direct initialization, use Registry::getInstance() instead 
     */ 
    private function __construct() { 
    } 

    /** 
     * Get instance of the object (the singleton method) 
     *  
     * @return  Registry 
     */ 
    public static function getInstance() { 
        if (!isset(self::$instance)) { 
            self::$instance = new self; 
        } 

        return self::$instance; 
    } 


    /** 
     * Dynamically load missing object and assign it to the Registry property 
     * 
     * @param   string $obj Object name (first char will be converted to upper case) 
     * @return  object 
     */ 
    function __get($obj) { 
        if( !isset( $this->knownObjects[ $obj ] ) ) {
            try {
                $this->_save_object( $obj );
            } catch( Exception $e ) {
                echo $e->getTraceAsString();
            }
        }

        return $this->knownObjects[ $obj ];
    } 

    private function _save_object( $obj ) {
        $objname = 'BASEL_' . ucfirst($obj);
        // echo $objname . '<br>';
        if( is_string($obj) && !isset($this->$obj) && class_exists($objname) ) {
            $this->knownObjects[ $obj ] = new $objname;
        }
    }

    /** 
     * Prevent users to clone the instance 
     */ 
    public function __clone() { 
        trigger_error('Clone is not allowed.', E_USER_ERROR); 
    } 
} 

if( ! function_exists( 'BASEL_Registry' ) ) {
	function BASEL_Registry() {
		return BASEL_Registry::getInstance();
	}
}