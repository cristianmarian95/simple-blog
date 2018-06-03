<?php


namespace App\Modules;

use \App\Main;

class Session extends Main
{
    /**
     * @param $name
     * @param $value
     * @return null
     */
    public function set($name, $value){
        if(!isset($_SESSION[$name])){
            return $_SESSION[$name] = $value;
        }
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name){
        if(isset($_SESSION[$name])){
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    public function del($name){
        if(isset($_SESSION[$name])){
            unset($_SESSION[$name]);
        }
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function is_set($name){
        return isset($_SESSION[$name]);
    }
}