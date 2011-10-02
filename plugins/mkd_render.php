<?php if (! defined('BASEPATH') exit('No direct script access!'));
require_once 'markdown.php';

class mkd_render {
    function rende($content) {
        return Markdown($content);
    }
}
?>
