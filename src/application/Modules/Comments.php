<?php


namespace App\Modules;

use \App\Main;

class Comments extends Main
{
    /**
     * @return mixed
     */
    public function countAllComments(){
        return $this->db->table('post_comments')->count();
    }

    /**
     * @return mixed
     */
    public function countInactiveComments(){
        return $this->db->table('post_comments')->where('approved', 0)->count();
    }

    /**
     * @param $days
     * @return mixed
     */
    public function stats($days){
        $dateBegin = date('Y-m-d 00:00:00', strtotime("-" . $days . "days"));
        $dateEnd = date('Y-m-d 23:59:59', strtotime("-" . $days . "days"));
        return $this->db->table('post_comments')->whereBetween('created_at',[$dateBegin, $dateEnd])->count();
    }

    /**
     * @return mixed
     */
    public function getComments(){
        return $this->db->table('post_comments')->where('approved', 0)->orderBy('created_at', 'asc')->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPostComments($id){
        return $this->db->table('post_comments')->where('post_id', $id)->where('approved', 1)->orderBy('created_at', 'asc')->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function count($id){
        return $this->db->table('post_comments')->where('approved', 1)->where('post_id', $id)->count();
    }

    public function countUserComments($uid){
        return $this->db->table('post_comments')->where('user_id', $uid)->count();
    }
}

