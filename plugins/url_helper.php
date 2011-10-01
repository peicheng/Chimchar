<?php if (!defined('BASEPATH')) exit('No direct access allowed!');

class url_helper {
    function __construct($param = false) {
        if ($param) {
            foreach($param as $arg => $value) {
                $this->$arg = $value;
            }
        }
    }

    function build($child, $parent = false) {
        if (!$parent) {
            $parent = Charizard::$current;
        }
        $base = Charizard::$base_url;

        if (strpos($child, '/') === 0) {
            // root level
            $uri = ltrim($child, '/');
            return trim($base, '/') . '/' . $uri;
        } else if (strpos($child, '../') === 0) {
            // up a level
            // TODO too many ../ will cause invaild url
            $uri = $child;
            $p = trim($parent, '/');
            while (strpos($uri, '../') === 0) {
                $tmp = '';
                for ($i = 3;$i < strlen($uri);$i++) {
                    $tmp .= $uri[$i];
                }
                $uri = $tmp;

                // find the last level
                $find_last_slash = false;
                for ($i = strlen($p);$i >=0;$i--) {
                    if ($p[$i] == '/') {
                        $last_slash = $i;
                        $find_last_slash = true;
                        break;
                    }
                }
                // build the new uri
                if ($find_last_slash) {
                    $tmp = '';
                    for ($i = 0;$i < $last_slash;$i++) {
                        $tmp .= $p[$i];
                    }
                    $p = $tmp;
                } else {
                    break;
                }
            }
            return $p . '/' . $uri;
        } else {
            // add to the tail
            $uri = ltrim($child, '/');
            return trim($parent, '/') . '/' . $uri;
        }
    }
}
?>
