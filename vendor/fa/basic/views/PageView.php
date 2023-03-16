<?php

namespace fa\basic\views;

use fa\App;

class PageView extends View
{
    public function __construct(public $dir, public $name){}

    public function run()
    {
        
    }

    public function render()
    {
        $labels = App::$app->getLabels();
        $widgets = App::$app->getWidgets();
        $modules = App::$app->getModules();
        foreach ( $modules as $modul ) {
            if ( array_key_exists('name', $modul) && array_key_exists('object', $modul) ) {
                $name = 'm_' . $modul['name'];
                $$name = $modul['object'];
            }
        }
        foreach ( $widgets as $widget) {
            if ( array_key_exists('name', $widget) && array_key_exists('code', $widget) ) {
                $name = 'w_' . $widget['name'];
                $$name = $widget['code'];
            }
        }
        ob_start();
        require_once ROOT . $this->dir . $this->name . 'View.php';
        return ob_get_clean();
    }

}