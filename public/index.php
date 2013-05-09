<?php
// Application contants
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

ini_set('display_errors', APPLICATION_ENV == 'production' ? 0 : 1);


define('ROOT_PATH',  dirname(__DIR__));
define('APPLICATION_PATH', ROOT_PATH . '/app');
define('LIBRARY_PATH', ROOT_PATH . '/resources/lib');
define('VENDOR_PATH', ROOT_PATH . '/resources/vendor');
define('DOMAIN_PATH', ROOT_PATH . '/app/domain');

chdir(ROOT_PATH);
ini_set('error_log', ROOT_PATH .'/var/log/errorlog.log');

// Remove include paths (except ZF1): forces to always use absolute paths. This is a major perf gain (even with an opcode cache)
set_include_path(VENDOR_PATH.'/bombayworks/zendframework1/library');

include APPLICATION_PATH . '/autoload_register.php';

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

$application->bootstrap()
    ->run();
