<?php


namespace App\Modules;

use App\Main;

class Post extends Main
{
    /**
     * @return mixed
     */
    public function countAllPosts(){
        return $this->db->table('posts')->count();
    }

    /**
     * @return mixed
     */
    public function getPosts() {
        return $this->db->table('posts')->orderBy('category_id', 'asc')->get();
    }

    /**
     * @return mixed
     */
    public function getPostsList() {
        return $this->db->table('posts')->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return mixed
     */
    public function getMostCommentPosts() {
        return $this->db->table('posts')->select(['posts.*', $this->db->table('post_comments')->raw('COUNT(post_comments.post_id) as total')])
            ->leftJoin('post_comments', 'post_comments.post_id','posts.id')
            ->groupBy('posts.id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPost($id) {
        return $this->db->table('posts')->where('id', $id)->first();
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function count($uid){
        return $this->db->table('posts')->where('user_id', $uid)->count();
    }


}