<?php


namespace App\Controllers;

use App\Main;

class BlogActions extends Main
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function login($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages([
            'required' =>  'You need to enter the {field} to continue!',
            'email' => 'You need to enter a valid email!'
        ]);
        $this->v->validate([
            'email' => [$data['email'], 'required|email'],
            'password' => [$data['password'], 'required']
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('login');
        }
        $user = $this->db->table('users')->where('email', $data['email'])->first();
        if(!$user){
            $this->flash->addMessage('danger', 'The email address do not exists!');
            return $this->redirect('login');
        }
        if(!password_verify($data['password'], $user->password)) {
            $this->flash->addMessage('danger', 'The password is incorrect!');
            return $this->redirect('login');
        }
        if(!$user->approved){
            $this->flash->addMessage('danger', 'The account is not yet active!');
            return $this->redirect('login');
        }
        $this->session->set('uid', $user->id);
        return $this->redirect('profile');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function register($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages([
            'required' =>  'You need to enter the {field} to continue!',
            'email' => 'You need to enter a valid email!',
            'matches' => 'The passwords do not match!'
        ]);
        $this->v->validate([
            'username' => [$data['username'], 'required'],
            'email' => [$data['email'], 'required|email'],
            'password' => [$data['password'], 'required'],
            're-password' => [$data['re-password'], 'required|matches(password)']
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('register');
        }
        $email = $this->db->table('users')->where('email', $data['email'])->first();
        if($email){
            $this->flash->addMessage('danger', 'The email address is already used!');
            return $this->redirect('register');
        }
        $username = $this->db->table('users')->where('username', $data['username'])->first();
        if($username){
            $this->flash->addMessage('danger', 'The username is already used!');
            return $this->redirect('register');
        }
        if($this->config->get('blogEnableAutoConfirmAccount')){
            $approved = true;
        }else{
            $approved = false;
        }
        $this->db->table('users')->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'approved' => $approved,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $encoded_image = base64_encode(file_get_contents($request->getUri()->getBaseUrl() . '/uploads/avatars/default.png'));
        $img = 'data:image/png;base64,' . $encoded_image;
        $user = $this->db->table('users')->where('username', $data['username'])->first();
        $this->db->table('users_profiles')->insert([
            'user_id' => $user->id,
            'avatar' => $img
        ]);
        $this->db->table('users_groups')->insert(['user_id' => $user->id , 'group_id' => 3]);
        if(!$this->config->get('blogEnableAutoConfirmAccount')) {
            $this->db->table('verifications')->insert([
                'hash' => md5($email),
                'user_id' => $user->id,
                'validate' => 0,
            ]);
            $this->mail->sendActivation($request, $email);
            $this->flash->addMessage('info', 'Please confirm your email address!');
        }
        $this->flash->addMessage('success', 'Your account has been successfully registered!');
        return $this->redirect('register');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function recover($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->validate(['email' => [$data['email'], 'required|email']]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errros()->first());
            return $this->redirect('recover');
        }
        $user = $this->db->table('users')->where('email', $data['email'])->first();
        if(!$user){
            $this->flash->addMessage('danger', 'The account is not registered!');
            return $this->redirect('recover');
        }
        $hash = md5(date('H:i:s'));
        $this->db->table('recovers')->insert([
            'user_id' => $user->id,
            'hash' => $hash,
            'validate' => 0,
        ]);
        $this->mail->sendRecoverMail($request, $hash, $user->email);
        $this->flash->addMessage('success', 'Check your email address for more details!');
        return $this->redirect('recover');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function comment($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to enter the {field} to continue!']);
        $this->v->validate([
            'post_id' => [$data['post_id'], 'required'],
            'content' => [$data['content'], 'required|max(400)'],
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $response->withRedirect($this->router->pathFor('read', ['id' => $data['post_id']]));
        }
        if($this->user->getPermission($this->session->get('uid')) >= 2){
            $approved = true;
            $this->flash->addMessage('success', 'Your comment has been sent successfully!');
        }else if($this->config->get('blogEnableAutoConfirmComment')){
            $approved = true;
            $this->flash->addMessage('success', 'Your comment has been sent successfully!');
        }else{
            $approved = false;
            $this->flash->addMessage('success', 'Your comment has been sent successfully!');
            $this->flash->addMessage('info', 'Your comment will be reviewed by an admin and it will be displayed!');
        }

        $this->db->table('post_comments')->insert([
            'user_id' => $this->session->get('uid'),
            'post_id' => $data['post_id'],
            'content' => $data['content'],
            'approved' => $approved,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $response->withRedirect($this->router->pathFor('read', ['id' => $data['post_id']]));
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updateProfile($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'email' => [$data['email'], 'required|email'],
            'name' => [$data['name'], 'required'],
            'last_name' => [$data['last_name'], 'required'],
            'country' => [$data['country'], 'required'],
            'city' => [$data['city'], 'required'],
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('profile');
        }
        $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();
        if($data['email'] != $user->email){
            $email = $this->db->table('users')->where('email', $data['email'])->first();
            if($email){
                $this->flash->addMessage('danger', 'The email address is already used');
                return $this->redirect('profile');
            }
        }
        $this->db->table('users')->where('id', $this->session->get('uid'))->update(['email' => $data['email']]);
        $this->db->table('users_profiles')->where('user_id', $this->session->get('uid'))->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'city' => $data['city']
        ]);
        $this->flash->addMessage('success', 'Your profile has been updated');
        return $this->redirect('profile');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updateAvatar($request, $response, $args){
        $image = $request->getUploadedFiles()['avatar'];
        if($image->getError() != UPLOAD_ERR_OK){
            $this->flash->addMessage('danger', 'Please select an image');
            return $this->redirect('profile');
        }else{
            if($image->getSize() > 3000000){
                $this->flash->addMessage('danger', 'The image is to large. Max size 3M');
                return $this->redirect('profile');
            }
            $encoded_image = base64_encode(file_get_contents($image->getStream()->getMetadata('uri')));
            $data = 'data:' . $image->getClientMediaType() . ';base64,' . $encoded_image;
            $this->db->table('users_profiles')->where('user_id', $this->session->get('uid'))->update(['avatar' => $data]);
            $this->flash->addMessage('success', 'Your profile avatar has been updated');
            return $this->redirect('profile');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updatePassword($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->validate([
            'old_password|old password' => [$data['old_password'], 'required'],
            'new_password|new password' => [$data['new_password'], 'required'],
            'conf_new_password|confirm the password' => [$data['conf_new_password'], 'required|matches(new_password)']
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('profile');
        }
        $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();
        if(!password_verify($data['old_password'], $user->password)){
            $this->flash->addMessage('danger', 'The current password is incorrect!');
            return $this->redirect('profile');
        }
        $this->db->table('users')->where('id', $user->id)->update(['password' => password_hash($data['new_password'], PASSWORD_DEFAULT)]);
        $this->flash->addMessage('success', 'Your password has been changed!');
        return $this->redirect('profile');
    }


    /***
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deleteProfile($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate(['uid' => [$data['uid'], 'required']]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('profile');
        }
        if($data['uid'] != $this->session->get('uid')){
            $this->flash->addMessage('danger', 'Unexpected error!');
            return $this->redirect('profile');
        }
        $this->db->table('users')->where('id', $data['uid'])->delete();
        $this->session->del('uid');
        $this->flash->addMessage('success', 'Your account has been deleted!');
        return $this->redirect('login');
    }


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function subscribe($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to enter the email address to continue!']);
        $this->v->validate(['email' => [$data['email'], 'required|email']]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('subscribe');
        }
        $email = $this->db->table('subscribers')->where('email', $data['email'])->first();
        if($email){
            $this->flash->addMessage('danger', 'You are already subscribed to this blog!');
            return $this->redirect('subscribe');
        }
        $this->db->table('subscribers')->insert(['email' => $data['email']]);
        $this->flash->addMessage('success', 'Thanks you for subscribing to our blog!');
        return $this->redirect('subscribe');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function submitContact($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to enter the {field} to continue!']);
        $this->v->validate([
            'subject' => [$data['subject'], 'required'],
            'email' => [$data['email'], 'required|email'],
            'message' => [$data['message'], 'required']
        ]);

        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('contact');
        }

        if($this->mail->sendContactMail($data['email'], $data['subject'], $data['message'])){
            $this->flash->addMessage('success', 'Your contact message was sent. We will contact you as soon as we can!');
            return $this->redirect('contact');
        }else{
            $this->flash->addMessage('danger', 'Unexpected error. Please try again!');
            return $this->redirect('contact');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function recoverPassword($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->validate([
            'password' => [$data['password'], 'required'],
            'conf_password' => [$data['conf_password'], 'required|matches(password)'],
            'hash' => [$data['hash'], 'required']
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $response->withRedirect($this->router->pathFor('recoverPassword', ['hash' => $data['hash']]));
        }
        $recover = $this->db->table('recovers')->where('hash', $data['hash'])->where('validate', 0)->first();
        if(!$recover){
            $this->flash->addMessage('danger', 'Unexpected error. Please try again!');
            return $response->withRedirect($this->router->pathFor('recoverPassword', ['hash' => $data['hash']]));
        }
        $this->db->table('users')->where('id', $recover->user_id)->update(['password' => password_hash($data['password'], PASSWORD_DEFAULT)]);
        $this->db->table('recovers')->where('id', $recover->id)->update(['validate' => 1]);
        $this->flash->addMessage('success', 'Your password has been successfully changed!');
        return $this->redirect('login');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function actionReport($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->validate([
            'link' => [$data['link'], 'required|url'],
            'email' => [$data['email'], 'required|email'],
            'message' => [$data['message'], 'required']
        ]);
        if(!$this->v->passes()){
            $this->flash->addMessage('danger', $this->v->errors()->first());
            return $this->redirect('report');
        }
        $this->db->table('reports')->insert([
            'link' => $data['link'],
            'email' => $data['email'],
            'content' => $data['message'],
            'approved' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $this->flash->addMessage('success', 'Thanks your for your report. We will look in to the problem as soon as we can');
        return $this->redirect('report');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function search($request, $response, $args){
        $data = $data = $request->getParsedBody();
        $this->v->validate(['search' => [$data['search'], 'required'],]);
        if(!$this->v->passes()){
            return $this->redirect('index');
        }
        $posts = $this->db->table('posts')->where('title', 'LIKE', '%' . $data['search'] . '%')->orWhere('content', 'LIKE', '%' . $data['search'] . '%')->get();
        return $this->view->render($response, 'blog/search.twig', ['posts' => $posts]);
    }
}