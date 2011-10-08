<?php if (!defined('BASEPATH')) exit('No direct script access!');

class index_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $site_info = $db->get_site();
        $index = $db->get_index();
        $pages = $db->get_pages();
        if (!$index['style']) {
            $path = $index['style'] = DEFAULT_STYLE;
        } else {
            $path = $index['style'];
        }

        $template_values = array(
            'site_info' => $site_info,
            'pages' => $pages,
            'post' => $index,
            'style' => $path,
            'u' => $url_helper
        );

        echo $render->rende('index.html', $path, $template_values);
    }
}

class feed_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $template_values = array(
            'site_info' => $db->get_site(),
            'site_domain' => Charizard::$base_url,
            'update' => date(DATE_FORMAT),
            'posts' => $db->get_only_posts(),
            'u' => $url_helper
        );
        $path = 'shared';

        header ("Content-Type:text/xml");  
        echo $render->rende('feed.xml', $path, $template_values);
    }
}

class blog_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');

        $site_info = $db->get_site();
        $pages = $db->get_pages();
        $posts = $db->get_only_posts();
        if (!$site_info['style']) {
            $path = $site_info['style'] = DEFAULT_STYLE;
        } else {
            $path = $site_info['style'];
        }

        $template_values = array(
            'site_info' => $site_info,
            'pages' => $pages,
            'posts' => $posts,
            'style' => $path,
            'u' => $url_helper
        );

        echo $render->rende('posts.html', $path, $template_values);
    }
}

class main_handler {
    function GET($url) {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        $url_helper = Charizard::load('url_helper');
        $status = Charizard::load('status_coder');

        $site_info = $db->get_site();
        $pages = $db->get_pages();
        // try mini site
        $post = $db->get_minisite_by_url($url);
        if ($post) {
            if (!$post['style']) {
                $path = $post['style'] = DEFAULT_STYLE;
            } else {
                $path = $post['style'];
            }
            if (!$post['tpl']) {
                $tpl = $post['tpl'] = 'post.html';
            } else {
                $tpl = $post['tpl'];
            }
        } else {
            $post = $db->get_post_by_url($url);
            if (!$site_info['style']) {
                $path = $site_info['style'] = DEFAULT_STYLE;
            } else {
                $path = $site_info['style'];
            }
            $tpl = 'post.html';
        }
        // didn't catch it
        if (!$post) {
            $status->_404("$url not found!");
            return;
        }

        $template_values = array(
            'site_info' => $site_info,
            'pages' => $pages,
            'post' => $post,
            'style' => $path,
            'u' => $url_helper
        );

        echo $render->rende($tpl, $path, $template_values);
    }
}
?>
