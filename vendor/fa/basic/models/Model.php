<?php

namespace fa\basic\models;

use fa\App;

class Model
{
    public function __construct(public $dir){}

    public function run(){}

    public function getAccess()
    {
        if (file_exists(ROOT . $this->dir . 'access.json')) {       
            $access = json_decode(file_get_contents(ROOT . $this->dir . 'access.json'), true);
            if ($access != '') {
                App::$app->addAccess($access);
            }
        }
    }

    public function getErrors()
    {
        if (file_exists(ROOT . $this->dir . 'errors.json')) {
            $errors = json_decode(file_get_contents(ROOT . $this->dir . 'errors.json'), true);
            if (is_array($errors)) {
                foreach ($errors as $key => $value) {
                    App::$app->setError($key, $value);
                }
            }
        }
    }

    public function getSettings()
    {
        if (file_exists(ROOT . $this->dir . 'settings.json')) {           
            $settings = json_decode(file_get_contents(ROOT . $this->dir . 'settings.json'), true);
            if (is_array($settings)) {
                foreach ($settings as $key => $value) {
                    foreach ($value as $key => $value) {
                        App::$app->setSetting($key, $value);
                    }
                }
            }
        }
    }

    public function getLabels()
    {
        if (file_exists(ROOT . $this->dir . 'labels.json')) {            
            $labels = json_decode(file_get_contents(ROOT . $this->dir . 'labels.json'), true);
            if (is_array($labels)) {
                $language = App::$app->getLanguage()['code'];
                if (array_key_exists($language, $labels)) {
                    $labels = $labels[$language];
                    foreach ($labels as $key => $value) {
                        App::$app->setLabel($key, $value);
                    }
                }
            }
        }
    }

    public function getModules()
    {
        if (file_exists(ROOT . $this->dir . 'modules.json')) {
            $modules = json_decode(file_get_contents(ROOT . $this->dir . 'modules.json'), true);
            if (is_array($modules)) {
                foreach ($modules as $key => $value) {
                    App::$app->setModul($key, $value);
                }
            }            
        }
    }

    public function getWidgets()
    {
        if (file_exists(ROOT . $this->dir . 'widgets.json')) {
            $widgets = json_decode(file_get_contents(ROOT . $this->dir . 'widgets.json'), true);
            if (is_array($widgets)) {
                foreach ($widgets as $key => $value) {
                    App::$app->setWidget($key, $value);
                }
            }            
        }
    }

}
