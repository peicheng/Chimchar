<?php
/* Where are you now? */
define('BASEPATH', realpath(dirname(__FILE__)));
define('EXT', '.php');

// What do you like?
require_once('config'.EXT);
// Loading kernel...
require_once('Charizard'.EXT);
// Here am I!
require_once('main'.EXT);
require_once('writer'.EXT);

$urls = array(
    '/' => 'index_handler',
    '/blog' => 'blog_handler',
    '/([0-9a-zA-Z\-\.]+)' => 'main_handler',
    '/feed.xml' => 'feed_handler',

    '/writer/login' => 'login',
    '/writer/logout' => 'logout',
    '/writer/overview' => 'overview_handler',
    '/writer/settings' => 'settings_handler',
    '/writer' => 'overview_handler',

    '/writer/minisite/new' => 'write_minisite_handler',
    '/writer/minisite/edit/([0-9a-zA-Z\-\_]+)' => 'write_minisite_handler',
    '/writer/minisite/save' => 'update_minisite_handler',
    '/writer/minisite/update/([0-9a-zA-Z\-\_]+)' => 'update_minisite_handler',
    '/writer/minisite/remove/([0-9a-zA-Z\-\_]+)' => 'remove_minisite_handler',

    '/writer/post/new' => 'write_post_handler',
    '/writer/post/edit/([0-9a-zA-Z\-\_]+)' => 'write_post_handler',
    '/writer/post/save' => 'update_post_handler',
    '/writer/post/update/([0-9a-zA-Z\-\_]+)' => 'update_post_handler',
    '/writer/post/remove/([0-9a-zA-Z\-\_]+)' => 'remove_post_handler',

    '/writer/index/edit' => 'write_index_handler',
    '/writer/index/update' => 'update_index_handler'
);

Charizard::run($urls, false);
?>
