<?php if (! defined('BASEPATH')) exit ('No direct script access');
class index_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        
        $template_values = array(
            'post' => $db->get_index(),
            'pages' => $db->get_pages(),
            'site_info' => $db->get_site()
        );

        $path = TPL . 'index.php';
        $render->rende($path, $template_values);
    }
}

class post_handler {
    function GET($url) {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $status_coder = Charizard::load('status_coder');

        $template_values = array(
            'post' => $db->get_post_by_url($url),
            'pages' => $db->get_pages(),
            'site_info' => $db->get_site()
        );

        if (!$template_values['post']) {
            $status_coder->_404("$url not found!");
        }

        $path = TPL . 'post.php';
        $render->rende($path, $template_values);
    }
}

class posts_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $template_values = array(
            'posts' => $db->get_posts(),
            'pages' => $db->get_pages(),
            'site_info' => $db->get_site()
        );

        $path = TPL . 'posts.php';
        $render->rende($path, $template_values);
    }
}
?>
