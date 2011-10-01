<?php if (! defined('BASEPATH')) exit('No direct access allowed.'); 
class requester {
    function __construct($param = false) {
        if ($param) {
            foreach($param as $arg => $value) {
                $this->$arg = $value;
            }
        }
        
        // init
        $this->scheme = $this->_get_scheme();
        $this->base_url = $this->_get_base();
        $this->method = $this->_get_method();
        $this->path = $this->_get_path();
        $this->current = $this->base_url . $this->path . '/';
    }

    function _get_scheme() {
        $scheme = split('/', $_SERVER['SERVER_PROTOCOL']);
        $scheme = strtolower($scheme[0]);
        return $scheme;
    }

    function _get_base() {
        # do you use hatccess?
        #$base_url = $this->scheme . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $script = split('/', $_SERVER['SCRIPT_NAME']);
        $parent = '/' . $script[1];
        $base_url = $this->scheme . '://' . $_SERVER['SERVER_NAME'] . $parent;
        return $base_url;
    }

    function _get_method() {
        $method = $_SERVER['REQUEST_METHOD'];
        return $method;
    }

    function _get_path() {
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
}
?>
