<?php


namespace App\Modules;

use App\Main;

class User extends Main
{
    /**
     * @return mixed
     */
    public function countAllUsers(){
        return $this->db->table('users')->count();
    }

    /**
     * @return mixed
     */
    public function countInactiveUsers() {
        return $this->db->table('users')->where('approved', 0)->count();
    }

    /**
     * @return mixed
     */
    public function usersList(){
        return $this->db->table('users')->orderBy('created_at', 'asc')->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAccount($id){
        return $this->db->table('users')->where('id', $id)->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getProfile($id){
        return $this->db->table('users_profiles')->where('user_id', $id)->first();
    }

    /**
     * @param $days
     * @return mixed
     */
    public function stats($days){
        $dateBegin = date('Y-m-d 00:00:00', strtotime("-" . $days . "days"));
        $dateEnd = date('Y-m-d 23:59:59', strtotime("-" . $days . "days"));
        return $this->db->table('users')->whereBetween('created_at',[$dateBegin, $dateEnd])->count();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserGroup($id){
        $group = $this->db->table('users_groups')->where('user_id', $id)->first();
        return $this->db->table('groups')->where('id', $group->group_id)->first();
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getPermission($uid){
        $userGroup = $this->db->table('users_groups')->where('user_id', $uid)->first();
        $group = $this->db->table('groups')->where('id', $userGroup->group_id)->first();
        return $group->permission;
    }

}