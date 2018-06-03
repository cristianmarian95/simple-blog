<?php
$container['comment'] = function ($c) {
    return new \App\Modules\Comments($c);
};
$container['post'] = function ($c) {
    return new \App\Modules\Post($c);
};
$container['user'] = function ($c) {
    return new \App\Modules\User($c);
};
$container['report'] = function ($c) {
    return new \App\Modules\Reports($c);
};
$container['function'] = function ($c) {
    return new \App\Modules\Functions($c);
};
$container['group'] = function ($c) {
    return new \App\Modules\Groups($c);
};
$container['category'] = function ($c){
    return new \App\Modules\Categories($c);
};
$container['session'] = function ($c){
    return new \App\Modules\Session($c);
};
$container['mail'] = function ($c){
    return new \App\Modules\Mail($c);
};
$container['config'] = function ($c){
    return new \App\Modules\Settings($c);
};