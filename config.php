<?php if (!defined('BASEPATH')) exit('No direct access allowed!');

/* Here is some basic setting... */

// Plugins
define('PLUGINS', realpath(BASEPATH.'/plugins').'/');

// Template
define('TPL', realpath(BASEPATH.'/tpl').'/');

// Logfile
define('LOGFILE', realpath(BASEPATH.'log.txt'));

// Database
define('Database', 'sqlite:chimchar.db');

// You can change the timezone here.
date_default_timezone_set('Asia/Shanghai');
define('DATE_FORMAT', 'Y-m-d h:i:s');

// Default style
define('DEFAULT_STYLE', 'chimchar');
?>
