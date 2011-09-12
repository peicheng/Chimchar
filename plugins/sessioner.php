<?php
define('COOKIE_NAME', 'charizard');

// TODO 
// * using session() instead...
// * user define key access
// * encrypt & decrypt
// * to database
// * pretty default key setting

class sessioner {
    // What time is now ?
    function _now() {
        //return date('YnjGis');
        return time();
    }

    function _parse($sess, $need) {
        $result = array();
        $find = false;

        foreach ($need as $key) {
            if (isset($sess[$key])) {
                $find = true;
                $result[$key] = $sess[$key];
            }
        }

        return ($find)?$result:$find;
    }

    function _check($pass, $key) {
        $secret = sha1($key);
        if ($secret === $pass) {
            return true;
        } else {
            return false;
        }
    }

    function _serialize($data) {
        if (is_array($data)) {
            foreach($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('\\', '{{slash}}', $val);
                }
            }
        } else {
            if (is_string($data)) {
                $data = str_replace('\\', '{{slash}}', $data);
            }
        }

        return serialize($data);
    }

    function _unserialize($data) {
        $data = @unserialize(stripslashes($data));

        if (is_array($data)) {
            foreach($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('{{slash}}', '\\', $val);
                }
            }
            return $data;
        }

        return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
    }

    function _set_cookie($data, $expire) {
        // TODO encrypt data
        
        $expire = $expire + time();

        setcookie(
            COOKIE_NAME,
            $data,
            $expire
        );

        return;
    }

    function get($key = false, $need = '') {
        if (empty($need)) {
            $need = array(
                'session_id',
                'encrypt_code',
                'expire_time',
                'last_activity',
            );
        }

        // Bring my $cookie back
        // Do some cleaning
        $raw = $this->_unserialize($_COOKIE[COOKIE_NAME]);
        $session = $this->_parse($raw, $need);
        // No cookie for you, just leave, CHILD
        if ($session === false) {
            return false;
        }

        // Check the cookie
        // TODO encrtpy & decrtpy
        if ($key) {
            // Decrtpy key wrong! Leave now!
            if (!$this->_check($session['encrypt_code'], $key)) {
                return false;
            }
        }

        // The cookie had expired, go home now, CHILD
        if ($session['last_activity'] + $session['expire_time'] < $this->_now()) {
            $this->destory();
            return false;
        }

        // Here is your cookie, CHILD
        return $session;
    }

    function write($sess, $key = '') {
        if (empty($key)) {
            $key = array(
                'session_id',
                'encrypt_code',
                'expire_time',
                'last_activity',
            );
        }

        // update the session_id
        $old = $sess['session_id'];
        $new = '';
        while (strlen($new) < 32) {
            $new .= mt_rand(0, mt_getrandmax());
        }
        $new = md5(uniqid($new, True));
        $sess['session_id'] = $new;

        $expire = $sess['expire_time'];
        $sess['last_activity'] = $this->_now();

        $sess = $this->_serialize($sess);
        $this->_set_cookie($sess, $expire);
    }

    function create($key, $expire = 3600) {
        // generate the session_id
        $sess_id = '';
        while (strlen($sess_id) < 32) {
            $sess_id .= mt_rand(0, mt_getrandmax());
        }
        $sess_id = md5(uniqid($sess_id, True));

        $sess = array(
            'session_id' => $sess_id,
            'encrypt_code' => sha1($key),
            'expire_time' => $expire,
            'last_activity' => $this->_now(),
        );

        $sess = $this->_serialize($sess);
        $this->_set_cookie($sess, $expire);
    }

    function destory() {
        setcookie(
            COOKIE_NAME,
            addslashes(serialize(array())),
            ($this->_now - 2 << 30)
        );
    }
}
?>
