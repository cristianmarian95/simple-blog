<?php


namespace App\Modules;

use \App\Main;

class Functions extends Main
{
    /**
     * @param $day
     * @return false|string
     */
    public function printDate($day) {
        $date = date('d-m-Y', strtotime("-" . $day . "days"));
        return $date;
    }

    /**
     * @return mixed
     */
    public function getCategories(){
        return $this->db->table('categories')->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function generateName() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . ".png";
    }

    /**
     * @param $date
     * @return false|string
     */
     public function convertDate($date){
        return date("F j, Y G:i", strtotime($date));
     }
}