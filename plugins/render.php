<?php if (! defined('BASEPATH')) exit ('No direct script access!');
require_once 'Twig/Autoloader.php';
Twig_Autoloader::register();

class render {
    function __construct() {
        $this->loader = new Twig_Loader_Filesystem(TPL);
        $this->twig = new Twig_Environment($this->loader);
    }
    function rende($template_name, $path, $template_values = '') {
        $this->loader = new Twig_Loader_Filesystem(TPL.$path.'/');
        $this->twig = new Twig_Environment($this->loader);
        $template = $this->twig->loadTemplate($template_name);
        return $template->render($template_values);
    }
}
?>
