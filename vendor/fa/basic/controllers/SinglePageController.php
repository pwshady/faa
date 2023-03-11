<?php

namespace fa\basic\controllers;

use fa\App;

class SinglePageController extends PageController
{

    public function run()
    {
        self::job();
        echo '<br>' . __CLASS__;
        //self::getView();
    }

    public function getView()
    {
        if ( file_exists(ROOT . $this->dir . 'View.php' )) {
            self::runView();
        } else {
            $view_name = App::$app->getSetting('view') ? App::$app->getSetting('view') : 'index';
            if ( file_exists(ROOT . $this->dir . $view_name . 'View.php') ) {
                echo self::createdView($view_name);
            } else {
                $controller = new PageController('/vendor/fa2/pages', ['samples', 'single']);
                $controller->run();
            }
        }
    }



}