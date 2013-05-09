<?php
namespace Codeception\Module;

// here you can define custom functions for CodeGuy 

class CodeHelper extends \Codeception\Module
{
    public function _initialize() {
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

        //$sessionId = "icb6tjh303bhehahbcq518thu5";
        //session_id($sessionId);
        //$_COOKIE =  array("visit" => $sessionId);
        //$_COOKIE['PHPSESSID'] = session_id();
        $_SESSION['identity'] = array ('userId' => 1); 
        // Create application, bootstrap
        $this->app = new \Zend_Application( APPLICATION_ENV, $config[APPLICATION_ENV]);
        //\Zend_Registry::set('app', $application);

        \Zend_Session::$_unitTestEnabled = true;
        //$this->app->bootstrap();
        $this->app->bootstrap('ODM')->bootstrap('ORM')->bootstrap('EditorODM')->bootstrap('EditorORM')->bootstrap('Events');

        //\Codeception\Module\Doctrine2::$em = $application->getBootstrap()->getResource('ORM');
        //\Codeception\Module\DoctrineODMMongoDB::$dm = $application->getBootstrap()->getResource('ODM');
    }

    public function setUp($this2) {
        $bootstrap = $this->app->getBootstrap();
        //$this2->orm = $bootstrap->getResource('ORM');
        //$this2->odm = $bootstrap->getResource('ODM');
        $this2->events = $bootstrap->getResource('Events');
        $this2->editorodm = $bootstrap->getResource('EditorODM');
        $this2->editororm = $bootstrap->getResource('EditorORM');
    }
}
