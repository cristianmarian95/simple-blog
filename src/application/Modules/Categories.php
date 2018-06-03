<?php


namespace App\Modules;

use \App\Main;

class Categories extends Main
{
    /**
     * @param $category_id
     * @return mixed
     */
    public function getCategoryName($category_id){
        $category = $this->db->table('categories')->where('id', $category_id)->first();
        return $category->name;
    }

    /**
     * @return mixed
     */
    public function getCategories() {
        return $this->db->table('categories')->get();
    }
}