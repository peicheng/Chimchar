<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class sess {
    function GET() {
        echo "dude";
        $s = Charizard::load('sessioner');
        $s->sess_name = 'test';
        //$s->lifetime_minutes(5);
        $s->start_session();
        //$s->set_session(array("test" => True));
        var_dump($_COOKIE);
        var_dump($s);
        $s->kill_session();
    }
}

class index_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');
        
        $template_values = array(
            'post' => $db->get_index(),
            'pages' => $db->get_pages(),
            'site_info' => $db->get_site()
        );

        $path = 'index.html';
        echo $render->rende($path, $template_values);
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

        $path = 'post.html';
        echo $render->rende($path, $template_values);
    }
}

class posts_handler {
    function GET() {
        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $posts = $db->get_posts();
        rsort($posts);
        $template_values = array(
            'posts' => $posts,
            'pages' => $db->get_pages(),
            'site_info' => $db->get_site()
        );

        $path = 'posts.html';
        echo $render->rende($path, $template_values);
    }
}
?>
