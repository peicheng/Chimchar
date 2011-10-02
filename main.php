<?php if (!defined('BASEPATH')) exit('No direct script access!');

class index_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'post' => $db->get_index(),
            'page' => $db->get_pages(),
            'u' => $url_helper
        );
        $path = 'chimchar/index.html';

        echo $render->rende($path, $template_values);
    }
}

class feed_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'posts' => $db->get_posts(),
            'u' => $url_helper
        );
        $path = 'shared/feed.xml';

        echo $render->rende($path, $template_values);
    }
}

class blog_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'posts' => $db->get_posts(),
            'pages' => $db->get_pages(),
            'u' => $url_helper
        );
        $path = 'chimchar/posts.html';

        echo $render->rende($path, $template_values);
    }
}

class main_handler {
    function GET($url) {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');
        $status = Charizard::load('status_coder');

        // try mini site
        $post = $db->get_minisite_by_url($url);
        if ($post) {
            $path = $post['tpl'] . '/post.html';
            $style = $post['tpl'] . '/' . $post['style'] . '.css';
        } else {
            $post = $db->get_post_by_url($url);
            $path = 'chimchar/posts.html';
            $style = 'chimchar/style.css';
        }
        // didn't catch it
        if (!$post) {
            $status->_404("$url not found!");
        }

        $template_values = array(
            'site_info' => $db->get_site(),
            'post' => $post,
            'pages' => $db->get_pages(),
            'style' => $style,
            'u' => $url_helper
        );

        echo $render->rende($path, $template_values);
    }
}
?>
