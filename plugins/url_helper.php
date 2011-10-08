<?php if (!defined('BASEPATH')) exit('No direct access allowed!');

class url_helper {
    function __construct($param = false) {
        if ($param) {
            foreach($param as $arg => $value) {
                $this->$arg = $value;
            }
        }
    }

    function build($child, $query = false, $parent = false) {
        if (!$parent) {
            $parent = Charizard::$current;
        }
        $base = Charizard::$base_url;
        $result = '';

        if (strpos($child, '/') === 0) {
            // root level
            $uri = ltrim($child, '/');
            $result = trim($base, '/') . '/' . $uri;
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
            $result = $p . '/' . $uri;
        } else {
            // add to the tail
            $uri = ltrim($child, '/');
            $result = trim($parent, '/') . '/' . $uri;
        }

        // add query to the tail
        if (is_array($query)) {
            $result .= '?';
            foreach ($query as $key => $value) {
                if ($value) {
                    $result .= "$key=$value" . '&';
                }
            }
            $result = substr($result, 0, -1);
        }

        return $result;
    }
}
?>
