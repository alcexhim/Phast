<?php
    namespace Phast;
    
    /**
     * Provides the base class for a full-featured Enumeration in PHP.
     *
     * @author Brian Cline, Robert Harvey @ stackoverflow.com
     * @link http://stackoverflow.com/questions/254514/php-and-enumerations
     */
    class Enumeration
    {
        private static $constCacheArray = NULL;
        
        public static function GetValues()
        {
            if (self::$constCacheArray == NULL)
            {
                self::$constCacheArray = array();
            }
            $calledClass = get_called_class();
            if (!array_key_exists($calledClass, self::$constCacheArray))
            {
                $reflect = new ReflectionClass($calledClass);
                self::$constCacheArray[$calledClass] = $reflect->getConstants();
            }
            return self::$constCacheArray[$calledClass];
        }
    
        /**
         * Determines if this enumeration contains the specified name.
         * @param string $name The name of the enum value to search for.
         * @param boolean $caseSensitive True if the search should be case-sensitive; false otherwise.
         */
        public static function ContainsName($name, $caseSensitive = false)
        {
            $constants = self::GetValues();
            
            if ($caseSensitive)
            {
                return array_key_exists($name, $constants);
            }
            
            $keys = array_map('strtolower', array_keys($constants));
            return in_array(strtolower($name), $keys);
        }
        /**
         * Determines if this enumeration contains the specified value.
         * @param unknown $value The enum value to search for.
         */
        public static function ContainsValue($value)
        {
            $values = array_values(self::GetValues());
            return in_array($value, $values, $strict = true);
        }
    }
?>