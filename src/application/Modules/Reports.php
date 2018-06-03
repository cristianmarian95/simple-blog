<?php

namespace App\Modules;

use \App\Main;

class Reports extends Main
{
    /**
     * @return mixed
     */
    public function countAllReports(){
        return $this->db->table('reports')->count();
    }

    /**
     * @return mixed
     */
    public function countInactiveReports(){
        return $this->db->table('reports')->where('approved', 0)->count();
    }

    /**
     * @return mixed
     */
    public function getReports(){
        return $this->db->table('reports')->orderBy('approved', 'asc')->orderBy('created_at', 'asc')->get();
    }
}