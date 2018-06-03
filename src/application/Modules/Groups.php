<?php


namespace App\Modules;

use \App\Main;

class Groups extends Main
{
    /**
     * @return mixed
     */
    public function getGroups(){
        return $this->db->table('groups')->orderBy('permission', 'desc')->get();
    }
}