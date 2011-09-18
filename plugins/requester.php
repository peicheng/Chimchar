<?php if (! defined('BASEPATH')) exit('No direct access allowed.'); 
class requester {
    function __construct($param = false) {
        $this->base_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        if ($param) {
            foreach ($param as $arg => $value) {
                $this->$arg = $value;
            }
        }

        $this->path = $this->_parser_uri();
    }

    function _parser_uri() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '';
        }

        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        if (strncmp($uri, '?/', 2) === 0) {
            $uri = substr($uri, 2);
        }
        $parts = preg_split('#\?#i', $uri, 2);
        $uri = $parts[0];
        if (isset($parts[1])) {
            $_SERVER['QUERY_STRING'] = $parts[1];
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        } else {
            $_SERVER['QUERY_STRING'] = '';
            $_GET = array();
        }

        if ($uri == '/' || empty($uri)) {
            return '/';
        }
        
        return '/'.str_replace(array('//', '../'), '/', trim($uri, '/'));
    }

    function get_base_url() {
        return $this->base_url;
    }

    function get_path() {
        return $this->path;
    }

    function get_method() {
        return $this->method;
    }
}
?>
