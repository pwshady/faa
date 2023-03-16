<?php

namespace fa\basic\controllers;

use fa\App;

class SinglePageController extends PageController
{

    public function run()
    {
        self::job();
        self::getView();
    }

    public function getView()
    {
        if ( file_exists(ROOT . $this->page_dir . 'View.php' )) {
            self::runView();
        } else {
            $view_name = App::$app->getSetting('view') ? App::$app->getSetting('view') : 'index';
            if ( file_exists(ROOT . $this->page_dir . $view_name . 'View.php') ) {
                echo self::createdView($view_name);
            } else {
                $controller = new PageController('/vendor/fa/pages', array_merge(['samples', 'single'], $this->page_arr));
                $controller->run();
            }
        }
    }



}