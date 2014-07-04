<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Driver\View;

use Hiano\Hiano;

class SmartyDriver implements \Hiano\View\ViewDriverInterface {

    /**
     *
     * @var \Hiano\Request\Request
     */
    protected $request;
    protected $tpl_dirs = [];

    function __construct() {
        $this->request = \Hiano\App\App::getRequest();
    }

    private function getSmarty() {
        \Smarty::$_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';
        $smarty = new \Smarty();
        $smarty->left_delimiter = '{{';
        $smarty->right_delimiter = '}}';
        $smarty->default_modifiers = array('escape:"html"');
        $smarty->compile_dir = HIANO_APP_PATH . '/Cache/smarty/templates_c/';
        $smarty->cache_dir = HIANO_APP_PATH . '/Cache/smarty/cache/';

        $smarty->registerPlugin('function', 'url', array($this, 'function_url'));
        $smarty->registerPlugin('function', 'link', array($this, 'function_link'));
        //$smarty->registerPlugin('block', 'form', array($this, 'block_form'));

        $plugins_dir = HIANO_APP_PATH . '/Plugin/Smarty';
        if (file_exists($plugins_dir)) {
            $smarty->addPluginsDir($plugins_dir);
        }
        return $smarty;
    }

    function function_url($params) {
        if (isset($params['uri'])) {
            $inner_url = new \Hiano\Route\InnerUrl(
                    $this->request->getParameter('module')
                    , $this->request->getParameter('controller')
                    , $this->request->getParameter('action')
            );
            $arr = $inner_url->parse($params['uri']);
        } else {
            $arr = $this->request->getParameter();
        }
        if (isset($params['append'])) {
            $arr = array_merge($arr, \Hiano\Url\StandardUrl::query2array($params['append']));
        }
        $url = \Hiano\App\App::getRouter()->format($arr);
        if ($params['return']) {
            $u = new \Hiano\Route\StandardUrl($url);
            $u->setQuery('return', \Hiano\App\App::getUrl());
            $url = $u->build();
        }
        return $url;
    }

    static function function_link($params) {
        if (isset($params['path'])) {
            $path = $params['path'];
            if (substr($path, -1) != '/') {
                $path.='/';
            }
        }
        return \Hiano\App\App::getBaseUrl() . $path . $params['uri'];
    }

    /*
      static function block_form($params, $content, Smarty_Internal_Template $template, &$repeat) {
      if (!$repeat) {
      $ret = '<form';
      if ($params) {
      foreach ($params as $key => $value) {
      $ret .= ' ' . $key . '="' . $value . '"';
      }
      }
      $ret .= ">\n" . $content . '<input type="hidden" name="__Token" value="' . spToken::generate('hiano_form') . '" /></form>';
      return $ret;
      }
      }
     * 
     */

    public function setTemplateDirs($dirs) {
        $this->tpl_dirs = $dirs;
    }

    function render($template_file, $vars) {
        $smarty = $this->getSmarty();
        $smarty->setTemplateDir($this->tpl_dirs);
        $smarty->assign($vars);
        return $smarty->fetch($template_file);
    }

}
