<?php if (! defined('BASEPATH')) exit ('No direct script access');
require_once('rb.php');

R::setup(DB);
R::debug(false);

class db {

    /* db reader */
    function get_site() {
        return R::findOne('site_setting');
    }

    function get_index() {
        $r = array(
            'is_index' => 1
        );
        return R::findOne('posts', "is_index = :is_index", $r);
    }

    function get_post_by_url($url) {
        $r = array(
            'url' => $url
        );
        return R::findOne('posts', "url = :url", $r);
    }

    function get_post_by_id($id) {
        $r = array(
            'id' => $id
        );
        return R::findOne('posts', "id = :id", $r);
    }

    function get_all($limit = 10000) {
        $r = array(
            'sort_order' => 'created_time',
            'limit' => $limit
        );
        return R::find('posts', "1 ORDER BY :sort_order DESC LIMIT :limit", $r);
    }

    function get_posts($limit = 10000) {
        $r = array(
            'is_page' => 0,
            'is_isolated' => 0,
            'is_index' => 0,
            'sort_order' => 'created_time',
            'limit' => $limit
        );
        return R::find('posts', "is_page = :is_page AND is_isolated = :is_isolated AND is_index = :is_index ORDER BY :sort_order DESC LIMIT :limit", $r);
    }

    function get_pages($limit = 10000) {
        $r = array(
            'is_page' => 1,
            'is_isolated' => 0,
            'is_index' => 0,
            'limit' => $limit
        );
        return R::find('posts', "is_page = :is_page AND is_isolated = :is_isolated AND is_index = :is_index ORDER BY :sort_order DESC LIMIT :limit", $r);
    }

    function get_isolated($limit = 10000) {
        $r = array(
            'is_isolated' => 1,
            'is_index' => 0,
            'limit' => $limit
        );
        return R::find('posts', "is_isolated = :is_isolated AND is_index = :is_index ORDER BY :sort_order DESC LIMIT :limit", $r);
    }

    /* db writer */
    function get_new($type) {
        return R::dispense($type);
    }

    function set_site($s) {
        $site = $this->get_site();
        $site->import($s, 'id, site_name, site_slogan, author, google_analytic_id');
        R::store($site);
    }

    function remove_post($id) {
        $post = $this->get_post_by_id($id);
        R::trash($post);
    }

    function set_post($p) {
        if (!empty($p['id']) || !isset($p['id'])) {
            $post = R::dispense('posts');
        } else {
            $post = $this->get_post_by_id($p['id']);
        }
        $post->import($p, 'title, url, link, content, formatted_content, modified_time, created_time, modified_time, is_page, is_isolated, is_index');
        $id = R::store($post);
        return $id;
    }

    function set_index($p) {
        $post = $this->get_index();
        $post->import($p, 'title, url, link, content, formatted_content, modified_time, created_time, modified_time, is_page, is_isolated, is_index');
        $id = R::store($post);
        return $id;
    }
}

?>
