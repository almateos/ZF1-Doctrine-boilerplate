<?php
define('APPLICATION_ENV', 'development');

chdir(dirname(dirname(__DIR__)));
define('ROOT_PATH', '.');
define('APPLICATION_PATH', './app');
define('LIBRARY_PATH', './resources/lib');
define('VENDOR_PATH', './resources/vendor');
define('DOMAIN_PATH', './app/domain');

require_once APPLICATION_PATH . '/autoload_register.php';

$config = include APPLICATION_PATH . '/etc/application.php';
foreach(array('db','mailer') as $name) {
    $buffer = include APPLICATION_PATH . "/etc/$name.php";
    $config[APPLICATION_ENV][$name] = $buffer[APPLICATION_ENV];
}
$config[APPLICATION_ENV]['router'] = include APPLICATION_PATH . "/etc/router.php";

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    $config[APPLICATION_ENV]
);

$application->bootstrap('ODM')->bootstrap('ORM');
Zend_Registry::set('ZEND_APP',$application);

//\Codeception\Module\Doctrine2::$em = $application->getBootstrap()->getResource('ORM');
//\Codeception\Module\DoctrineODMMongoDB::$dm = $application->getBootstrap()->getResource('ODM');
