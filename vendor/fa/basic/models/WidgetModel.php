<?php

namespace fa\basic\models;

use fa\App;

class WidgetModel extends Model
{
    protected array $config = [];

    public function __construct(public $dir){}

    public function run()
    {
        self::getSettings();
    }

    public function getLabels()
    {
        if (file_exists($this->dir . '/labels.json')) {            
            $labels = json_decode(file_get_contents($this->dir . '/labels.json'), true);
            if (is_array($labels)) {
                $language = App::$app->getLanguage()['code'];
                if (array_key_exists($language, $labels)) {
                    return $labels[$language];
                } else {
                    foreach ( $labels as $key => $value) {
                        return $value;
                    }
                }
            }
        }
    }
}