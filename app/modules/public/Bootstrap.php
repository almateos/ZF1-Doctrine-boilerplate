<?php
class Public_Bootstrap extends Zend_Application_Module_Bootstrap {
    public function getResourceLoader(){
        parent::getResourceLoader();
        $this->_resourceLoader->addResourceType('controller','controllers','Controller');
    }
}
