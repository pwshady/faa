<?php

namespace fa\basic\controllers;

use fa\App;

class MultiPageController extends PageController
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
                self::createdView($view_name);
            } else {
                if (App::$app->getError('1002') !== '') {                        
                    $controller = new PageController('/app/pages', explode('/', App::$app->getError('1002') . '/' . App::$app->getRequest()), []);
                } else {
                    $controller = new PageController('/vendor/fa/pages', array_merge(['samples', 'multi'], explode('/', App::$app->getRequest())), []);
                }
                $controller->run();
            }
        }
    }
}