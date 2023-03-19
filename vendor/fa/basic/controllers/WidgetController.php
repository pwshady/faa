<?php

namespace fa\basic\controllers;

use fa\App;
use fa\Cache;

class WidgetController extends Controller
{
    protected string $prefix_kebab = '';
    protected string $prefix_snake = '';
    protected string $widget_dir = '';
    protected object $model;


    public function __construct(public $page_dir, protected $widget_name, public $params = []){}

    public function run()
    {        
        self::init();
        $model_path = 'app\widgets\\' . $this->widget_name . '\Model';
        if (!class_exists($model_path)) {
            $model_path = 'fa\basic\models\WidgetModel';
        }
        $this->model = new $model_path($this->widget_dir);
        $this->model->run();
    }

    public function render()
    {
        ///Недоделано
        $view_path = WIDGET . '/' . $this->widget_name . '/indexView.php';
        $widget = App::$app->getWidget($this->widget_name);
        if (is_file($view_path)) {
            ob_start();
            require_once $view_path;
            $widget['code'] = ob_get_clean();
        } else {
            $widget['code'] = 'no code';
        }
        $widget['complete'] = 1;
        App::$app->updateWidget($widget);
        debug($widget);
    }

    protected function init()
    {
        $this->widget_dir = WIDGET . '/'. $this->widget_name;
        $this->prefix_kebab = 'w-' . $this->widget_name . '-';
        $this->prefix_snake = 'w_' . $this->widget_name . '_';
    }

}
