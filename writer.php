<?php if (!defined('BASEPATH')) exit('No direct access allowed');

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

class login extends auth {
    // TODO message
    public $message = false;

    function GET() {
        if ($this->is_pass()) {
            $u = Charizard::load('url_helper');
            $this->status->redirect($u->build('/writer/overview'));
        }

        $render = Charizard::load('render');
        $template_values = array(
            'title' => 'login',
            'message' => $this->message,
        );

        $path = 'writer/login.html';
        echo $render->rende($path, $template_values);
    }

    function POST() {
        if ($this->do_auth($_POST['passwd'])) {
            $u = Charizard::load('url_helper');
            $this->status->seeother($u->build('/writer/overview'));
        } else {
            $this->back_auth();
        }
    }
}

class logout extends auth {
    public $message = false;

    function GET() {
        if (!$this->is_pass()) {
            $this->back_auth();
        } else {
            $this->kill();
            $this->back_auth();
        }
    }
}

class remove_handler extends auth {
    function GET($id) {
        if (!$this->is_pass()) {
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $post = $db->get_post_by_id($id);
        if ($post) {
            $db->remove_post($id);
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
        if (!$this->is_pass()) {
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');

        $posts = $db->get_all();
        rsort($posts);
        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
            'posts' => $posts
        );

        $path = 'writer/overview.html';
        echo $render->rende($path, $template_values);
    }
}

class write_handler extends auth {
    function GET($id = false) {
        if (!$this->is_pass()) {
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
            $template_values['post'] = $db->get_new();
        }

        $path = 'writer/write.html';
        echo $render->rende($path, $template_values);
    }
}

class index_write_handler extends auth {
    function GET() {
        if (!$this->is_pass()) {
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

        $path = 'writer/index_write.html';
        echo $render->rende($path, $template_values);
    }
}

class index_update_handler extends auth {
    function POST() {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');

        $p = $_POST;
        $p['formatted_content'] = Markdown($p['content']);
        $db->set_index($p);

        $u = Charizard::load('url_helper');
        $status_coder->_301($u->build('/writer/overview'));
    }
}

class edit_handler extends auth {
    function GET($id = false) {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');

        $p = $_POST;
        $p['formatted_content'] = Markdown($p['content']);
        $p['id'] = $id;
        $p['modified_time'] = date(DATE_FORMAT);
        if (!$id) {
            $p['created_time'] = date(DATE_FORMAT);
        }
        $db->set_post($p);

        $u = Charizard::load('url_helper');
        $status_coder->_301($u->build('/writer/overview'));
    }

    function POST($id = false) {
        $this->GET($id);
    }
}

class settings_handler extends auth {
    function GET() {
        if (!$this->is_pass()) {
            $this->back_auth();
            return;
        }

        $db = Charizard::load('db');
        $render = Charizard::load('render');
        
        $template_values = array(
            'site_info' => $db->get_site(),
            'u' => Charizard::load('url_helper'),
        );

        $path = 'writer/settings.html';
        echo $render->rende($path, $template_values);
    }

    function POST() {
        $db = Charizard::load('db');
        $status_coder = Charizard::load('status_coder');
    
        $s = $_POST;
        $db->set_site($s);

        $u = Charizard::load('url_helper');
        $status_coder->_301($u->build('/writer/overview'));
    }
}
?>
