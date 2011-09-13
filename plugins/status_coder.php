<?php if (! defined('BASEPATH')) exit ('No direct script access');
// TODO 
// * testing, testing and testing

class status_coder {
    function set_status_code($code = 200, $message = '') {
        if ($code == '' || !is_numeric($code)) {
            $this->internal_error("Status code must be a numberic");
        }

        echo "<h1>$code</h1>\n<p>$message</p>\n";

        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi')
		{
			header("Status: {$code} {$message}", TRUE);
		}
		elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
		{
			header($server_protocol." {$code} {$message}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$message}", TRUE, $code);
        }

        return;
    }

    // 200
    function ok($message = '') {
        if ($message == '') {
            $message = 'OK';
        }
        
        $this->set_status_code(200, $message);
    }

    function _200($message = '') {
        $this->ok($message);
    }

    // 201
    function created($message = '') {
        if ($message == '') {
            $message = 'Created';
        }

        $this->set_status_code(201, $message);
    }

    function _201($message = '') {
        $this->created($message);
    }

    // 202
    function accepted($message = '') {
        if ($message == '') {
            $message = 'Accepted';
        }

        $this->set_status_code(202, $message);
    }

    function _202($message = '') {
        $this->accepted($message);
    }

    // TODO need to set status code ?
    // 301
    function redirect($newloc, $base_url = '', $message = '') {
        if ($message == '') {
            $message = 'Moved Permanently';
        }

        if ($newloc[0] === '/') {
            if ($base_url[strlen($base_url)] === '/') {
                $newloc = substr($newloc, 1);
            }
            $newloc = $base_url . $newloc;
        }

        header("Location: $newloc");
    }

    function _301($newloc, $base_url = '', $message = '') {
        $this->redirect($newloc, $base_url, $message);
    }

    // 302
    function found($newloc, $base_url = '', $message = '') {
        if ($message == '') {
            $message = 'Found';
        }
        $this->redirect($newloc, $base_url, $message);
    }

    function _302($newloc, $base_url = '', $message = '') {
        $this->_302($newloc, $base_url, $message);
    }

    // 303
    function seeother($newloc, $base_url = '', $message = '') {
        if ($message == '') {
            $message = 'See Other';
        }
        $this->redirect($newloc, $base_url, $message);
    }

    function _303($newloc, $base_url = '', $message = '') {
        $this->seeother($newloc, $base_url, $message);
    }

    // 304
    function not_modified($message = '') {
        if ($message == '') {
            $message = 'Not Modified';
        }
        $this->set_status_code(304, $message);
    }

    function _304($message = '') {
        $this->not_modified($message);
    }

    // 307
    function temp_redirect($newloc, $base_url = '', $message = '') {
        if ($message == '') {
            $message = 'Temporary Redirect';
        }
        $this->redirect($newloc, $base_url, $message);
    }

    function _307($newloc, $base_url = '', $message = '') {
        $this->temp_redirect($newloc, $base_url, $message);
    }

    // 400
    function bad_request($message = '') {
        if ($message == '') {
            $message = 'Bad Request';
        }

        $this->set_status_code(400, $message);
    }

    function _400($message = '') {
        $this->bad_request($message);
    }

    // 401
    function unauthorized($message = '') {
        if ($message == '') {
            $message = 'Unauthorized';
        }

        $this->set_status_code(401, $message);
    }

    function _401($message = '') {
        $this->unauthorized($message);
    }

    // 403
    function forbidden($message = '') {
        if ($message == '') {
            $message = 'Forbidden';
        }

        $this->set_status_code(403, $message);
    } 

    function _403($message = '') {
        $this->forbidden($message);
    }

    // 404
    function not_found($message = '') {
        if ($message == '') {
            $message = "We didn't find it!! =@";
        }

        $this->set_status_code(404, $message);
        
        exit;
    }

    function _404($message = '') {
        $this->not_found($message);
    }

    // 405
    function method_not_allowed($message = '') {
        if ($message == '') {
            $message = "Method Not Allowed";
        }

        $this->set_status_code(405, $message);
    }

    function _405($message = '') {
        $this->method_not_allowed($message);
    }

    // 406
    function not_acceptable($message = '') {
        if ($message == '') {
            $message = "Not Acceptable";
        }

        $this->set_status_code(406, $message);
    }

    function _406($message = '') {
        $this->not_acceptable($message);
    }

    // 409
    function conflict($message = '') {
        if ($message == '') {
            $message = "Conflict";
        }

        $this->set_status_code(409, $message);
    }

    function _409($message = '') {
        $this->conflict($message);
    }

    // 410
    function gone($message = '') {
        if ($message == '') {
            $message = "Gone";
        }

        $this->set_status_code(410, $message);
    }

    function _410($message = '') {
        $this->gone($message);
    }

    // 412
    function precodition_failed($message = '') {
        if ($message == '') {
            $message = "Precondition Failed";
        }

        $this->set_status_code(412, $message);
    }

    function _412($message = '') {
        $this->precodition_failed($message);
    }

    // 500
    function internal_error($message = '') {
        if ($message == '') {
            $message = "Internal Server Error";
        }

        $this->set_status_code(500, $message);

        exit;
    }

    function _500($message = '') {
        $this->internal_error($message);
    }
}
?>
