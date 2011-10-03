<?php if (!defined('BASEPATH')) exit('No direct sciprt access!');

class auth {
    function __construct() {
        include('secret'.EXT);
        $this->secret = $secret;
        
        $param = array(
            'key' => 'chimchar',
            'sess_name' => 'chimchar',
            'lifetime' => '3600',
        );
        $this->session = Charizard::load('sessioner', $param);
        $this->status = Charizard::load('status_coder');
    }

    function check($key) {
        if (sha1($key) === $this->secret) {
            return true;
        } else {
            return false;
        }
    }

    function set() {
        $this->session->userdata['is_pass'] = true;
        $this->session->sess_write();
    }

    function kill() {
        $this->session->sess_destory();
        $this->session = array();
    }

    function do_auth($key) {
        if ($this->is_pass()) {
            return true;
        }

        if ($this->check($key)) {
            $this->set();
            return true;
        }

        $this->kill();
        return false;
    }

    function is_pass() {
        return $this->session->userdata['is_pass'];
    }

    function back_auth() {
        $u = Charizard::load('url_helper');
        $this->status->redirect($u->build('/writer/login'));
    }
}

/*
 * TODO
 * Need a postman?
 *
 * message => {
 *      'error': String,
 *      'warning': String,
 *      'success': String
 * }
 *
 * 
 * */
$message = array(
    'error' => false,
    'warning' => false,
    'success' => false
);

class login extends auth {
    function GET() {
        if ($this->is_pass()) {
            $u = Charizard::load('url_helper');
            $this->status->redirect($u->build('/writer/overview'));
        }

        global $message;
        $render = Charizard::load('render');
        $template_values = array(
            'title' => 'login',
            'message' => $message,
            'u' => Charizard::load('url_helper')
        );

        $path = 'writer';
        echo $render->rende('auth.html', $path, $template_values);
    }

    function POST() {
        if ($this->do_auth($_POST['passwd'])) {
            $u = Charizard::load('url_helper');
            $this->status->seeother($u->build('/writer/overview'));
        } else {
            global $message;
            $message = array(
                'error' => 'Secret parse wrong!',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
        }
    }
}

class logout extends auth {
    public $message = false;

    function GET() {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
        } else {
            $this->kill();
            $message = array(
                'error' => false,
                'warning' => false,
                'success' => 'Bye my friend =)'
            );
            $this->back_auth();
        }
    }
}

class remove_post_handler extends auth {
    function GET($id) {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $post = $db->get_post_by_id($id);
        if ($post) {
            $db->remove_post($id);
            $message = array(
                'success' => "post $id remove.",
                'warning' => false,
                'error' => false
            );
        } else {
            $message = array(
                'success' => false,
                'warning' => "post $id not found!",
                'error' => false
            );
        }
        $u = Charizard::load('url_helper');
        $this->status->redirect($u->build('/writer/overview'));
    }

    function POST($id) {
        $this->GET($id);
    }
}

class remove_minisite_handler extends auth {
    function GET($id) {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $post = $db->get_minisite_by_id($id);
        if ($post) {
            $db->remove_minisite($id);
            $message = array(
                'success' => "minisite $id remove.",
                'warning' => false,
                'error' => false
            );
        } else {
            $message = array(
                'success' => false,
                'warning' => "minisite $id not found!",
                'error' => false
            );
        }
        $u = Charizard::load('url_helper');
        $this->status->redirect($u->build('/writer/overview'));
    }

    function POST($id) {
        $this->GET($id);
    }
}

class overview_handler extends auth {
    function GET() {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
            'posts' => $db->get_posts(),
            'minisites' => $db->get_minisites()
        );

        $path = 'writer';
        echo $render->rende('overview.html', $path, $template_values);
    }
}

class write_post_handler extends auth {
    function GET($id = false) {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
        );

        if ($id) {
            $template_values['mode'] = 'edit';
            $template_values['post'] = $db->get_post_by_id($id);
        } else {
            $template_values['mode'] = 'new';
            $template_values['post'] = $db->get_new('posts');
        }

        $path = 'writer';
        echo $render->rende('write_post.html', $path, $template_values);
    }
}

class write_minisite_handler extends auth {
    function GET($id = false) {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
        );

        if ($id) {
            $template_values['mode'] = 'edit';
            $template_values['post'] = $db->get_minisite_by_id($id);
        } else {
            $template_values['mode'] = 'new';
            $template_values['post'] = $db->get_new('minisite');
        }

        $path = 'writer';
        echo $render->rende('write_minisite.html', $path, $template_values);
    }
}
class write_index_handler extends auth {
    function GET() {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $template_values = array(
            'site_info' => $db->get_site(),
            'mode' => 'edit',
            'post' => $db->get_index(),
            'u' => Charizard::load('url_helper'),
        );

        $path = 'writer';
        echo $render->rende('write_index.html', $path, $template_values);
    }
}

class update_index_handler extends auth {
    function POST() {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');
        $u = Charizard::load('url_helper');
        $mkd = Charizard::load('mkd_render');

        $p = $_POST;
        $p['formatted_content'] = $mkd->rende($p['content']);
        $db->set_index($p);

        global $message;
        $message = array(
            'success' => 'Your post saved',
            'warning' => false,
            'error' => false
        );

        $status_coder->_301($u->build('/writer/overview'));
    }
}

class update_post_handler extends auth {
    function POST($id = false) {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');
        $u = Charizard::load('url_helper');
        $mkd = Charizard::load('mkd_render');

        $p = $_POST;
        $p['id'] = $id;
        $p['formatted_content'] = $mkd->rende($p['content']);
        $p['modified_time'] = date(DATE_FORMAT);
        if (!$id) {
            $p['created_time'] = date(DATE_FORMAT);
        }
        $db->set_post($p);

        global $message;
        $message = array(
            'success' => 'Your post saved',
            'warning' => false,
            'error' => false
        );

        $status_coder->_301($u->build('/writer/overview'));
    }
}

class update_minisite_handler extends auth {
    function POST($id = false) {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');
        $u = Charizard::load('url_helper');
        $mkd = Charizard::load('mkd_render');

        $p = $_POST;
        $p['id'] = $id;
        $p['formatted_content'] = $mkd->rende($p['content']);
        $p['modified_time'] = date(DATE_FORMAT);
        if (!$id) {
            $p['created_time'] = date(DATE_FORMAT);
        }
        $db->set_minisite($p);

        global $message;
        $message = array(
            'success' => 'Your minisite saved',
            'warning' => false,
            'error' => false
        );

        $status_coder->_301($u->build('/writer/overview'));
    }
}

class settings_handler extends auth {
    function GET() {
        global $message;
        if (!$this->is_pass()) {
            $message = array(
                'error' => 'login first',
                'warning' => false,
                'success' => false
            );
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');
        
        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
        );

        $path = 'writer';
        echo $render->rende('settings.html', $path, $template_values);
    }

    function POST() {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');
        $u = Charizard::load('url_helper');
    
        $s = $_POST;
        $db->set_site($s);

        global $message;
        $message = array(
            'success' => 'Your settings saved',
            'warning' => false,
            'error' => false
        );

        $status_coder->_301($u->build('/writer/overview'));
    }
}
?>
