<?php

namespace App\Controllers;

use App\Main;

class Blog extends Main
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function index($request, $response, $args){
       return $this->view->render($response, 'blog/index.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function read($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('index');
        }
        $post = $this->db->table('posts')->where('id', $args['id'])->first();
        if(!$post){
            return $this->redirect('index');
        }
        return $this->view->render($response, 'blog/read.twig', ['id' => $post->id]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function login($request, $response, $args){
        return $this->view->render($response, 'blog/login.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function register($request, $response, $args){
        return $this->view->render($response, 'blog/register.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function contact($request, $response, $args){
        return $this->view->render($response, 'blog/contact.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function recover($request, $response, $args){
        return $this->view->render($response, 'blog/recover.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function profile($request, $response, $args){
        return $this->view->render($response, 'blog/profile.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function logout($request, $response, $args){
        $this->session->del('uid');
        $this->flash->addMessage('success', 'You successfully disconnected!');
        return $this->redirect('login');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function subscribe($request, $response, $args){
        return $this->view->render($response, 'blog/subscribe.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function category($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('index');
        }
        $cat = $this->db->table('categories')->where('id', $args['id'])->first();
        if(!$cat){
            return $this->redirect('index');
        }
        $posts = $this->db->table('posts')->where('category_id', $cat->id)->orderBy('created_at', 'desc')->get();
        $numberOfPosts = $this->db->table('posts')->where('category_id', $cat->id)->count();
        return $this->view->render($response, 'blog/category.twig', ['posts' => $posts, 'numberOfPosts' => $numberOfPosts]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function active($request, $response, $args){
        if(!isset($args['key'])){
            return $this->redirect('index');
        }
        $user = $this->db->table('verifications')->where('hash', $args['key'])->where('validate', '0')->first();
        if($user){
            $this->db->table('users')->where('id', $user->user_id)->update(['validate' => 1]);
            $this->db->table('verifications')->where('id', $user->id)->where('validate', '0')->update(['validate' => 1]);
            $this->flash->addMessage('success', 'Your account was successfully confirmed');
            return $this->redirect('login');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function recoverChangePassword($request, $response, $args){
        if(!isset($args['hash'])){
            return $this->redirect('index');
        }
        $recover = $this->db->table('recovers')->where('hash', $args['hash'])->where('validate', 0)->first();
        if(!$recover){
            return $this->redirect('index');
        }
        return $this->view->render($response, 'blog/recoverPassword.twig', ['hash' => $args['hash']]);
    }

    public function report($request, $response, $args){
        return $this->view->render($response, 'blog/report.twig');
    }
}