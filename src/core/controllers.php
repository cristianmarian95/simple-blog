<?php
$container['blog'] = function ($c) {
    return new \App\Controllers\Blog($c);
};
$container['blogActions'] = function ($c) {
    return new \App\Controllers\BlogActions($c);
};
$container['admin'] = function ($c) {
    return new \App\Controllers\Admin($c);
};
$container['adminActions'] = function ($c) {
    return new \App\Controllers\AdminActions($c);
};