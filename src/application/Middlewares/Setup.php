<?php


namespace App\Middlewares;

use App\Main;

class Setup extends Main
{
    public function install($request, $response, $next){





        if(!$this->db->schema()->hasTable('users')){
            $this->db->schema()->create('users', function($table){
                $table->increments('id');
                $table->string('username', 30);
                $table->string('password', 255);
                $table->string('email', 255);
                $table->boolean('approved', false);
                $table->timestamp('created_at', NULL);
                $table->timestamp('updated_at', NULL);
            });
        }
        if(!$this->db->schema()->hasTable('users_profiles')){
            $this->db->schema()->create('users_profiles', function($table){
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('name', NULL);
                $table->string('last_name', NULL);
                $table->string('country', NULL);
                $table->string('city', NULL);
                $table->longtext('avatar');
            });
        }
        if(!$this->db->schema()->hasTable('recovers')){
            $this->db->schema()->create('recovers', function($table){
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('hash', 255);
                $table->boolean('validate', false);
                $table->timestamp('created_at', NULL);
                $table->timestamp('expire_at', NULL);
            });
        }
        if(!$this->db->schema()->hasTable('verifications')){
            $this->db->schema()->create('verifications', function($table){
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('hash', 255);
                $table->boolean('validate', false);
                $table->timestamp('created_at', NULL);
                $table->timestamp('updated_at', NULL);
            });
        }
        if(!$this->db->schema()->hasTable('groups')){
            $this->db->schema()->create('groups', function($table){
                $table->increments('id');
                $table->string('name', 30);
                $table->integer('permission');
            });
        }
        if(!$this->db->schema()->hasTable('users_groups')){
            $this->db->schema()->create('users_groups', function($table){
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedInteger('group_id');
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            });
        }

        if(!$this->db->schema()->hasTable('subscribers')){
            $this->db->schema()->create('subscribers', function($table){
                $table->increments('id');
                $table->string('email', 30);
            });
        }
        if(!$this->db->schema()->hasTable('configs')){
            $this->db->schema()->create('configs', function($table){
                $table->increments('id');
                $table->string('name', 255);
                $table->string('value', 255);
            });
        }

        if(!$this->db->schema()->hasTable('categories')){
            $this->db->schema()->create('categories', function($table){
                $table->increments('id');
                $table->string('name', 255);
            });
        }
        if(!$this->db->schema()->hasTable('posts')){
            $this->db->schema()->create('posts', function($table){
                $table->increments('id');
                $table->unsignedInteger('category_id');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('title', 255);
                $table->longText('header_img');
                $table->longText('content');
                $table->timestamp('created_at', NULL);
            });
        }
        if(!$this->db->schema()->hasTable('post_comments')){
            $this->db->schema()->create('post_comments', function($table){
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedInteger('post_id');
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->string('approved', 255);
                $table->longText('content');
                $table->timestamp('created_at', NULL);
            });
        }
        if(!$this->db->schema()->hasTable('reports')){
            $this->db->schema()->create('reports', function($table){
                $table->increments('id');
                $table->string('link');
                $table->string('email');
                $table->string('approved', 255);
                $table->longText('content');
                $table->timestamp('created_at', NULL);
            });
        }


        /** Insert the groups */
        $group = $this->db->table('groups')->where('name', 'administrator')->first();
        if(!$group){
            $this->db->table('groups')->insert([
                'id' => 1,
                'name' => 'administrator',
                'permission' => 3
            ]);
        }
        $group = $this->db->table('groups')->where('name', 'moderator')->first();
        if(!$group){
            $this->db->table('groups')->insert([
                'id' => 2,
                'name' => 'moderator',
                'permission' => 2
            ]);
        }
        $group = $this->db->table('groups')->where('name', 'user')->first();
        if(!$group){
            $this->db->table('groups')->insert([
                'id' => 3,
                'name' => 'user',
                'permission' => 1
            ]);
        }

        /** Insert the first category */
        $category = $this->db->table('categories')->where('name', 'uncategorized')->first();
        if(!$category){
            $this->db->table('categories')->insert(['name' => 'uncategorized']);
        }

        /** Insert the default admin */
        $user = $this->db->table('users')->where('username', 'admin')->first();
        if(!$user){
            $this->db->table('users')->insert([
                'username' => 'admin',
                'email' => 'admin@domain.com',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'approved' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $user = $this->db->table('users')->where('username', 'admin')->first();
            $this->db->table('users_profiles')->insert([
                'user_id' => $user->id,
                'avatar' => '/uploads/avatars/default.png'
            ]);
            $this->db->table('users_groups')->insert(['user_id' => $user->id , 'group_id' => 1]);
        }

        /** Insert the default configs */
        $configs = [
            ['name' => 'blogTitle', 'value' => 'Simple Blog'],
            ['name' => 'blogLogo', 'value' => 'Simple Blog'],
            ['name' => 'blogDescription', 'value' => 'This is the default blog description. Please modify it'],
            ['name' => 'blogKeywords', 'value' => 'blog, simple'],
            ['name' => 'blogEnableAutoConfirmAccount', 'value' => '0'],
            ['name' => 'blogEnableAutoConfirmComment', 'value' => '0'],
            ['name' => 'enableGoogleAnalytics', 'value' => '0'],
            ['name' => 'googleAnalytics', 'value' => ''],
            ['name' => 'facebook', 'value' => 'https://www.facebook.com'],
            ['name' => 'google', 'value' => 'https://www.google.com'],
            ['name' => 'twitter', 'value' => 'https://www.twitter.com'],
            ['name' => 'contactEmail', 'value' => 'contact@domain.com']

        ];
        foreach($configs as $config){
            $result = $this->db->table('configs')->where('name', $config['name'])->first();
            if(!$result){
                $this->db->table('configs')->insert(['name' => $config['name'], 'value' => $config['value']]);
            }
        }

        return $next($request, $response);
    }
}