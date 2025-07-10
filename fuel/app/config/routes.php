<?php

return array(
    '_root_'  => 'project/index',  // デフォルトルート
    '_404_'   => 'welcome/404',    // 404ページ
    
    // プロジェクト関連
    'project'                => 'project/index',
    'project/create'         => 'project/create',
    'project/edit/(:num)'    => 'project/edit/$1',
    'project/delete/(:num)'  => 'project/delete/$1',
    'project/view/(:num)'    => 'project/view/$1',
    
    // タスク関連
    'task/create/(:num)'     => 'task/create/$1',
    'task/edit/(:num)'       => 'task/edit/$1',
    'task/delete/(:num)'     => 'task/delete/$1',
    'task/toggle_status/(:num)' => 'task/toggle_status/$1',
    
    // 認証関連
    'auth/login'             => 'auth/login',
    'auth/register'          => 'auth/register',
    'auth/logout'            => 'auth/logout',
);