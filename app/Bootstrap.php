<?php
use Alma\Doctrine\DbBuilder;
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initPlaceholders() {
        // Initialisation de l'URL de base
        $baseURL = 'http://'.$_SERVER['HTTP_HOST'];


        $this->bootstrap('frontController');
        /** @var $front Zend_Controller_Front */
        $front = $this->getResource('frontController');
        $front->setBaseUrl($baseURL);


        $this->bootstrap('view');
        /** @var $view  */
        $view = $this->getResource('view');

        $view->getHelper('BaseUrl')
            ->setBaseUrl($baseURL);
        //Dirty fix
        $view->doctype('XHTML1_TRANSITIONAL');
    }

    protected function _initRouter() {
        $this->bootstrap('frontController');
        /** @var $front Zend_Controller_Front */
        $front = $this->getResource('frontController');

        $routesDefinition = $this->getOption('router');

        /** @var $router Zend_Controller_Router_Rewrite */
        $router = $front->getRouter();
        $router->addConfig(new Zend_Config($routesDefinition));
        return $router;
    }


    protected function _initORM() {
        $config = $this->getOption('db');
        return DbBuilder::constructOrm(new Zend_Config($config['orm'])); 
    }

    protected function _initODM() {
        $config = $this->getOption('db');
        return DbBuilder::constructOdm(new Zend_Config($config['odm']));
    }

    protected function _initEvents() {
        $manager = new \Incube\Event\EventManager();
        $fsIterator = new FilesystemIterator(DOMAIN_PATH . '/Services/Listeners');
        foreach( $fsIterator as $fileInfo){
            list($fileName,$ext) = explode('.',$fileInfo->getFileName());
            if($fileInfo->isFile() && $ext == 'php') {
                $listenerClassName = '\Services\Listeners\\'. $fileName;
                foreach($listenerClassName::getEvents() as $namespace => $event) {
                    foreach($event as $eventName => $callableNames) {
                        $manager->attach(array($namespace, $eventName), array($listenerClassName, $callableNames));
                    }
                }
            }
        }
        return $manager;
    }

    protected function _initMailer() {
        $config = $this->getOption('mailer');
        return new \Alma\Mailer($config);
    }

    protected function _initPlugin() {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $front->registerPlugin(new Alma\Controller\Plugin\Acl());
    }

    protected function _initSession() {
        return new Zend_Session_Namespace('identity');
    }

}
