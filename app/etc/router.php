<?php
return array(
    'default' => array(
        'route' => ':controller/:action/*',
        'defaults' => array(
            'module' => 'private',
            'controller' => 'index',
            'action' => 'index'
        )
    ),
    'auth' => array(
        'route' => ':action',
        'reqs' => array(
            'action'=> '(login|logout|register|reset-password|validate)'
        ),
        'defaults' => array(
            'module' => 'public',
            'controller' => 'auth'
        )
    )
);
