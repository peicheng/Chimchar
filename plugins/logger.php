<?php if (! defined('BASEPATH')) exit ('No direct script access');
// TODO 
// * pretty pre-define config (__construction)
// * formatted logging
// * file operating

class logger {
    function set($file, $enable = false, $format = 'Y-m-d h:i:s') {
        $this->enable = $enable;
        $this->file = $file;
        $this->format = $format;
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
