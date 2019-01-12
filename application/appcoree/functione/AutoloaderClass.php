<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class AutoloaderClass {

    public function loadController() {
        spl_autoload_register(function($class) {
            $file = CONTROLLER_PATH.$class.'.php';
            if (file_exists($file)) {
                require_once $file;
            };
        });
    }

    public function unloadController() {
        $alFunctions = spl_autoload_functions();
        foreach($alFunctions as $afunction) {
            spl_autoload_unregister($afunction);
        }
    }

    public function isCallable($class, $method) {
        if (class_exists($class)) {
            $loadedClass = new $class();
            if (method_exists($loadedClass, $method)) {
                return $loadedClass;
            }
        }
        return FALSE;
    }

}

?>