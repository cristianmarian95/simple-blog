<?php


namespace App\Controllers;

use App\Main;

class AdminActions extends Main
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deleteUser($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminPeople');
        }
        $user = $this->db->table('users')->where('id', $args['id'])->first();
        if($user){
            if($user->username == 'admin'){
                $this->flash->addMessage('message', 'You can not delete the master admin!');
                return $this->redirect('adminPeople');
            }
            if($this->session->get('uid') == $user->id){
                $this->db->table('users')->where('id', $user->id)->delete();
                $this->session->del('uid');
                $this->flash->addMessage('success', 'Your account has been deleted!');
                return $this->redirect('login');
            }
            if($this->user->getPermission($this->session->get('uid')) != 3){
                $this->flash->addMessage('message', 'You do not have permission to delete another user!');
                return $this->redirect('adminPeople');
            }
            $this->db->table('users')->where('id', $user->id)->delete();
            $this->flash->addMessage('message', 'The user ' . $user->username . ' have been delete!');
            return $this->redirect('adminPeople');
        }
        return $this->redirect('adminPeople');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updateUser($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'permission' => [$data['permission'], "required"],
            'uid' => [$data['uid'], "required"]
        ]);

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $response->withRedirect($this->router->pathFor('AdminUserProfile', ['id' => $data['uid']]));
        }

        $user = $this->db->table('users')->where('id', $data['uid'])->first();
        if(!$user){
            $this->flash->addMessage('message', 'The user do not exists!');
            return $response->withRedirect($this->router->pathFor('adminUserProfile', ['id' => $data['uid']]));
        }

        $this->db->table('users_groups')->where('user_id', $user->id)->update(['group_id' => $data['permission']]);

        $this->flash->addMessage('message', 'The user information have been updated!');
        return $response->withRedirect($this->router->pathFor('adminUserProfile', ['id' => $data['uid']]));

    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function createPost($request, $response, $args){
        $data = $request->getParsedBody();
        $image = $request->getUploadedFiles()['img'];

        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'title' => [$data['title'], "required"],
            'category' => [$data['category'], "required"],
            'content' => [$data['content'], "required"],
        ]);

        if($image->getError() != UPLOAD_ERR_OK){
            $encoded_image = base64_encode(file_get_contents($request->getUri()->getBaseUrl() . '/uploads/posts/default.png'));
            $img = 'data:image/png;base64,' . $encoded_image;
        }else{
            if($image->getSize() > 5000000){
                $this->flash->addMessage('message', 'The image is to large. Max size 5M');
                return $this->redirect('adminGetCreate');
            }
            $encoded_image = base64_encode(file_get_contents($image->getStream()->getMetadata('uri')));
            $img = 'data:' . $image->getClientMediaType() . ';base64,' . $encoded_image;
        }

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $this->redirect('adminGetCreate');
        }

        $this->db->table('posts')->insert([
            'category_id' => $data['category'],
            'user_id' => $this->session->get('uid'),
            'title' => $data['title'],
            'header_img' => $img,
            'content' => $data['content'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $post = $this->db->table('posts')->where('title', $data['title'])->first();
        $this->mail->subscribeMail($request, $post->title, $post->id);
        $this->flash->addMessage('message', 'The post has been created!');
        return $this->redirect('adminGetCreate');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function editPost($request, $response, $args){
        $data = $request->getParsedBody();
        $image = $request->getUploadedFiles()['img'];

        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'title' => [$data['title'], "required"],
            'category' => [$data['category'], "required"],
            'content' => [$data['content'], "required"],
            'post_id' => [$data['post_id'], "required"]
        ]);

        $post = $this->db->table('posts')->where('id', $data['post_id'])->first();
        if(!$post){
            $this->flash->addMessage('message', 'Unexpected error!');
            return $response->withRedirect($this->router->pathFor('editPost', ['id' => $data['post_id']]));
        }
        if($image->getError() != UPLOAD_ERR_OK){
            $img = $post->header_img;
        }else{
            if($image->getSize() > 5000000){
                $this->flash->addMessage('message', 'The image is to large. Max size 5M');
                return $this->redirect('adminGetCreate');
            }
            $encoded_image = base64_encode(file_get_contents($image->getStream()->getMetadata('uri')));
            $img = 'data:' . $image->getClientMediaType() . ';base64,' . $encoded_image;
        }

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $response->withRedirect($this->router->pathFor('editPost', ['id' => $data['post_id']]));
        }

        $this->db->table('posts')->where('id', $post->id)->update([
            'category_id' => $data['category'],
            'title' => $data['title'],
            'header_img' => $img,
            'content' => $data['content'],
        ]);
        $this->flash->addMessage('message', 'The post has been edited!');
        return $response->withRedirect($this->router->pathFor('editPost', ['id' => $data['post_id']]));

    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deletePost($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('posts');
        }
        $post = $this->db->table('posts')->where('id', $args['id'])->first();
        if(!$post){
            return $this->redirect('posts');
        }
        $this->db->table('posts')->where('id', $post->id)->delete();
        $this->flash->addMessage('message', 'The post have been deleted!');
        return $this->redirect('posts');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function createCategory($request, $response, $args){
        $data = $request->getParsedBody();
        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate(['title' => [$data['cat_name'], "required"]]);

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $this->redirect('adminCategoryGet');
        }

        $cat = $this->db->table('categories')->where('name', $data['cat_name'])->first();

        if($cat){
            $this->flash->addMessage('message', 'The category already exists!');
            return $this->redirect('adminCategoryGet');
        }

        $this->db->table('categories')->insert(['name' => $data['cat_name']]);
        $this->flash->addMessage('message', 'The category has been created!');
        return $this->redirect('adminCategoryGet');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updateCategory($request, $response, $args){
        $data = $request->getParsedBody();

        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'cat_name' => [$data['cat_name'], "required"],
            'cat_id' => [$data['cat_id'], "required"]
        ]);

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $this->redirect('adminCategoryGet');
        }

        $this->db->table('categories')->where('id', $data['cat_id'])->update(['name' => $data['cat_name']]);
        $this->flash->addMessage('message', 'The category has been updated!');
        return $this->redirect('adminCategoryGet');

    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deleteCategory($request, $response, $args){
        $data = $request->getParsedBody();

        $this->v->addRuleMessages(['required' =>  'You need to fill all the forms!']);
        $this->v->validate([
            'delete' => [$data['delete'], "required"],
            'cat_id' => [$data['cat_id'], "required"]
        ]);

        if($this->v->passes()){
            $this->db->table('categories')->where('id', $data['cat_id'])->delete();
            $this->flash->addMessage('message', 'The category has been deleted!');
            return $this->redirect('adminCategoryGet');
        }else{

            $this->v->validate([
                'category' => [$data['category'], "required"],
                'cat_id' => [$data['cat_id'], "required"]
            ]);

            if(!$this->v->passes()){
                $this->flash->addMessage('message', $this->v->errors()->first());
                return $this->redirect('adminCategoryGet');
            }

            $posts = $this->db->table('posts')->where('category_id', $data['cat_id'])->get();

            foreach($posts as $post){
                $this->db->table('posts')->where('id', $post->id)->update(['category_id' => $data['category']]);
            }

            $this->db->table('categories')->where('id', $data['cat_id'])->delete();

            $this->flash->addMessage('message', 'The category has been deleted!');
            return $this->redirect('adminCategoryGet');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function closeReport($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('reports');
        }
        $report = $this->db->table('reports')->where('id',$args['id'])->where('approved',0)->first();

        if(!$report){
            return $this->redirect('reports');
        }

        $this->flash->addMessage('message', 'The report have been closed!');
        $this->db->table('reports')->where('id', $report->id)->update(['approved' => 1]);
        return $this->redirect('reports');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function confirmComment($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('comments');
        }
        $report = $this->db->table('post_comments')->where('id',$args['id'])->where('approved',0)->first();

        if(!$report){
            return $this->redirect('comments');
        }
        $this->flash->addMessage('message', 'The comment have been approved!');
        $this->db->table('post_comments')->where('id', $report->id)->update(['approved' => 1]);
        return $this->redirect('comments');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deleteComment($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('comments');
        }
        $report = $this->db->table('post_comments')->where('id',$args['id'])->first();

        if(!$report){
            return $this->redirect('comments');
        }
        $this->flash->addMessage('message', 'The comment have been deleted!');
        $this->db->table('post_comments')->where('id', $report->id)->delete();
        return $this->redirect('comments');
    }



    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updateConfigs($request, $response, $args){
        $data = $request->getParsedBody();

        /** Blog General Configs */
        $this->v->validate([
            'blogTitle' => [$data['blogTitle'], 'required|max(30)'],
            'blogLogo' => [$data['blogLogo'], 'required|max(15)'],
            'blogDescription' =>[$data['blogDescription'], 'required|max(250)'],
            'blogKeywords' =>[$data['blogKeywords'], 'required|max(250)'],
            'contact' => [$data['contact'], 'required|email']
        ]);

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $this->redirect('configs');
        }

        /** Google Analytics Configs */
        $this->v->validate(['enableGoogleAnalytics' => [$data['enableGoogleAnalytics'], 'required|checked']]);
        if($this->v->passes()){
            $enableGoogleAnalytics = 1;
            $this->v->validate(['googleAnalyticsId' => [$data['googleAnalyticsId'], 'required']]);
            if(!$this->v->passes()){
                $this->flash->addMessage('message', $this->v->errors()->first());
                return $this->redirect('configs');
            }
            $googleAnalyticId = $data['googleAnalyticsId'];
        } else {
            $enableGoogleAnalytics = 0;
            $googleAnalyticId = $this->config->get('googleAnalytics');
        }

        /** Blog Auto confirm account  */
        $this->v->validate(['blogEnableAutoConfirmAccount' => [$data['blogEnableAutoConfirmAccount'], 'required|checked']]);
        if($this->v->passes()){
            $enableAutoConfirmAccount = 1;
        }else{
            $enableAutoConfirmAccount = 0;
        }

        /** Blog auto confirm comments */
        $this->v->validate(['blogEnableAutoConfirmComment' => [$data['blogEnableAutoConfirmComment'], 'required|checked']]);
        if($this->v->passes()){
            $enableAutoConfirmComment = 1;
        }else{
            $enableAutoConfirmComment = 0;
        }

        /** Social Media Links */
        $this->v->validate([
            'facebook' => [$data['facebook'], 'required|url'],
            'google' => [$data['google'], 'required|url'],
            'twitter' => [$data['twitter'], 'required|url'],
        ]);

        if(!$this->v->passes()){
            $this->flash->addMessage('message', $this->v->errors()->first());
            return $this->redirect('configs');
        }

        /** List with all the configs and the values */
        $configs = [
            ['name' => 'blogTitle', 'value' => $data['blogTitle']],
            ['name' => 'blogLogo', 'value' => $data['blogLogo']],
            ['name' => 'blogDescription', 'value' => $data['blogDescription']],
            ['name' => 'blogKeywords', 'value' => $data['blogKeywords']],
            ['name' => 'blogEnableAutoConfirmAccount', 'value' => $enableAutoConfirmAccount],
            ['name' => 'blogEnableAutoConfirmComment', 'value' => $enableAutoConfirmComment],
            ['name' => 'enableGoogleAnalytics', 'value' => $enableGoogleAnalytics],
            ['name' => 'googleAnalytics', 'value' => $googleAnalyticId],
            ['name' => 'facebook', 'value' => $data['facebook']],
            ['name' => 'google', 'value' => $data['google']],
            ['name' => 'twitter', 'value' => $data['twitter']],
            ['name' => 'contactEmail', 'value' => $data['contact']]
        ];

        /** Update all the configs */
        foreach($configs as $config){
            $this->db->table('configs')->where('name', $config['name'])->update(['value' => $config['value']]);
        }

        $this->flash->addMessage('message', 'The configs have been update!');
        return $this->redirect('configs');
    }


}