<?php if (! defined('BASEPATH')) exit('No direct access allowed'); 
// TODO Encrypt & Decrypt

class sessioner {
    public $key = '';
    public $sess_name = 'session';
    public $lifetime = 3600;
    public $path = '/';
    public $domain = '';
    public $http_only = true;
    public $ssl_only = false;
    public $userdata;

    function __construct($param = array()) {
        // Basic configures
        foreach (array('key', 'sess_name', 'lifetime', 'path', 'domain') as $key) {
            if (isset($param[$key])) {
                $this->$key = $param[$key];
            }
        }
        // What time is now?
        $this->now = $this->_time();

        // To create or update?
        if (!$this->sess_read()) {
            $this->sess_create();
        } else {
            $this->sess_update();
        }
    }

    function sess_read() {
        // Fetch your cookie back.
        $session = $this->_get_cookie($this->sess_name);
        // No cookie for you, bye CHILD.
        if ($session === false) {
            return false;
        }

        // Decrypt your cookie.
        //$session = $this->_decode($session);
        // Unserialize your cookie.
        $session = $this->_unserialize($session);

        // Is your cookie correct?
        if (!is_array($session) || !isset($session['session_id']) || !isset($session['last_activity'])) {
            $this->sess_destory();
            return false;
        }

        // Your cookie is out of date.
        if ($session['last_activity'] + $this->lifetime < $this->now) {
            $this->sess_destory();
            return false;
        }

        // Your cookie is vaild! Here CHILD.
        $this->userdata = $session;
        unset($session);
        return true;
    }

    function sess_write() {
        $this->_set_cookie();
    }

    function sess_create() {
        $sessid = '';
        while (strlen($sessid) < 32) {
            $sessid .= mt_rand(0, mt_getrandmax());
        }

        $this->userdata = array(
            'session_id' => md5(uniqid($sessid, True)),
            'last_activity' => $this->now
        );

        $this->_set_cookie();
    }

    function sess_update() {
        $old_sessid = $this->userdata['session_id'];
        $new_sessid = '';
        while (strlen($new_sessid) < 32) {
            $new_sessid .= mt_rand(0, mt_getrandmax());
        }

        $new_sessid = md5(uniqid($new_sessid, true));

        $this->userdata['session_id'] = $new_sessid;
        $this->userdata['last_activity'] = $this->now;

        $this->_set_cookie();
    }

    function sess_destory() {
        setcookie(
            $this->sess_name,
            addslashes(serialize(array())),
            ($this->now - 2<<30),
            $this->path,
            $this->domain,
            $this->ssl_only,
            $this->http_only
        );
    }

    function _get_cookie() {
        if (!isset($_COOKIE[$this->sess_name])) {
            return false;
        } else {
            return $_COOKIE[$this->sess_name];
        }
    }

    function _set_cookie() {
        // Serialize your data.
        $cookie_data = $this->_serialize($this->userdata);
        
        // Encrypt your data.
        //$cookie_data = $this->_encode($cookie_data);

        $lifetime = ($this->lifetime + $this->_time());

        // All done.
        setcookie(
            $this->sess_name,
            $cookie_data,
            $lifetime,
            $this->path,
            $this->domain,
            $this->ssl_only,
            $this->http_only
            
        );
    }

    function _encode($data, $key = false) {
        if (!$key) {
            $key = $this->key;
        }

        $init_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
        $crypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $init_vect);

        return base64_encode($this->_add_cipher_noise($crypted, $key));
    }

    function _decode($raw, $key = false) {
        if (!$key) {
            $key = $this->key;
        }

        $raw = base64_decode($raw);
        $raw = $this->_remove_cipher_noise($raw, $key);
        $init_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        if ($init_size > strlen($raw)) {
            return false;
        }

        $init_vect = substr($raw, 0, $init_size);
        $data = substr($raw, $init_size);
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $init_vect), "\0");
    }

    function _add_cipher_noise($data, $key) {
		$keyhash = sha1($key);
		$keylen = strlen($keyhash);
		$str = '';

		for ($i = 0, $j = 0, $len = strlen($data);$i < $len;$i++, $j++) {
			if ($j >= $keylen) $j = 0;
			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}

		return $str;
    }

    function _remove_cipher_noise($data, $key) {
        $keyhash = sha1($key);
        $keylen = strlen($keyhash);
        $str = '';
        for ($i =0, $j =0, $len = strlen($data);$i < $len;$i++, $j++) {
            if ($j >= $keylen) $j = 0;
            $temp = ord($data[$i]) - ord($keyhash[$j]);
            if ($temp < 0) {
                $temp = $temp + 256;
            }
            $str .= chr($temp);
        }

        return $str;
    }

    function _serialize($data) {
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_string($val)) {
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else {
			if (is_string($data)) {
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}
		return serialize($data);
    }

	function strip_slashes($str) {
		if (is_array($str)) {
			foreach ($str as $key => $val) {
				$str[$key] = strip_slashes($val);
			}
		} else {
			$str = stripslashes($str);
		}

		return $str;
	}

    function _unserialize($data) {
        $data = @unserialize($this->strip_slashes($data));

		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_string($val)) {
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}
			return $data;
		}
		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
    }

    function _time() {
        return time();
    }
}
?>
