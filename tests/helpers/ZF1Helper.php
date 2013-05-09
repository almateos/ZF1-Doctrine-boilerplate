<?php
namespace Codeception\Module;

/**
 * This module allows you to run tests inside Zend Framework.
 * It acts just like ControllerTestCase, but with usage of Codeception syntax.
 * Currently this module is a bit *alpha* as I have a little bit experience with ZF. Thus, contributions are welcome.
 *
 * It assumes, you have standard structure with __APPLICATION_PATH__ set to './application'
 * and LIBRARY_PATH set to './library'. If it's not redefine this constants in bootstrap file of your suite.
 *
 * ## Config
 *
 * * env  - environment used for testing ('testing' by default).
 * * config - relative path to your application config ('application/configs/application.ini' by default).
 *
 * ## API
 *
 * * client - BrowserKit client
 * * db - current instance of Zend_Db_Adapter
 * * bootstrap - current bootstrap file.
 *
 * ## Cleaning up
 *
 * For Doctrine1 and Doctrine2 all queries are put inside rollback transaction. If you are using one of this ORMs connect their modules to speed up testing.
 *
 * Unfortunately Zend_Db doesn't support nested transactions, thus, for cleaning your database you should either use standard Db module or
 * [implement nested transactions yourself](http://blog.ekini.net/2010/03/05/zend-framework-how-to-use-nested-transactions-with-zend_db-and-mysql/).
 *
 * If your database supports nested transactions (MySQL doesn't) or you implemented them you can put all your code inside a transaction.
 * Use a generated helper TestHelper. Usse this code inside of it.
 *
 * ``` php
 * <?php
 * namespace Codeception\Module;
 * class TestHelper extends \Codeception\Module {
 *      function _before($test) {
 *          $this->getModule('ZF1')->db->beginTransaction();
 *      }
 *
 *      function _after($test) {
 *          $this->getModule('ZF1')->db->rollback();
 *      }
 * }
 * ?>
 * ```
 *
 * This will make your functional tests run super-fast.
 *
 */

class ZF1Helper extends \Codeception\Util\Framework implements \Codeception\Util\FrameworkInterface
{
    /**
     * @var \Zend_Application
     */
    protected $_application;

    /**
     * @var \Codeception\Util\Connector\ZF1
     */
    public $client;

    protected $queries = 0;
    protected $time = 0;

    public function _initialize() {
        define('APPLICATION_ENV', 'development');

        chdir(dirname(dirname(__DIR__)));
        define('ROOT_PATH', '.');
        define('APPLICATION_PATH', './app');
        define('LIBRARY_PATH', './resources/lib');
        define('VENDOR_PATH', './resources/vendor');
        define('DOMAIN_PATH', './app/domain');

        require_once APPLICATION_PATH . '/autoload_register.php';

        $this->client = new \Codeception\Util\Connector\ZF1bis();

        //$_SESSION = array();
        $_COOKIE = array();
        $_GET = array();
        $_POST = array();

        \Zend_Session::$_unitTestEnabled = true;

        $config = include APPLICATION_PATH . '/etc/application.php';
        foreach(array('db', 'mailer') as $name) {
            $buffer = include APPLICATION_PATH . "/etc/$name.php";
            $config[APPLICATION_ENV][$name] = $buffer[APPLICATION_ENV];
        }
        $config[APPLICATION_ENV]['router'] = include APPLICATION_PATH . "/etc/router.php";
        $this->_config = $config;
    }

    public function init() {
        $this->_application = new \Zend_Application(
                APPLICATION_ENV,
                $this->_config[APPLICATION_ENV]
                );

        $this->_application->bootstrap();
        $this->client->setBootstrap($this->_application);
    }

    public function _before(\Codeception\TestCase $test) {
        $this->init();
        // Create application, bootstrap, and run
    }

    public function _after(\Codeception\TestCase $test) {
        $_SESSION = array();
        $_GET     = array();
        $_POST    = array();
        $_COOKIE  = array();
        $fc = $this->_application->getBootstrap()->getResource('frontcontroller');
        if ($fc) {
            $fc->resetInstance();
        }
        \Zend_Layout::resetMvcInstance();
        \Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->queries = 0;
        $this->time = 0;
    }

    protected function debugResponse()
    {
//        $this->debugsection('Route', sprintf('%/%/%',
//            $this->client->getzendrequest()->getmodulename(),
//            $this->client->getzendrequest()->getcontrollername(),
//            $this->client->getzendrequest()->getactionname()
//        ));
        $this->debugSection('Session',json_encode($_COOKIE));
        if ($this->db) {
            $profiler = $this->db->getProfiler();
            $queries = $profiler->getTotalNumQueries() - $this->queries;
            $time = $profiler->getTotalElapsedSecs() - $this->time;
            $this->debugSection('Db',$queries.' queries');
            $this->debugSection('Time',round($time,2).' secs taken');
            $this->time = $profiler->getTotalElapsedSecs();
            $this->queries = $profiler->getTotalNumQueries();
        }
    }

}
