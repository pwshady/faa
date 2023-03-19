<?php

namespace fa\basic\models;

use fa\App;

class PageModel extends Model
{
    public function __construct(public $dir){}

    public function run()
    {
        self::getAccess();
        self::getErrors();
        self::getSettings();
        self::getLabels();
        self::getModules();
        self::getWidgets();
    }


}
