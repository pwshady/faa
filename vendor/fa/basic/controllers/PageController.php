<?php

namespace fa\basic\controllers;

use fa\App;

class PageController extends Controller
{

    public object $model;

    public function __construct(public $page_dir, public $page_arr){}

    public function run()
    {
        self::getController();
    }

    public function getController()
    {
        if ($this->page_arr && !$this->page_arr[0]) {
            array_shift($this->page_arr);
        }
        $this->page_dir .= '/';
        $controller_path = str_replace('/', '\\', $this->page_dir) . 'PageController';
        if (class_exists($controller_path) && ($controller_path != '\\' . __CLASS__)) {
            $controller = new $controller_path($this->page_dir, $this->page_arr);
            $controller->run();
        } else {
            self::job();
            if ($this->page_arr) {
                if (is_dir(ROOT . $this->page_dir . $this->page_arr[0]) && ($this->page_arr[0] != '_')) {
                    $this->page_dir .= $this->page_arr[0];
                    array_shift($this->page_arr);
                    $controller_path = 'fa\basic\controllers\PageController';
                    $controller = new $controller_path($this->page_dir, $this->page_arr);
                } else {
                    if (is_dir(ROOT . $this->page_dir . '__')) {
                        $controller_path = str_replace('/', '\\', $this->page_dir) . '__\MultiPageController';
                        if (!(class_exists($controller_path) && ($controller_path != '\\' . __CLASS__))) {
                            $controller_path = 'fa\basic\controllers\MultiPageController';
                        }
                        $controller = new $controller_path($this->page_dir . '__/', $this->page_arr);
                        array_shift($this->page_arr);
                    } else {
                        $controller = new PageController('/vendor/fa/pages', ['errors', '404']);
                    }                  
                }
            } else {
                if (is_dir(ROOT . $this->page_dir . '_')){
                    $controller_path = str_replace('/', '\\', $this->page_dir) . '_\SinglePageController';
                    if (!(class_exists($controller_path) && ($controller_path != '\\' . __CLASS__))) {
                        $controller_path = 'fa\basic\controllers\SinglePageController';
                    }
                    $controller = new $controller_path($this->page_dir . '_/', $this->page_arr);     
                    array_shift($this->page_arr);
                } else {
                    if (is_dir(ROOT . $this->page_dir . '__')) {
                        $controller_path = str_replace('/', '\\', $this->page_dir) . '__\MultiPageController';
                        if (!(class_exists($controller_path) && ($controller_path != '\\' . __CLASS__))) {
                            $controller_path = 'fa\basic\controllers\MultiPageController';
                        }
                        $controller = new $controller_path($this->page_dir . '__/', $this->page_arr);      
                        array_shift($this->page_arr);
                    }  else {
                        $controller = new PageController('/vendor/fa/pages', ['errors', '404']);
                    } 
                }     
            }
            $controller->run();
        }
    }

    public function job()
    {
        self::runModel();
        self::getAccess();
    }

    public function runModel()
    {
        $model_path = str_replace('/', '\\', $this->page_dir) . 'PageModel';
        if (!class_exists($model_path)) {
            $model_path = 'fa\basic\models\PageModel';
        }
        $this->model = new $model_path($this->page_dir);
        $this->model->run();
    }

    public function getAccess()
    {
        $access = App::$app->getAccess();
        $user_roles = [];
        if (array_key_exists('user_roles', $_SESSION)) {
            $user_roles = $_SESSION['user_roles'];
        }
        foreach ($access as $value) {
            if (!in_array($value, $user_roles)) {
                //=============================================
                App::$app->cleanAccess();
                $controller = new PageController('/vendor/fa/pages', ['errors', '500']);
                $controller->run();
                die;
            }
        }
    }

    public function createdView($view_name)
    {
        self::createdModules();
        //self::createdWidgets();
        $view_path = 'fa\basic\views\PageView';
        $view = new $view_path($this->page_dir, $view_name);
        $view->run();
        return self::render($view->render());
    }

    public function render($view)
    {
        debug(App::$app->getWidgets());
        debug(App::$app->getModules());
        debug($this->page_arr);
        $html = '';
        $html .= self::headerCreate();
        $html .= $view . PHP_EOL;
        $html .= self::footerCreate();
        return $html;
    }

    public function headerCreate()
    {
        $header_html = '<!doctype html>' . PHP_EOL;
        $header_html .= '<html lang="' . App::$app->getLanguage()['code'] . '">' . PHP_EOL;
        $header_html .= '<head>' . PHP_EOL;
        $title = App::$app->getLabel(App::$app->getSetting('title')) ?? App::$app->getSetting('title');
        $header_html .= $title ? '<title>' . $title . '</title>' . PHP_EOL : '';
        $charset = App::$app->getSetting('charset');
        $header_html .= $charset ? '<meta charset="' . $charset . '">' . PHP_EOL : '';
        $keywords = App::$app->getLabel(App::$app->getSetting('keywords')) ?? App::$app->getSetting('keywords');
        $header_html .= $keywords ? '<meta name="keywords" content="' . $keywords . '">' . PHP_EOL : '';
        $description = App::$app->getLabel(App::$app->getSetting('description')) ?? App::$app->getSetting('description');
        $header_html .= $description ? '<meta name="description" content="' . $description . '">' . PHP_EOL : '';
        $header_html .= self::createStrings('header_strings');
        $header_html .= self::createStyles();
        $header_html .= self::createScripts('header_scripts');
        $header_html .= '</head>' . PHP_EOL;
        return $header_html;
    }

    public function footerCreate()
    {
        $footer_html = '<footer>' . PHP_EOL;
        $footer_html .= self::createStrings('footer_strings_top');
        $footer_html .= self::createScripts('footer_scripts');
        $footer_html .= self::createStrings('footer_strings_bottom');
        $footer_html .= '</footer>' . PHP_EOL;
        $footer_html .= '</html>' . PHP_EOL;
        return $footer_html;
    }

    public function createStrings($key)
    {
        $html = '';
        if (App::$app->getSetting($key)) {
            foreach (App::$app->getSetting($key) as $string) {
                $html .= $string['string'] ? $string['string'] . PHP_EOL : '';
            }
        }
        return $html;
    }

    public function createStyles()
    {
        $html = '';
        if (App::$app->getSetting('styles')) {
            foreach (App::$app->getSetting('styles') as $string) {
                $type = array_key_exists('type', $string) ? $string['type'] : '';
                switch ($type){
                    case 'css':
                        $html .= self::getCss($string);
                        break;
                }
            }
        }
        return $html;
    }

    public function getCss($string)
    {
        $rel = array_key_exists('rel', $string) ? $string['rel'] : 'stylesheet';
        $media = array_key_exists('media', $string) ? $string['media'] : 'all';
        $href = array_key_exists('href', $string) ? $string['href'] : '';
        return '<link type="text/css" rel="' . $rel . '" media="' . $media . '" href="' . $href . '" />' . PHP_EOL;
    }

    public function createScripts($key)
    {
        $html = '';
        if (App::$app->getSetting($key)) {
            foreach (App::$app->getSetting($key) as $string) {
                $type = array_key_exists('type', $string) ? $string['type'] : '';
                switch ($type){
                    case 'js':
                        $html .= self::getJs($string);
                        break;
                }
            }
        }
        return $html;
    }

    public function getJs($string)
    {
        $href = array_key_exists('href', $string) ? $string['href'] : '';
        return '<script src="' . $href . '"></script>' . PHP_EOL;
    }

    public function getParams($name)
    {
        $params = [];
        foreach ( $_GET as $key => $value ) {
            if ( str_starts_with($key, $name ) ) {
                $key_arr = explode('-', $key, 3);
                $params[$key_arr[2]] = ['value' => $value, 'method' => 'GET'];
            }
        }
        foreach ( $_POST as $key => $value ) {
            if ( str_starts_with($key, $name ) ) {
                $key_arr = explode('-', $key, 3);
                $params[$key_arr[2]] = ['value' => $value, 'method' => 'POST'];
            }
        }
        return $params;
    }

    public function createdModules()
    {
        $modules = App::$app->getModules();
        foreach ( $modules as $modul ) {
            if ( array_key_exists('name', $modul) ) {
                $params = self::getParams('m-' . $modul['name'] . '-');
                self::createdModul( $modul, $params );
            } else {
                echo 'setting error' . PHP_EOL;
            }
        }
    }

    public function createdModul($modul, $params)
    {
        $controller_path = 'app\modules\\' . $modul['name'] . '\Controller';
        if ( !class_exists($controller_path) ) {
            return null;
        }
        if ( !method_exists($controller_path, 'getInstance') ) {
            return null;
        }
        $modul['object'] = $controller_path::getInstance();
        $modul['complete'] = 1;
        if ( method_exists( $controller_path, 'run') ) {
            $modul['complete'] = $modul['object']->run();
        }
        App::$app->updateModul($modul);
    }
}