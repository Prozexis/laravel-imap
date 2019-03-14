<?php
/*
* File: Mask.php
* Category: Mask
* Author: M.Goldenbaum
* Created: 14.03.19 20:49
* Updated: -
*
* Description:
*  -
*/

namespace Webklex\IMAP\Support\Masks;

use Webklex\IMAP\Exceptions\MethodNotFoundException;

/**
 * Class Mask
 *
 * @package Webklex\IMAP\Support\Masks
 */
class Mask {

    /**
     * @var array $attributes
     */
    protected $attributes = [];

    /**
     * @var object $parent
     */
    protected $parent;

    /**
     * Mask constructor.
     * @param $parent
     */
    public function __construct($parent) {
        $this->parent = $parent;

        if(method_exists($this->parent, 'getAttributes')){
            $this->attributes = array_merge($this->attributes, $this->parent->getAttributes());
        }
    }


    /**
     * Call dynamic attribute setter and getter methods and inherit the parent calls
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     * @throws MethodNotFoundException
     */
    public function __call($method, $arguments) {
        if(strtolower(substr($method, 0, 3)) === 'get') {
            $name = snake_case(substr($method, 3));

            if(isset($this->attributes[$name])) {
                return $this->attributes[$name];
            }

        }elseif (strtolower(substr($method, 0, 3)) === 'set') {
            $name = snake_case(substr($method, 3));

            if(isset($this->attributes[$name])) {
                $this->attributes[$name] = array_pop($arguments);

                return $this->attributes[$name];
            }

        }

        if(method_exists($this->parent, $method) === true){
            return call_user_func_array([$this->parent, $method], $arguments);
        }

        throw new MethodNotFoundException("Method ".self::class.'::'.$method.'() is not supported');
    }

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function __set($name, $value) {
        $this->attributes[$name] = $value;

        return $this->attributes[$name];
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name) {
        if(isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getAttributes(){
        return $this->attributes;
    }

}