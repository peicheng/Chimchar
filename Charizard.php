<?php if (! defined('BASEPATH')) exit ('No direct script access');
// TODO 
// * better pre-define global vars
// * load a instance with config (__construction)
// * better params for controllers
// * sub app support

define('EXT', '.php');
define('PLUGINS', realpath(BASEPATH . '/plugins') . '/');

class Charizard {
    static $plugins = array();
    static $urls;
    static $path;
    static $base_url;
    static $method;
    static $log = false;

    static function run($urls, $base_url = '', $log = false) {
        if (!self::$log && !$log) {
            self::$log = $log;
        }
        self::$urls = $urls;
        krsort(self::$urls);

        // load the kernel stuff ...
        $requester = self::load("requester");
        $status_coder = self::load("status_coder");
        $logger = self::load("logger");
        $logger->set(realpath(BASEPATH . '/log.txt'), self::$log);

        self::$base_url = (empty($base_url))?$requester->get_self():$base_url;
        self::$path = $requester->get_path();
        if (!self::$path) {
            $logger->log("Path didn't found.");
            $status_coder->_404();
        }
        self::$method = $requester->get_method();
        if (!self::$method) {
            $logger->log("Method didn't found.");
            $status_coder->_405();
        }

        // :: is evil =<
        $urls = self::$urls;
        $base_url = self::$base_url;
        $path = self::$path;
        $method = self::$method;

        foreach($urls as $regex => $class) {
            $regex = str_replace('/', '\/', $regex);
            $regex = '^' . $regex . '\/?$';

            $found = false;
            
            if (preg_match("/$regex/i", $path, $matches)) {
                $found = true;
                
                if (class_exists($class)) {
                    $obj = new $class;
                    if (method_exists($obj, $method)) {
                        $logger->log("$path found.", 'success');
                        // TODO better param
                        $obj->$method($matches[1]);
                    } else {
                        $logger->log("$method doesn't exists.");
                        $status_coder->_405();
                    }
                } else {
                    $logger->log("$class doesn't exists.");
                    $status_coder->_404("$class not found!! =@");
                }

                break;
            }
        }

        if (!$found) {
            $logger->log("$path not found.");
            $status_coder->_404("$path not found!! =@");
        }
        return;
    }

    static function &load($plugin) {
        if (isset(self::$plugins[$plugin])) {
            return self::$plugins[$plugin];
        }

        if (file_exists(PLUGINS . $plugin.EXT)) {
            require(PLUGINS . $plugin.EXT);
            self::$plugins[$plugin] =& new $plugin();
            return self::$plugins[$plugin];
        }

        return false;
    }
}
?>
