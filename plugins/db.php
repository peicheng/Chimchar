<?php if (! defined('BASEPATH')) exit ('No direct script access!');
require_once('rb.php');

R::setup(Database);
R::debug(false);


/*
 * How your database looks like?
 *
 * Table site_info:
 *      id: Integer
 *      site_name: String
 *      author: String
 *      site_slogan: String
 *      google_analytic_id: String
 *
 * Table posts:
 *      id: Integer
 *      title: String
 *      url: String
 *      link: String
 *      content: String
 *      formatted_content: String
 *      is_page: Integer
 *      is_isolated: Integer
 *      created_time: String
 *      modified_time: String
 *
 * Table minisite:
 *      id: Integer
 *      title: String
 *      url: String
 *      content: String
 *      formatted_content: String
 *      tpl: String
 *      style: String
 *      created_time: String
 *      modified_time: String
 *
 * Table index:
 *      id: Integer
 *      title: String
 *      url: String
 *      content: String
 *      formatted_content: String
 *      created_time: String
 *      modified_time: String
 * */

// TODO Does redbean support auto generate databae?

class db {
    // reader
    function get_site() {
        return R::findOne('site_setting');
    }

    function get_index() {
        return R::findOne('index');
    }

    function get_post_by_id($id) {
        $r = array(
            'id' => $id
        );
        return R::findOne('posts', 'id=:id', $r);
    }

    function get_minisite_by_id($id) {
        $r = array(
            'id' => $id
        );
        return R::findOne('minisite', 'id=:id', $r);
    }

    function get_post_by_url($url) {
        $r = array(
            'url' => $url
        );
        return R::findOne('posts', 'url=:url', $r);
    }

    function get_minisite_by_url($url) {
        $r = array(
            'url' => $url
        );
        return R::findOne('minisite', 'url=:url', $r);
    }

    function get_posts($limit = 100000) {
        $r = array(
            'order' => 'created_time',
            'limit' => $limit
        );
        return R::find('posts', "1 ORDER BY :order DESC LIMIT :limit", $r);
    }

    function get_minisites($limit = 100000) {
        $r = array(
            'order' => 'created_time',
            'limit' => $limit
        );
    }

    function get_pages($limit = 100000) {
        $r = array(
            'is_page' => 1,
            'limit' => $limit
        );
        return R::find('posts', "is_page = :is_page AND is_isolated = 0 ORDER BY created_time DESC LIMIT :limit", $r);
    }

    function get_isolated($limit = 100000) {
        $r = array(
            'is_isolated' => 1,
            'limit' => $limit
        );
        return R::find('posts', "is_isolated = :is_isolated ORDER BY created_time DESC LIMIT :limit", $r);
    }
    // end of reader

    // writer
    function _get_new($type) {
        return R::dispense($type);
    }

    function set_site($settings) {
        $site = $this->get_site();
        $site->import($s, 'site_name, site_slogan, author, google_analytic_id');
        R::store($site);
    }

    function set_index($index) {
        $post = $this->get_index();
        $post->import($index, 'title, url, content, formatted_content, modified_time, created_time');
    }

    function remove_post($id) {
        $post = $this->get_post_by_id($id);
        R::trash($post);
    }

    function remove_minisite($id) {
        $minisite = $this->get_minisite_by_id($id);
        R::trash($minisite);
    }

    function set_post($p) {
        if (!$p['id']) {
            // new
            $post = R::dispense('posts');
            $post->import($p, 'title, url, link, content, formatted_content, modified_time, created_time, is_page, is_isolated');
        } else {
            // update
            $post = $this->get_post_by_id($p['id']);
            $post->import($p, 'title, url, link, content, formatted_content, modified_time, is_page, is_isolated');
        }
        $id = R::store($post);
        return $id;
    }

    function set_minisite($p) {
        if (!$p['id']) {
            // new
            $post = R::dispense('minisite');
            $post->import($p, 'title, url, content, formatted_content, modified_time, created_time, tpl, style');
        } else {
            // update
            $post = $this->get_post_by_id($p['id']);
            $post->import($p, 'title, url, content, formatted_content, modified_time, tpl, style');
        }
        $id = R::store($post);
        return $id;
    }
    // end of writer
}
?>
