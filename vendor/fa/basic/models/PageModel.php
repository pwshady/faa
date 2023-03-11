<?php

namespace fa\basic\models;

use fa\App;

class PageModel extends Model
{
    public function __construct(public $page_dir){}

    public function run()
    {
        self::getAccess();
        self::getErrors();
        self::getSettings();
        self::getLabels();
        self::getModules();
        self::getWidgets();
        //debug(App::$app->getAccess());
        //debug(App::$app->getErrors());
        //debug(App::$app->getSettings());
        //debug(App::$app->getLabels());
        debug(App::$app->getModules());
    }


}
