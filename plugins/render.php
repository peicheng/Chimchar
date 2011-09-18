<?php if (! defined('BASEPATH')) exit ('No direct script access');
require_once 'Twig/Autoloader.php';
Twig_Autoloader::register();

class render {
    function __construct() {
        $this->loader = new Twig_Loader_Filesystem(TPL);
        $this->twig = new Twig_Environment($this->loader);
    }
    function rende($tpl, $tpl_values = '') {
        $template = $this->twig->loadTemplate($tpl);
        return $template->render($tpl_values);
    }
}
?>
