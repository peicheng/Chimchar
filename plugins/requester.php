<?php
// TODO
// * .htaccess test
// * more pretty parser
// * handle both $_POST & $_GET

class requester {
    function __construct() {
        $this->path = $this->_parse_uri();
        // FIXME fix the protocol
        $this->self = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    // TODO more parser...
    // I took these codes from CodeIgniter
    function _parse_uri() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '';
        }

        $uri = $_SERVER['REQUEST_URI'];

        // TODO .htaccess test
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }
        
        // We fix the $_GET by the way.
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

    function get_self() {
        if (isset($this->self)) {
            return $this->self;
        }

        return false;
    }

    function get_path() {
        if (isset($this->path)) {
            return $this->path;
        }

        return false;
    }

    function get_method() {
        if (isset($this->method)) {
            return $this->method;
        }

        return false;
    }
}
?>
