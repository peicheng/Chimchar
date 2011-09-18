<?php if (! defined('BASEPATH')) exit('No direct access allowed.'); 
// TODO 
// * formatted logging
// * file operating

class logger {
    function __construct($param = false) {
        $this->format = 'Y-m-d h:i:s';

        if ($param) {
            foreach($param as $arg => $value) {
                $this->$arg = $value;
            }
        }
    }

    function log($message, $level = 'error') {
        if (!$this->enable) {
            return false;
        }

        if (is_writeable($this->file)) {
            if (!$handle = fopen($this->file, 'a')) {
                return false;
            }

            $current = date($this->format);
            $message = "[$level] $message $current\n";

            if (fwrite($handle, $message) === false) {
                return false;
            } else {
                fclose($handle);
                return true;
            }
        } else {
            return false;
        }
    }
}
?>
