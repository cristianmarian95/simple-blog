<?php

namespace App\Middlewares;

use \App\Main;

class Account extends Main
{
    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function isLog($request, $response, $next){
        if(!$this->session->is_set('uid')){
            return $this->redirect('index');
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function isAdmin($request, $response, $next){
        $permission = $this->user->getPermission($this->session->get('uid'));
        if($permission < 2){
            return $this->redirect('index');
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function logged($request, $response, $next){
        if($this->session->is_set('uid')){
            return $this->redirect('index');
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function isSuperAdmin($request, $response, $next){
        $permission = $this->user->getPermission($this->session->get('uid'));
        if($permission != 3){
            $this->flash->addMessage('message', 'You do not have permission to do that!');
            return $this->redirect('adminHome');
        }
        $response = $next($request, $response);
        return $response;
    }
}