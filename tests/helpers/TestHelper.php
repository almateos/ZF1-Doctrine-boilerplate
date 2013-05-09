<?php
namespace Codeception\Module;

// here you can define custom functions for TestGuy 

class TestHelper extends \Codeception\Module
{
    public function _initialize() {
        $this->client = new \Codeception\Util\Connector\Universal();
        // or any other connector you implement

        // we need specify path to index file
        $this->client->setIndex('public/index.php');

        //exec('cake db:drop');
        //exec('cake db:migrate');
        exec('rm -rf var/tmp/mailbox');
        exec('rm -rf var/log/*');


        $sessionId = "icb6tjh303bhehahbcq518thu5";
        session_id($sessionId);
        $_COOKIE =  array("visit" => $sessionId);
    }     

    public function amVisitor() {
        $_SESSION = array();
        unset($_COOKIE['PHPSESSID']);
    }

    public function amEditor() {
        $I = $this->getModule('ZF1Helper');
        $this->amVisitor();

        $I->amOnPage('/login');
        $_POST = array('email' => 'f@test.com', 'password' => 'testtest'); 
        $I->click('submit');

        $_COOKIE['PHPSESSID'] = session_id();
        $_SESSION['identity'] = array ('userId' => 1); 
        $I->init();
        //$I->amOnPage('/');
        //$I->see('Dashboard');
    }   
}
