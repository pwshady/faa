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
                        $controller = new $controller_path($this->page_dir . '__/', []);
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
                    $controller = new $controller_path($this->page_dir . '_/', []);
                    array_shift($this->page_arr);
                } else {
                    if (is_dir(ROOT . $this->page_dir . '__')) {
                        $controller_path = str_replace('/', '\\', $this->page_dir) . '__\MultiPageController';
                        if (class_exists($controller_path) && ($controller_path != '\\' . __CLASS__)) {
                            $controller_path = 'fa\basic\controllers\MultiPageController';
                        }
                        $controller = new $controller_path($this->page_dir . '__/', []);
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
        echo '<br>' . __DIR__;
        echo '<br>' . __CLASS__;
        self::runModel();
        //self::getAccess();
    }

    public function runModel()
    {
        $model_path = str_replace('/', '\\', $this->page_dir) . 'MyPageModel';
        if (!class_exists($model_path)) {
            $model_path = 'fa\basic\models\PageModel';
        }
        $this->model = new $model_path($this->page_dir);
        $this->model->run();
    }
}