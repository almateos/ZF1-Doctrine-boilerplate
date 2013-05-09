<?php
spl_autoload_register(
    function ($class) {
        static $map;
        if (!$map) {
            $map = include VENDOR_PATH . '/composer/autoload_classmap.php';
        }

        if (!isset($map[$class])) {
            return false;
        }
        return include $map[$class];
    }
);
