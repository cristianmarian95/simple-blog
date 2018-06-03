<?php


namespace App\Controllers;

use App\Main;

class Admin extends Main
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function index($request, $response, $args){
        return $this->view->render($response, 'acp/index.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function people($request, $response, $args){
        return $this->view->render($response, 'acp/people.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function userProfile($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminPeople');
        }
        return $this->view->render($response, 'acp/userProfile.twig', ['uid' => $args['id']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function createPost($request, $response, $args){
        return $this->view->render($response, 'acp/post.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function createCategory($request, $response, $args){
        return $this->view->render($response, 'acp/category.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function posts($request, $response, $args){
        return $this->view->render($response, 'acp/posts.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function editPost($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('posts');
        }
        return $this->view->render($response, 'acp/edit.twig', ['post_id' => $args['id']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function reports($request, $response, $args){
        return $this->view->render($response, 'acp/reports.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function comments($request, $response, $args){
        return $this->view->render($response, 'acp/comments.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function profile($request, $response, $args){
        return $this->view->render($response, 'acp/profile.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function settings($request, $response, $args){
        return $this->view->render($response, 'acp/settings.twig');
    }
}