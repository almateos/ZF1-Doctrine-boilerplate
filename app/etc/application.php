<?php

/***************************************** PRODUCTION *****************************************/
$production = array(

        'appnamespace' => '',


        'bootstrap' => array(
            'path'  => APPLICATION_PATH . '/Bootstrap.php',
            'class' => 'Bootstrap',
            ),

        'phpSettings' => array(
            'display_startup_errors' => false,
            'display_errors' => false,
            'date' => array('timezone' => 'Europe/Paris'),
            ),

        'resources' => array(
            'frontController' => array(
                'params' => array('displayExceptions' => false),
                'moduleDirectory' => APPLICATION_PATH . '/modules',
                'defaultModule' => 'public',
                'partialPath' => APPLICATION_PATH . '/views/partials',
                ),
            'modules' => array(),
            'layout'  => array('layoutPath' => APPLICATION_PATH . '/views/layouts/scripts'),
            'view'    => array(
                'helperPaths'=> array(
                )
            ),
            'ORM'     => array(),
            'ODM'     => array(),
            'locale'  => array( 'default' => 'en', 'force' => true, 'cache'=>null),
            'Mailer'  => array(),
            'Events'  => array(),
            'Session'  => array()
            ),

        'autoloaderNamespaces' => array( )

        );


/******************************************* STAGING *******************************************/
$staging = $production;


/******************************************* TESTING *******************************************/
$testing = $production;

$testing['phpSettings']['display_startup_errors'] = true;
$testing['phpSettings']['display_errors'] = true;
$testing['resources']['frontController']['params']['displayExceptions'] = false;


/***************************************** DEVELOPMENT *****************************************/
$development = $testing;
$development['resources']['frontController']['params']['displayExceptions'] = true;


return array('production' => $production, 'staging' => $staging, 'testing' => $testing, 'development' => $development);
