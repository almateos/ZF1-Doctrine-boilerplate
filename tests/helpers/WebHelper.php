<?php
namespace Codeception\Module;

// here you can define custom functions for WebGuy 

class WebHelper extends \Codeception\Module
{
    public function amVisitor() {
        $I = $this->getModule('PhpBrowser');
        $I->amOnPage('/logout');
        $I->amOnPage('/');
    }

    public function amEditor() {
        $this->amVisitor();
        $I = $this->getModule('PhpBrowser');
        $I->amOnPage('/login');
        $I->fillField('email','a@test.com');
        $I->fillField('password','testtest');
        $I->click('.btn-login');
    }

    public function haveNoEmails() {
        //$app = \Zend_Registry::get('ZEND_APP');
        //$mailerConfig = $app->getBootstrap()->getOption('mailer');
        //exec('rm -f '.$mailerConfig['transport']['options']['path'].'/*');
        exec('rm -rf var/tmp/mailbox');
    }

    public function seeAnEmailContaining($text) {
        $app = \Zend_Registry::get('ZEND_APP');
        $mailerConfig = $app->getBootstrap()->getOption('mailer');
        $result = false;
        $mailDirectory = new \DirectoryIterator($mailerConfig['transport']['options']['path']);
        foreach($mailDirectory as $file){
            /**
             * @var $file \DirectoryIterator
             */
            if($file->isDot()) continue;
            $fileContent = file_get_contents($file->getPathname());
            if(preg_match('#'.preg_quote($text).'#', $fileContent)) { 
                $result = true;
                break;
            }
        }
        $this->assertTrue($result);
    }

    // HOOK: used after configuration is loaded
    public function _initialize() {
        //exec('cake db:drop');
        //exec('cake db:migrate');
        //exec('cake db:seeds');
        $this->haveNoEmails();
    }


    // HOOK: on every Guy class initialization
    public function _cleanup() {
    }
}
