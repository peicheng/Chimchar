<?php
define('BASEPATH', dirname(__FILE__));
define('TPL', realpath(BASEPATH . '/tpl') . '/');
define('COOKIE_NAME', 'chimchar');
define('DB', 'sqlite:imhbc.db');

require_once('Charizard.php');
require_once('writer.php');
require_once('main.php');

require_once('secret.php');

$urls = array(
    '/' => 'index_handler',
    '/blog' => 'posts_handler',
    '/([0-9a-zA-Z\-\.]+)' => 'post_handler',

    "/$secret/overview" => 'overview_handler',
    "/$secret/settings" => 'settings_handler',
    "/$secret/new" => 'write_handler',
    "/$secret/save" => 'post_handler',
    "/$secret/edit/index" => 'index_write_handler',
    "/$secret/update/index" => 'index_update_handler',
    "/$secret/edit/([0-9a-zA-Z\-\_]+)" => 'write_handler',
    "/$secret/update/([0-9a-zA-Z\-\_]+)" => 'edit_handler',
    "/$secret/remove/([0-9a-zA-Z\-\_]+)" => 'remove_handler',
    "/$secret" => 'overview_handler'
);

Charizard::$log = false;
$app = Charizard::run($urls);
?>
