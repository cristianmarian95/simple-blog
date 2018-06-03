<?php


namespace App\Modules;

use \App\Main;

class Settings extends Main
{
    /**
     * @param $name
     * @return null
     */
    public function get($name){
        $config = $this->db->table('configs')->where('name', $name)->first();
        if($config){
            return $config->value;
        }
        return false;
    }
}