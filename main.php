<?php if (!defined('BASEPATH')) exit('No direct script access!');

class index_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'post' => $db->get_index(),
            'pages' => $db->get_pages(),
            'u' => $url_helper
        );
        $path = 'chimchar';

        echo $render->rende('index.html', $path, $template_values);
    }
}

class feed_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        header ("Content-Type:text/xml");  
        
        $template_values = array(
            'site_info' => $db->get_site(),
            'site_domain' => Charizard::$base_url,
            'update' => date(DATE_FORMAT),
            'posts' => $db->get_posts(),
            'u' => $url_helper
        );
        $path = 'shared';

        echo $render->rende('feed.xml', $path, $template_values);
    }
}

class blog_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'posts' => $db->get_only_posts(),
            'pages' => $db->get_pages(),
            'u' => $url_helper
        );
        $path = 'chimchar';

        echo $render->rende('posts.html', $path, $template_values);
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
            $path = $post['tpl'];
            $style = $post['tpl'] . '/' . $post['style'] . '.css';
        } else {
            $post = $db->get_post_by_url($url);
            $path = 'chimchar';
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

        echo $render->rende('post.html', $path, $template_values);
    }
}
?>
