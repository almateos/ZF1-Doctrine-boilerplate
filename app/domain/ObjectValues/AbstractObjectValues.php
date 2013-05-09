<?php
/**
 * @author NMO <nico@multeegaming.com> 
 */
namespace ObjectValues;

abstract class AbstractObjectValues 
{
    /**
     * @static
     * @param $id
     * @return string|null
     */
    public static function getNameById($id) {
        return array_key_exists($id, static::$_names) ? static::$_names[$id] : null;
    }

    /**
     * @static
     * @param $id
     * @return string|null
     */
    public static function getIdByName($name) {
        $tmp =  array_flip(static::$_names);
        return $tmp[$name];
    }

    /**
     * @static
     * @return array
     */
    public static function getConstants() {
        return static::$_names; 
    }
}
