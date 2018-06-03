<?php
$container['account'] = function ($c) {
    return new \App\Middlewares\Account($c);
};
$container['setup'] = function ($c) {
    return new \App\Middlewares\Setup($c);
};