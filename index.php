<?php
define('BASEPATH', realpath(dirname(__FILE__)));
define('EXT', '.php');
define('PLUGINS', realpath(BASEPATH.'/plugins').'/');
define('TPL', realpath(BASEPATH.'/tpl').'/');
define('LOGFILE', realpath(BASEPATH.'log.txt'));
define('Database', 'sqlite:imhbc.db');
// You can change the timezone here.
date_default_timezone_set('Asia/Shanghai');
define('DATE_FORMAT', 'Y-m-d h:i:s');

require_once('Charizard'.EXT);
require_once('main'.EXT);
require_once('writer'.EXT);
require_once('markdown'.EXT);

$urls = array(
    '/sess' => 'sess',
    '/' => 'index_handler',
    '/blog' => 'posts_handler',
    '/([0-9a-zA-Z\-\.]+)' => 'post_handler',

    '/writer/login' => 'login',
    '/writer/overview' => 'overview_handler',
    '/writer/settings' => 'settings_handler',
    '/writer/new' => 'write_handler',
    '/writer/save' => 'edit_handler',
    '/writer/edit/index' => 'index_write_handler',
    '/writer/update/index' => 'index_update_handler',
    '/writer/edit/([0-9a-zA-Z\-\_]+)' => 'write_handler',
    '/writer/update/([0-9a-zA-Z\-\_]+)' => 'edit_handler',
    '/writer/remove/([0-9a-zA-Z\-\_]+)' => 'remove_handler',
    '/writer' => 'overview_handler'
);

Charizard::run($urls, false);
?>
