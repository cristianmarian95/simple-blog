<?php
$app->group('', function() use($app) {
    $app->get('/', 'blog:index')->setName('index');
    $app->get('/post[/{id}]', 'blog:read')->setName('read');
    $app->get('/login', 'blog:login')->setName('login')->add('account:logged');
    $app->get('/register', 'blog:register')->setName('register')->add('account:logged');
    $app->get('/contact', 'blog:contact')->setName('contact');
    $app->get('/recover', 'blog:recover')->setName('recover')->add('account:logged');
    $app->get('/confirm[/{hash}]', 'blog:recoverChangePassword')->setName('recoverPassword');
    $app->get('/profile', 'blog:profile')->setName('profile')->add('account:isLog');
    $app->get('/logout', 'blog:logout')->setName('logout')->add('account:isLog');
    $app->get('/subscribe', 'blog:subscribe')->setName('subscribe');
    $app->get('/unsubscribe', 'blogActions:unsubscribe')->setName('unsubscribe');
    $app->get('/active[/{key}]', 'blog:active')->setName('active');
    $app->get('/category[/{id}]', 'blog:category')->setName('category');
    $app->get('/report', 'blog:report')->setName('report');
    $app->post('/search', 'blogActions:search')->setName('search');

    $app->group('/action', function() use ($app) {
        $app->post('/login', 'blogActions:login')->setName('actionLogin');
        $app->post('/register', 'blogActions:register')->setName('actionRegister');
        $app->post('/recover', 'blogActions:recover')->setName('actionRecover');
        $app->post('/comment', 'blogActions:comment')->setName('actionComment')->add('account:isLog');
        $app->post('/update/profile', 'blogActions:updateProfile')->setName('updateProfile')->add('account:isLog');
        $app->post('/update/profile/avatar', 'blogActions:updateAvatar')->setName('updateAvatar')->add('account:isLog');
        $app->post('/delete', 'blogActions:deleteProfile')->setName('delete')->add('account:isLog');
        $app->post('/subscribe', 'blogActions:subscribe')->setName('actionSubscribe');
        $app->post('/change/password', 'blogActions:updatePassword')->setName('updatePassword');
        $app->post('/recover/password', 'blogActions:recoverPassword')->setName('actionRecoverPassword');
        $app->post('/contact', 'blogActions:submitContact')->setName('submitContact');
        $app->post('/report', 'blogActions:actionReport')->setName('actionReport');
    });

    $app->group('/admin', function() use ($app) {
        $app->get('/', 'admin:index')->setName('adminHome');
        $app->get('/people', 'admin:people')->setName('adminPeople');
        $app->get('/profile[/{id}]', 'admin:userProfile')->setName('adminUserProfile');
        $app->get('/add/post', 'admin:createPost')->setName('adminGetCreate');
        $app->get('/add/category', 'admin:createCategory')->setName('adminCategoryGet');
        $app->get('/posts', 'admin:posts')->setName('posts');
        $app->get('/reports', 'admin:reports')->setName('reports');
        $app->get('/edit/post[/{id}]', 'admin:editPost')->setName('editPost');
        $app->get('/comments', 'admin:comments')->setName('comments');
        $app->get('/settings', 'admin:settings')->setName('configs');

        $app->group('/action', function() use ($app) {
           $app->get('/user/delete[/{id}]', 'adminActions:deleteUser')->setName('actionDeleteUser');
           $app->post('/user/update', 'adminActions:updateUser')->setName('adminUpdateUser')->add('account:isSuperAdmin');
           $app->post('/post/create', 'adminActions:createPost')->setName('adminPostCreate');
           $app->post('/post/update', 'adminActions:editPost')->setName('editPostAction');
           $app->get('/post/delete[/{id}]', 'adminActions:deletePost')->setName('deletePost');
           $app->post('/category/create', 'adminActions:createCategory')->setName('adminPostCategory')->add('account:isSuperAdmin');
           $app->post('/category/update', 'adminActions:updateCategory')->setName('updateCategory')->add('account:isSuperAdmin');
           $app->post('/category/delete', 'adminActions:deleteCategory')->setName('deleteCategory')->add('account:isSuperAdmin');
           $app->get('/close/report[/{id}]', 'adminActions:closeReport')->setName('closeReport')->add('account:isSuperAdmin');
           $app->get('/confirm/comment[/{id}]', 'adminActions:confirmComment')->setName('confirmComment')->add('account:isSuperAdmin');
           $app->get('/delete/comment[/{id}]', 'adminActions:deleteComment')->setName('deleteComment')->add('account:isSuperAdmin');
           $app->post('/update/configs', 'adminActions:updateConfigs')->setName('updateConfigs');
        });
    })->add('account:isLog')->add('account:isAdmin');

})->add('setup:install');